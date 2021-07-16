<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_requests', function (Blueprint $table) {
            $table->increments('id')->comment('ポイントリクエストID');
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->integer('request_point')->comment('リクエストポイント');
            $table->integer('amount')->comment('金額');
            $table->string('bank_name', 64)->comment('銀行名');
            $table->string('branch_name', 64)->comment('支店名');
            $table->integer('account_type')->comment('1:普通/2:当座/3:定期');
            $table->integer('account_number')->comment('口座番号');
            $table->string('account_name', 64)->comment('口座名義');
            $table->timestamps();
            $table->foreign('user_id', 'point_requests_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_requests');
    }
}
