<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id')->comment('通知ID');
            $table->enum('type', ['user_action', 'system_announcement'])->comment('通知タイプ');
            $table->integer('sender_id')->unsigned()->nullable()->comment('通知送信者のユーザーID（運営通知の場合はNULL）');
            $table->integer('receiver_id')->unsigned()->comment('通知受信者のユーザーID');
            $table->string('title', 1000)->comment('通知タイトル');
            $table->string('message', 1000)->nullable()->comment('通知本文');
            $table->integer('is_read')->unsigned()->default(0)->comment('既読フラグ');
            $table->timestamp('read_at')->nullable()->comment('既読日時');
            $table->timestamps();

            // 外部キー制約
            $table->foreign('sender_id', 'notifications_sender_id_foreign')->references('id')->on('users');
            $table->foreign('receiver_id', 'notifications_receiver_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
