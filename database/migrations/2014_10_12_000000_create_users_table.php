<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->comment('ユーザーID');
            $table->string('name', 24)->comment('ユーザー名');
            $table->string('email', 64)->comment('メールアドレス');
            $table->string('password', 64)->comment('パスワード');
            $table->string('facebook_id', 50)->nullable()->comment('facebookID');
            $table->string('twitter_id', 50)->nullable()->comment('twitterID');
            $table->integer('status')->comment('1:仮登録/2:本登録');
            $table->string('image', 128)->nullable()->comment('プロフィール画像');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
