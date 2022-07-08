<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovieViewLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_view_logs', function (Blueprint $table) {
            $table->increments('id')->comment('動画再生ログID');
            $table->integer('movie_id')->unsigned()->comment('動画ID');
            $table->string('ip_address', 50)->comment('IPAddress');
            $table->string('user_agent', 256)->comment('ユーザーエージェント');
            $table->timestamps();
            $table->foreign('movie_id', 'movie_view_logs_movie_id_foreign')->references('id')->on('movies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_view_logs');
    }
}
