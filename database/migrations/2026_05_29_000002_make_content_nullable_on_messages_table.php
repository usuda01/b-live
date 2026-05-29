<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeContentNullableOnMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('content', 128)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 既存データに null が混ざっている場合 change() で NOT NULL に戻すと失敗するため、
        // 画像メッセージ（content が null）を伴う前提では down は手動対応とする
        Schema::table('messages', function (Blueprint $table) {
            $table->string('content', 128)->nullable(false)->change();
        });
    }
}
