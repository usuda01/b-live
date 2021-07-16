<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->increments('id')->comment('フォローID');
            $table->integer('follower_id')->unsigned()->comment('フォローしたユーザーID');
            $table->integer('follow_id')->unsigned()->comment('フォローされたユーザーID');
            $table->timestamps();
            $table->foreign('follower_id', 'followers_follower_id_foreign')->references('id')->on('users');
            $table->foreign('follow_id', 'followers_follow_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followers');
    }
}
