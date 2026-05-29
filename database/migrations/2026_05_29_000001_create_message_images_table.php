<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_images', function (Blueprint $table) {
            $table->increments('id')->comment('メッセージ画像ID');
            $table->integer('message_id')->unsigned()->comment('メッセージID');
            $table->integer('user_id')->unsigned()->comment('アップロードしたユーザーID');
            $table->string('filename', 128)->comment('ファイル名（拡張子込み）');
            $table->bigInteger('size')->unsigned()->comment('ファイルサイズ（バイト）');
            $table->tinyInteger('has_thumbnail')->unsigned()->default(0)->comment('0:未生成 / 1:生成済み');
            $table->timestamps();

            $table->unique('message_id', 'message_images_message_id_unique');
            $table->index(['user_id', 'created_at'], 'message_images_user_id_created_at_index');

            $table->foreign('message_id', 'message_images_message_id_foreign')
                  ->references('id')->on('messages')
                  ->onDelete('cascade');
            $table->foreign('user_id', 'message_images_user_id_foreign')
                  ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_images');
    }
}
