<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RoomRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_rankings', function (Blueprint $table) {
            $table->increments('id')->comment('ルームランキングID');
            $table->integer('rank')->comment('動画ランク');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->integer('room_id')->unsigned()->comment('ルームID');
            $table->integer('max_view')->comment('瞬間最大視聴者数');
            $table->timestamps();
            $table->foreign('user_id', 'room_rankins_user_id_foreign')->references('id')->on('users');
            $table->foreign('room_id', 'room_rankins_room_id_foreign')->references('id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_rankings');
    }
}
