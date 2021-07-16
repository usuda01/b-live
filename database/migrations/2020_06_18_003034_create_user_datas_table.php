<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_datas', function (Blueprint $table) {
            $table->increments('id')->comment('ユーザーデータID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->string('stripe_id')->comment('StripeID');
            $table->integer('stripe_status')->unsigned()->default(0)->comment('0:通常会員/1:プレミアム会員');
            $table->integer('point')->unsigned()->comment('ポイント');
            $table->timestamps();
            $table->foreign('user_id', 'user_datas_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_datas');
    }
}
