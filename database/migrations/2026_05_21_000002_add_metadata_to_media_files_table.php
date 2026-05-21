<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetadataToMediaFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_files', function (Blueprint $table) {
            $table->timestamp('taken_at')->nullable()->after('has_thumbnail')->comment('撮影日時（EXIF DateTimeOriginal等）');
            $table->decimal('latitude', 10, 7)->nullable()->after('taken_at')->comment('撮影地点の緯度');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude')->comment('撮影地点の経度');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_files', function (Blueprint $table) {
            $table->dropColumn(['taken_at', 'latitude', 'longitude']);
        });
    }
}
