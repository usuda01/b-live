<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id')->comment('メッセージID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->integer('room_id')->unsigned()->comment('ルームID');
            $table->string('content', 128)->comment('メッセージ');
            $table->timestamps();
            $table->foreign('user_id', 'messages_user_id_foreign')->references('id')->on('users');
            $table->foreign('room_id', 'messages_room_id_foreign')->references('id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
