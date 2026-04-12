<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBestScoreToCityEscapePlayerStats extends Migration
{
    public function up()
    {
        Schema::table('city_escape_player_stats', function (Blueprint $table) {
            $table->integer('best_score')->default(0)->after('best_time');
        });
    }

    public function down()
    {
        Schema::table('city_escape_player_stats', function (Blueprint $table) {
            $table->dropColumn('best_score');
        });
    }
}
