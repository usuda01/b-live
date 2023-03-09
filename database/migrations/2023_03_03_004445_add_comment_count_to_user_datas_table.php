<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentCountToUserDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->integer('comment_count')->after('point')->unsigned()->default(0)->comment('総コメント数');
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
            $table->dropColumn('comment_count');
        });
    }
}
