<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovieGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_goods', function (Blueprint $table) {
            $table->increments('id')->comment('いいねID');
            $table->integer('user_id')->unsigned()->comment('いいねしたユーザーID');
            $table->integer('movie_id')->unsigned()->comment('動画ID');
            $table->timestamps();
            $table->foreign('user_id', 'movie_goods_user_id_foreign')->references('id')->on('users');
            $table->foreign('movie_id', 'movie_goods_movie_id_foreign')->references('id')->on('movies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_goods');
    }
}
