<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id')->comment('グループID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->string('game_title', 128)->comment('ゲームタイトル');
            $table->string('name', 64)->comment('グループ名');
            $table->integer('member_number')->comment('人数');
            $table->string('description', 1000)->comment('概要');
            $table->integer('is_publish')->comment('0:掲載しない/1:掲載する');
            $table->timestamps();
            $table->foreign('user_id', 'groups_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
