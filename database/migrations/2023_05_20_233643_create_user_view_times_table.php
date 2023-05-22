<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserViewTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_view_times', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('viewer_user_id')->unsigned()->comment('視聴したユーザーID');
            $table->integer('viewed_user_id')->unsigned()->comment('視聴されたユーザーID');
            $table->time('view_time')->nullable()->comment('視聴時間');
            $table->timestamps();
            $table->foreign('viewer_user_id', 'user_view_times_viewer_user_id_foreign')->references('id')->on('users');
            $table->foreign('viewed_user_id', 'user_view_times_viewed_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_view_times');
    }
}
