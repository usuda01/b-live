<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsLineConnectedToUserDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->integer('is_line_connected')->unsigned()->after('join_ranking')->nullable()->comment('LINE連携');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->dropColumn('is_line_connected');
        });
    }
}
