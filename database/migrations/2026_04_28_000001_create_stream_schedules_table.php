<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_schedules', function (Blueprint $table) {
            $table->increments('id')->comment('配信予定ID');
            $table->integer('user_id')->unsigned()->comment('配信者のユーザーID');
            $table->string('title', 64)->comment('配信タイトル');
            $table->string('description', 1000)->nullable()->comment('配信概要');
            $table->integer('game_id')->unsigned()->nullable()->comment('カテゴリ（ゲーム）ID');
            $table->string('thumbnail', 128)->nullable()->comment('サムネイル画像ファイル名');
            $table->dateTime('scheduled_start_at')->comment('予定開始日時');
            $table->dateTime('scheduled_end_at')->nullable()->comment('予定終了日時（任意）');
            $table->integer('status')->comment('1:公開/2:非公開/3:キャンセル/4:配信中/5:終了');
            $table->integer('room_id')->unsigned()->nullable()->comment('配信開始時に紐付くRoom ID');
            $table->timestamps();

            $table->foreign('user_id', 'stream_schedules_user_id_foreign')->references('id')->on('users');
            $table->foreign('game_id', 'stream_schedules_game_id_foreign')->references('id')->on('games');
            $table->foreign('room_id', 'stream_schedules_room_id_foreign')->references('id')->on('rooms');

            $table->index(['scheduled_start_at', 'status'], 'stream_schedules_start_status_index');
            $table->index(['user_id', 'scheduled_start_at'], 'stream_schedules_user_start_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stream_schedules');
    }
}
