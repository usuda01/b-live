<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_rankings', function (Blueprint $table) {
            $table->increments('id')->comment('イベントランキングID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->integer('room_id')->unsigned()->comment('ルームID');
            $table->string('room_name', 32)->comment('ルーム名');
            $table->string('room_image', 128)->nullable()->comment('ルーム画像');
            $table->integer('max_view')->comment('瞬間最大視聴者数');
            $table->timestamps();
            $table->foreign('user_id', 'event_rankins_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_rankings');
    }
}
