<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->increments('id')->comment('動画ID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->integer('game_id')->unsigned()->nullable()->comment('GameID');
            $table->string('name', 64)->comment('動画名');
            $table->string('image', 128)->nullable()->comment('動画サムネイル');
            $table->string('path', 128)->comment('動画パス');
            $table->integer('is_publish')->comment('0:掲載しない/1:掲載する');
            $table->timestamps();
            $table->foreign('user_id', 'movies_user_id_foreign')->references('id')->on('users');
            $table->foreign('game_id', 'movies_game_id_foreign')->references('id')->on('games');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
