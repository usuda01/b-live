<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityEscapePlayerStatsTable extends Migration
{
    public function up()
    {
        Schema::create('city_escape_player_stats', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->integer('total_matches')->default(0);
            $table->integer('total_wins')->default(0);
            $table->bigInteger('total_score')->default(0);
            $table->float('best_time')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('city_escape_player_stats');
    }
}
