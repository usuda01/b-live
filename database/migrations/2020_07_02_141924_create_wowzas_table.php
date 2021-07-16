<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWowzasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wowzas', function (Blueprint $table) {
            $table->increments('id')->comment('WowzaID');
            $table->string('wowza_id', 128)->nullable()->comment('WowzaID');
            $table->string('server_url', 256)->comment('サーバーURL');
            $table->string('stream_key', 128)->comment('ストリームキー');
            $table->string('hls_url', 256)->comment('サーバーURL');
            $table->timestamp('started_at')->nullable()->comment('スタート日時');
            $table->timestamp('finished_at')->nullable()->comment('終了日時');
            $table->integer('status')->comment('1:使用中/2:終了');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wowzas');
    }
}
