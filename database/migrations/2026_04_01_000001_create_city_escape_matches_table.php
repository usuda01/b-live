<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityEscapeMatchesTable extends Migration
{
    public function up()
    {
        Schema::create('city_escape_matches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('room_code', 8);
            $table->enum('status', ['waiting', 'playing', 'finished'])->default('waiting');
            $table->bigInteger('zone_seed')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('city_escape_matches');
    }
}
