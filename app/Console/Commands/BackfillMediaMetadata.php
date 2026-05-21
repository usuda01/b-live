<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\MediaController;
use App\Models\MediaFile;
use Illuminate\Console\Command;

class BackfillMediaMetadata extends Command
{
    protected $signature = 'media:backfill-metadata {--dry-run : 実際には書き込まずに対象だけ表示}';

    protected $description = '既存の media_files レコードに EXIF/動画メタデータ（撮影日時・GPS）を遡及反映する';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $controller = app(MediaController::class);

        $rows = MediaFile::orderBy('id')->get();
        $total = $rows->count();
        $this->info("対象: {$total} 件");

        $updated = 0;
        foreach ($rows as $row) {
            $sourcePath = storage_path('app/media/' . $row->user_id . '/' . $row->filename);
            if (!is_file($sourcePath)) {
                $this->warn("missing file: id={$row->id} {$row->filename}");
                continue;
            }

            $meta = $controller->extractMetadata($sourcePath, (bool) $row->is_video);
            $line = sprintf(
                'id=%d %s taken_at=%s lat=%s lng=%s',
                $row->id,
                $row->filename,
                $meta['taken_at'] ?? 'null',
                $meta['latitude'] ?? 'null',
                $meta['longitude'] ?? 'null'
            );
            $this->line($line);

            if (!$dryRun) {
                $row->taken_at = $meta['taken_at'];
                $row->latitude = $meta['latitude'];
                $row->longitude = $meta['longitude'];
                $row->save();
                $updated++;
            }
        }

        $this->info("更新: {$updated} 件" . ($dryRun ? ' (dry-run)' : ''));

        return 0;
    }
}
