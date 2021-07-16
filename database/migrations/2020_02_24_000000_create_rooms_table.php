<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id')->comment('ストリームID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->string('name', 32)->comment('ルーム名');
            $table->string('stream_key', 24)->comment('ストリームキー');
            $table->string('image', 128)->nullable()->comment('ルーム画像');
            $table->timestamp('published_at')->nullable()->comment('公開日時');
            $table->timestamp('finished_at')->nullable()->comment('終了日時');
            $table->integer('status')->comment('1:公開中/2:終了/3:終了（非公開）');
            $table->timestamps();
            $table->foreign('user_id', 'rooms_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
