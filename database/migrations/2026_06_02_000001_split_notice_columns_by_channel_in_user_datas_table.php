<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SplitNoticeColumnsByChannelInUserDatasTable extends Migration
{
    public function up()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->integer('notice_live_start_mail')->after('notice_follow')->unsigned()->default(1)->comment('配信開始通知(メール) 0/1');
            $table->integer('notice_live_start_push')->after('notice_live_start_mail')->unsigned()->default(1)->comment('配信開始通知(プッシュ) 0/1');
            $table->integer('notice_live_start_line')->after('notice_live_start_push')->unsigned()->default(1)->comment('配信開始通知(LINE) 0/1');
            $table->integer('notice_follow_mail')->after('notice_live_start_line')->unsigned()->default(1)->comment('フォロー通知(メール) 0/1');
            $table->integer('notice_follow_push')->after('notice_follow_mail')->unsigned()->default(1)->comment('フォロー通知(プッシュ) 0/1');
            $table->integer('notice_follow_line')->after('notice_follow_push')->unsigned()->default(1)->comment('フォロー通知(LINE) 0/1');
        });

        // 既存の一括フラグを各チャネルへ引き継ぐ
        DB::statement('UPDATE user_datas SET
            notice_live_start_mail = notice_live_start,
            notice_live_start_push = notice_live_start,
            notice_live_start_line = notice_live_start,
            notice_follow_mail = notice_follow,
            notice_follow_push = notice_follow,
            notice_follow_line = notice_follow');

        Schema::table('user_datas', function (Blueprint $table) {
            $table->dropColumn(['notice_live_start', 'notice_follow']);
        });
    }

    public function down()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->integer('notice_live_start')->after('is_line_connected')->unsigned()->default(1)->comment('配信開始通知 0/1');
            $table->integer('notice_follow')->after('notice_live_start')->unsigned()->default(1)->comment('フォロー通知 0/1');
        });

        // いずれかのチャネルが有効なら一括フラグを有効として復元
        DB::statement('UPDATE user_datas SET
            notice_live_start = GREATEST(notice_live_start_mail, notice_live_start_push, notice_live_start_line),
            notice_follow = GREATEST(notice_follow_mail, notice_follow_push, notice_follow_line)');

        Schema::table('user_datas', function (Blueprint $table) {
            $table->dropColumn([
                'notice_live_start_mail', 'notice_live_start_push', 'notice_live_start_line',
                'notice_follow_mail', 'notice_follow_push', 'notice_follow_line',
            ]);
        });
    }
}
