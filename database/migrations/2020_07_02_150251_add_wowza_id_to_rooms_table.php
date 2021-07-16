<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWowzaIdToRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('wowza_id')->unsigned()->after('status')->nullable()->comment('WowzaID');
            $table->foreign('wowza_id', 'rooms_wowza_id_foreign')->references('id')->on('wowzas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign('rooms_wowza_id_foreign');
            $table->dropColumn('wowza_id');
        });
    }
}
