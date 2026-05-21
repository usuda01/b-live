<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

class MediaController extends Controller
{
    private const MAX_BYTES = 1610612736; // 1.5 GiB
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'heic', 'mov', 'mp4', 'm4v'];
    private const VIDEO_EXTENSIONS = ['mov', 'mp4', 'm4v'];
    private const THUMBNAIL_SIZE = 200;

    public function index(Request $request): JsonResponse
    {
        $this->ensureManager($request);

        $items = MediaFile::orderBy('created_at', 'desc')
            ->get(['user_id', 'filename', 'size', 'is_video', 'has_thumbnail', 'taken_at', 'latitude', 'longitude', 'created_at'])
            ->map(function ($row) {
                return [
                    'user_id'       => (int) $row->user_id,
                    'filename'      => $row->filename,
                    'key'           => $row->user_id . '/' . $row->filename,
                    'size'          => (int) $row->size,
                    'is_video'      => (bool) $row->is_video,
                    'has_thumbnail' => (bool) $row->has_thumbnail,
                    'taken_at'      => optional($row->taken_at)->toIso8601String(),
                    'latitude'      => $row->latitude !== null ? (float) $row->latitude : null,
                    'longitude'     => $row->longitude !== null ? (float) $row->longitude : null,
                    'last_modified' => optional($row->created_at)->toIso8601String(),
                ];
            });

        return response()->json(['items' => $items]);
    }

    public function show(Request $request, $userId, $filename): Response
    {
        $this->ensureManager($request);

        $filePath = $this->resolveFilePath($userId, $filename);
        if ($filePath === null) {
            abort(404);
        }

        return response()->file($filePath);
    }

    public function showThumbnail(Request $request, $userId, $filename): Response
    {
        $this->ensureManager($request);

        if (!ctype_digit((string) $userId)) {
            abort(404);
        }
        $safeName = basename((string) $filename);
        if ($safeName === '' || $safeName === '.' || $safeName === '..') {
            abort(404);
        }
        if (strpos($safeName, '/') !== false || strpos($safeName, '\\') !== false) {
            abort(404);
        }

        $thumbPath = $this->thumbnailPath((int) $userId, $safeName);
        if (!is_file($thumbPath)) {
            abort(404);
        }

        return response()->file($thumbPath);
    }

    public function destroy(Request $request, $userId, $filename): JsonResponse
    {
        $this->ensureManager($request);

        $filePath = $this->resolveFilePath($userId, $filename);
        if ($filePath === null) {
            MediaFile::where('user_id', $userId)->where('filename', $filename)->delete();
            return response()->json(['status' => 'not_found'], 404);
        }

        if (!@unlink($filePath)) {
            return response()->json(['message' => 'delete failed'], 500);
        }

        $thumbPath = $this->thumbnailPath((int) $userId, $filename);
        if (is_file($thumbPath)) {
            @unlink($thumbPath);
        }

        MediaFile::where('user_id', $userId)->where('filename', $filename)->delete();

        return response()->json(['status' => 'deleted']);
    }

    public function upload(Request $request): JsonResponse
    {
        set_time_limit(600);

        $user = $request->user();
        $key  = $request->header('X-Media-Key');

        if (!is_string($key) || $key === '') {
            return response()->json(['message' => 'X-Media-Key required'], 422);
        }

        $filename = basename($key);
        if ($filename === '' || $filename === '.' || $filename === '..') {
            return response()->json(['message' => 'invalid key'], 422);
        }
        if (strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return response()->json(['message' => 'invalid key'], 422);
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            return response()->json(['message' => 'unsupported extension'], 422);
        }

        $contentLength = (int) $request->header('Content-Length');
        if ($contentLength > self::MAX_BYTES) {
            return response()->json(['message' => 'file too large'], 413);
        }

        $relativePath = 'media/' . $user->id . '/' . $filename;
        $absoluteDir  = storage_path('app/media/' . $user->id);
        $absolutePath = $absoluteDir . '/' . $filename;

        if (is_file($absolutePath)) {
            $this->upsertMediaFileRow((int) $user->id, $filename, $absolutePath);
            return response()->json([
                'status' => 'already_exists',
                'path'   => $relativePath,
            ]);
        }

        if (!is_dir($absoluteDir) && !@mkdir($absoluteDir, 0755, true) && !is_dir($absoluteDir)) {
            return response()->json(['message' => 'cannot create directory'], 500);
        }

        $input = @fopen('php://input', 'rb');
        if ($input === false) {
            return response()->json(['message' => 'cannot read input'], 500);
        }

        $tempPath = $absolutePath . '.uploading-' . bin2hex(random_bytes(4));
        $out = @fopen($tempPath, 'wb');
        if ($out === false) {
            fclose($input);
            return response()->json(['message' => 'cannot open output'], 500);
        }

        $totalBytes = 0;
        while (!feof($input)) {
            $chunk = fread($input, 65536);
            if ($chunk === false) {
                fclose($input);
                fclose($out);
                @unlink($tempPath);
                return response()->json(['message' => 'read error'], 500);
            }
            if ($chunk === '') {
                break;
            }
            $totalBytes += strlen($chunk);
            if ($totalBytes > self::MAX_BYTES) {
                fclose($input);
                fclose($out);
                @unlink($tempPath);
                return response()->json(['message' => 'file too large'], 413);
            }
            if (fwrite($out, $chunk) === false) {
                fclose($input);
                fclose($out);
                @unlink($tempPath);
                return response()->json(['message' => 'write error'], 500);
            }
        }
        fclose($input);
        fclose($out);

        if (!rename($tempPath, $absolutePath)) {
            @unlink($tempPath);
            return response()->json(['message' => 'finalize error'], 500);
        }

        $this->upsertMediaFileRow((int) $user->id, $filename, $absolutePath);

        return response()->json([
            'status' => 'stored',
            'path'   => $relativePath,
            'bytes'  => $totalBytes,
        ]);
    }

    private function upsertMediaFileRow(int $userId, string $filename, string $absolutePath): void
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $isVideo = in_array($ext, self::VIDEO_EXTENSIONS, true);
        $result = $this->processMedia($userId, $filename, $absolutePath);

        MediaFile::updateOrCreate(
            ['user_id' => $userId, 'filename' => $filename],
            [
                'size'          => filesize($absolutePath) ?: 0,
                'is_video'      => $isVideo,
                'has_thumbnail' => $result['thumbnail_ok'],
                'taken_at'      => $result['taken_at'],
                'latitude'      => $result['latitude'],
                'longitude'     => $result['longitude'],
            ]
        );
    }

    /**
     * メディアファイルからサムネイル生成と EXIF メタデータ抽出を実施する。
     * @return array{thumbnail_ok: bool, taken_at: ?string, latitude: ?float, longitude: ?float}
     */
    public function processMedia(int $userId, string $filename, string $sourcePath): array
    {
        $thumbPath = $this->thumbnailPath($userId, $filename);
        $thumbDir = dirname($thumbPath);
        if (!is_dir($thumbDir) && !@mkdir($thumbDir, 0755, true) && !is_dir($thumbDir)) {
            return ['thumbnail_ok' => false, 'taken_at' => null, 'latitude' => null, 'longitude' => null];
        }

        if ($this->isHeicContent($sourcePath)) {
            return $this->processHeic($sourcePath, $thumbPath);
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $isVideo = in_array($ext, self::VIDEO_EXTENSIONS, true);

        $thumbOk = $this->runFfmpegThumbnail($sourcePath, $thumbPath);
        $meta = $isVideo
            ? $this->extractVideoMetadata($sourcePath)
            : $this->extractJpegMetadata($sourcePath);

        return array_merge(['thumbnail_ok' => $thumbOk], $meta);
    }

    /**
     * メタデータのみ抽出する（既存ファイルの backfill 用）。サムネイルファイルは作らない。
     * @return array{taken_at: ?string, latitude: ?float, longitude: ?float}
     */
    public function extractMetadata(string $sourcePath, bool $isVideo): array
    {
        if ($this->isHeicContent($sourcePath)) {
            // HEIC は Python スクリプトで取るのが確実。サムネ出力は一時ファイルへ。
            $tmpThumb = tempnam(sys_get_temp_dir(), 'heicmeta_') . '.jpg';
            $result = $this->processHeic($sourcePath, $tmpThumb);
            if (is_file($tmpThumb)) {
                @unlink($tmpThumb);
            }
            unset($result['thumbnail_ok']);
            return $result;
        }
        return $isVideo
            ? $this->extractVideoMetadata($sourcePath)
            : $this->extractJpegMetadata($sourcePath);
    }

    private function runFfmpegThumbnail(string $sourcePath, string $thumbPath): bool
    {
        $size = self::THUMBNAIL_SIZE;
        $process = new Process([
            'ffmpeg', '-y',
            '-ss', '0',
            '-i', $sourcePath,
            '-vf', "scale={$size}:{$size}:force_original_aspect_ratio=decrease",
            '-frames:v', '1',
            $thumbPath,
        ]);
        $process->setTimeout(60);

        try {
            $process->run();
        } catch (\Throwable $e) {
            return false;
        }

        return $process->isSuccessful() && is_file($thumbPath);
    }

    private function isHeicContent(string $path): bool
    {
        $f = @fopen($path, 'rb');
        if ($f === false) {
            return false;
        }
        $header = fread($f, 12);
        fclose($f);
        if ($header === false || strlen($header) < 12) {
            return false;
        }
        if (substr($header, 4, 4) !== 'ftyp') {
            return false;
        }
        $brand = substr($header, 8, 4);
        return in_array($brand, ['heic', 'heix', 'heim', 'mif1'], true);
    }

    private function processHeic(string $sourcePath, string $thumbPath): array
    {
        $script = base_path('scripts/heic_to_thumb.py');
        $pythonBin = config('services.heic_python_bin', 'python3');
        $process = new Process([
            $pythonBin, $script, $sourcePath, $thumbPath, (string) self::THUMBNAIL_SIZE,
        ]);
        $process->setTimeout(60);

        try {
            $process->run();
        } catch (\Throwable $e) {
            return ['thumbnail_ok' => false, 'taken_at' => null, 'latitude' => null, 'longitude' => null];
        }

        $thumbOk = $process->isSuccessful() && is_file($thumbPath);
        $meta = ['taken_at' => null, 'latitude' => null, 'longitude' => null];

        if ($thumbOk) {
            $json = json_decode(trim($process->getOutput()), true);
            if (is_array($json)) {
                $meta['taken_at'] = $this->normalizeExifDateTime($json['taken_at'] ?? null);
                $meta['latitude'] = isset($json['latitude']) && is_numeric($json['latitude']) ? (float) $json['latitude'] : null;
                $meta['longitude'] = isset($json['longitude']) && is_numeric($json['longitude']) ? (float) $json['longitude'] : null;
            }
        }

        return array_merge(['thumbnail_ok' => $thumbOk], $meta);
    }

    private function extractJpegMetadata(string $sourcePath): array
    {
        $result = ['taken_at' => null, 'latitude' => null, 'longitude' => null];
        if (!function_exists('exif_read_data')) {
            return $result;
        }
        $exif = @exif_read_data($sourcePath);
        if (!is_array($exif)) {
            return $result;
        }
        $datetime = $exif['DateTimeOriginal'] ?? $exif['DateTime'] ?? null;
        $result['taken_at'] = $this->normalizeExifDateTime($datetime);
        $result['latitude'] = $this->parseExifGps($exif['GPSLatitude'] ?? null, $exif['GPSLatitudeRef'] ?? null);
        $result['longitude'] = $this->parseExifGps($exif['GPSLongitude'] ?? null, $exif['GPSLongitudeRef'] ?? null);
        return $result;
    }

    private function extractVideoMetadata(string $sourcePath): array
    {
        $result = ['taken_at' => null, 'latitude' => null, 'longitude' => null];
        $process = new Process([
            'ffprobe', '-v', 'quiet',
            '-show_entries', 'format_tags=creation_time,com.apple.quicktime.location.ISO6709',
            '-of', 'default=noprint_wrappers=1',
            $sourcePath,
        ]);
        $process->setTimeout(30);
        try {
            $process->run();
        } catch (\Throwable $e) {
            return $result;
        }
        if (!$process->isSuccessful()) {
            return $result;
        }
        foreach (explode("\n", $process->getOutput()) as $line) {
            $line = trim($line);
            if (strpos($line, 'TAG:creation_time=') === 0) {
                $raw = substr($line, strlen('TAG:creation_time='));
                try {
                    $dt = new \DateTime($raw);
                    $result['taken_at'] = $dt->format('Y-m-d H:i:s');
                } catch (\Throwable $e) {
                    // ignore
                }
            } elseif (strpos($line, 'TAG:com.apple.quicktime.location.ISO6709=') === 0) {
                $iso = substr($line, strlen('TAG:com.apple.quicktime.location.ISO6709='));
                if (preg_match('/^([+-]\d+(?:\.\d+)?)([+-]\d+(?:\.\d+)?)/', $iso, $m)) {
                    $result['latitude'] = (float) $m[1];
                    $result['longitude'] = (float) $m[2];
                }
            }
        }
        return $result;
    }

    private function normalizeExifDateTime($datetime): ?string
    {
        if (!is_string($datetime) || $datetime === '') {
            return null;
        }
        $dt = \DateTime::createFromFormat('Y:m:d H:i:s', $datetime);
        if (!$dt) {
            return null;
        }
        return $dt->format('Y-m-d H:i:s');
    }

    private function parseExifGps($values, $ref): ?float
    {
        if (!is_array($values) || count($values) < 3 || !is_string($ref)) {
            return null;
        }
        $parts = [];
        foreach ($values as $v) {
            if (is_string($v) && strpos($v, '/') !== false) {
                $segments = explode('/', $v);
                if (count($segments) !== 2) {
                    return null;
                }
                $num = (float) $segments[0];
                $den = (float) $segments[1];
                if ($den == 0.0) {
                    return null;
                }
                $parts[] = $num / $den;
            } else {
                $parts[] = (float) $v;
            }
        }
        if (count($parts) < 3) {
            return null;
        }
        $decimal = $parts[0] + $parts[1] / 60 + $parts[2] / 3600;
        if ($ref === 'S' || $ref === 'W') {
            $decimal = -$decimal;
        }
        return $decimal;
    }

    public function thumbnailPath(int $userId, string $filename): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        return storage_path('app/media-thumbs/' . $userId . '/' . $base . '.jpg');
    }

    private function ensureManager(Request $request): void
    {
        $user = $request->user();
        $allowedIds = config('services.media_manager_user_ids', []);
        if (!$user || !in_array((int) $user->id, $allowedIds, true)) {
            abort(403);
        }
    }

    private function resolveFilePath($userId, $filename)
    {
        if (!ctype_digit((string) $userId)) {
            return null;
        }
        $safeName = basename((string) $filename);
        if ($safeName === '' || $safeName === '.' || $safeName === '..') {
            return null;
        }
        if (strpos($safeName, '/') !== false || strpos($safeName, '\\') !== false) {
            return null;
        }
        $ext = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            return null;
        }
        $absolutePath = storage_path('app/media/' . $userId . '/' . $safeName);
        if (!is_file($absolutePath)) {
            return null;
        }
        return $absolutePath;
    }
}
