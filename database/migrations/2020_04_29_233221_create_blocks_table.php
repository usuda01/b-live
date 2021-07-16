<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->increments('id')->comment('ブロックID');
            $table->integer('blocker_id')->unsigned()->comment('フォローしたユーザーID');
            $table->integer('blocked_id')->unsigned()->comment('フォローされたユーザーID');
            $table->timestamps();
            $table->foreign('blocker_id', 'blocks_blocker_id_foreign')->references('id')->on('users');
            $table->foreign('blocked_id', 'blocks_blocked_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blocks');
    }
}
