<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->increments('id')->comment('メディアファイルID');
            $table->integer('user_id')->unsigned()->comment('アップロードしたユーザーID');
            $table->string('filename', 128)->comment('ファイル名（拡張子込み）');
            $table->unsignedBigInteger('size')->comment('ファイルサイズ（バイト）');
            $table->integer('is_video')->unsigned()->default(0)->comment('0:画像/1:動画');
            $table->integer('has_thumbnail')->unsigned()->default(0)->comment('0:未生成/1:生成済み');
            $table->timestamps();

            $table->unique(['user_id', 'filename'], 'media_files_user_filename_unique');
            $table->index(['user_id', 'created_at'], 'media_files_user_created_index');
            $table->foreign('user_id', 'media_files_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_files');
    }
}
