<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToWowzasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wowzas', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->after('wowza_id')->nullable()->comment('使用したユーザーID');
            $table->foreign('user_id', 'wowzas_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wowzas', function (Blueprint $table) {
            $table->dropForeign('wowzas_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
