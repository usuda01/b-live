<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UnifyNoticeColumnsInUserDatasTable extends Migration
{
    public function up()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->integer('notice_live_start')->after('is_notice1')->unsigned()->default(1)->comment('配信開始通知 0/1');
            $table->integer('notice_follow')->after('notice_live_start')->unsigned()->default(1)->comment('フォロー通知 0/1');
        });

        DB::statement('UPDATE user_datas SET notice_live_start = CASE WHEN is_notice1 = 1 OR line_notice = 1 THEN 1 ELSE 0 END');

        Schema::table('user_datas', function (Blueprint $table) {
            $table->dropColumn(['is_notice1', 'line_notice']);
        });
    }

    public function down()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->integer('line_notice')->after('join_ranking')->unsigned()->default(1)->comment('0:通知しない/1:通知する');
            $table->integer('is_notice1')->after('line_notice')->unsigned()->default(1)->comment('配信通知 0/1');
        });

        DB::statement('UPDATE user_datas SET is_notice1 = notice_live_start, line_notice = notice_live_start');

        Schema::table('user_datas', function (Blueprint $table) {
            $table->dropColumn(['notice_live_start', 'notice_follow']);
        });
    }
}
