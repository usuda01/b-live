<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovieMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_messages', function (Blueprint $table) {
            $table->increments('id')->comment('メッセージID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->integer('movie_id')->unsigned()->comment('動画ID');
            $table->string('content', 128)->comment('メッセージ');
            $table->timestamps();
            $table->foreign('user_id', 'movie_messages_user_id_foreign')->references('id')->on('users');
            $table->foreign('movie_id', 'movie_messages_movie_id_foreign')->references('id')->on('movies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_messages');
    }
}
