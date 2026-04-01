<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityEscapeMatchPlayersTable extends Migration
{
    public function up()
    {
        Schema::create('city_escape_match_players', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('match_id');
            $table->unsignedInteger('user_id');
            $table->integer('finish_rank')->nullable();
            $table->integer('score')->default(0);
            $table->float('elapsed_time')->nullable();
            $table->boolean('is_caught')->default(false);

            $table->foreign('match_id')->references('id')->on('city_escape_matches');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('city_escape_match_players');
    }
}
