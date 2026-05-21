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
            ->get(['user_id', 'filename', 'size', 'is_video', 'has_thumbnail', 'created_at'])
            ->map(function ($row) {
                return [
                    'user_id'       => (int) $row->user_id,
                    'filename'      => $row->filename,
                    'key'           => $row->user_id . '/' . $row->filename,
                    'size'          => (int) $row->size,
                    'is_video'      => (bool) $row->is_video,
                    'has_thumbnail' => (bool) $row->has_thumbnail,
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
        $thumbOk = $this->generateThumbnail((int) $userId, $filename, $absolutePath);

        MediaFile::updateOrCreate(
            ['user_id' => $userId, 'filename' => $filename],
            [
                'size'          => filesize($absolutePath) ?: 0,
                'is_video'      => $isVideo,
                'has_thumbnail' => $thumbOk,
            ]
        );
    }

    public function generateThumbnail(int $userId, string $filename, string $sourcePath): bool
    {
        $thumbPath = $this->thumbnailPath($userId, $filename);
        $thumbDir = dirname($thumbPath);
        if (!is_dir($thumbDir) && !@mkdir($thumbDir, 0755, true) && !is_dir($thumbDir)) {
            return false;
        }

        if ($this->isHeicContent($sourcePath)) {
            return $this->generateHeicThumbnail($sourcePath, $thumbPath);
        }

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

    private function generateHeicThumbnail(string $sourcePath, string $thumbPath): bool
    {
        $script = base_path('scripts/heic_to_thumb.py');
        $process = new Process([
            'python3', $script, $sourcePath, $thumbPath, (string) self::THUMBNAIL_SIZE,
        ]);
        $process->setTimeout(60);

        try {
            $process->run();
        } catch (\Throwable $e) {
            return false;
        }

        return $process->isSuccessful() && is_file($thumbPath);
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
