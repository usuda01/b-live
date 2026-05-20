<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    private const MAX_BYTES = 1610612736; // 1.5 GiB
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'heic', 'mov', 'mp4', 'm4v'];

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

        return response()->json([
            'status' => 'stored',
            'path'   => $relativePath,
            'bytes'  => $totalBytes,
        ]);
    }
}
