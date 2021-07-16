<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id')->comment('ペイメントID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->integer('message_id')->unsigned()->nullable()->comment('メッセージID');
            $table->string('product_id', 128)->comment('製品ID');
            $table->timestamps();
            $table->foreign('user_id', 'payments_user_id_foreign')->references('id')->on('users');
            $table->foreign('message_id', 'payments_message_id_foreign')->references('id')->on('messages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
