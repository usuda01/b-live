<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms')->insert([
            [
                'id' => 1,
                'user_id' => 3,
                'name' => '【スマブラSP】トナメ配信',
                'description' => "Youtube\nhttps://www.youtube.com/watch?v=tB5kKAtiC28",
                'image' => 'YWPdigjIgogeiZxQ2XzqaFkc0nABkG066AXRcGfR.png',
                'published_at' => DB::raw('NOW()'),
                'finished_at' => null,
                'stream_time' => null,
                'max_view' => 1,
                'rank' => 1,
                'status' => 1,
                'game_id' => 5,
                'wowza_id' => 1,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => '2022-06-17 11:52:59'
            ],
            [
                'id' => 2,
                'user_id' => 3,
                'name' => '【スマブラSP】視聴者参加型ライブ配信　対戦しましょうー',
                'description' => "Youtube\nhttps://www.youtube.com/watch?v=tB5kKAtiC28",
                'image' => 'MUFAiqHU-2.png',
                'published_at' => '2022-05-24 15:28:12',
                'finished_at' => '2022-05-24 16:42:12',
                'stream_time' => '01:14:00',
                'max_view' => 4,
                'rank' => 1,
                'status' => 2,
                'game_id' => 5,
                'wowza_id' => 1,
                'created_at' => '2022-05-24 15:28:12',
                'updated_at' => '2022-05-24 16:42:12'
            ],
            [
                'id' => 3,
                'user_id' => 4,
                'name' => 'リハビリ配信宇宙提督ドッグ',
                'description' => "配信リハビリのため１～２時間くらいを予定しております",
                'image' => 'cCgpzI3fHiTCNaPTLFVZIziKt2bg22ghByrVR7uI.jpg',
                'published_at' => '2022-06-21 18:06:49',
                'finished_at' => '2022-06-21 20:00:02',
                'stream_time' => '01:53:13',
                'max_view' => 4,
                'rank' => 1,
                'status' => 2,
                'game_id' => null,
                'wowza_id' => 2,
                'created_at' => '2022-06-21 18:06:49',
                'updated_at' => '2022-06-21 20:00:02'
            ],
            [
                'id' => 4,
                'user_id' => 10,
                'name' => '【MHRS/参加型】日曜日だよ♪金冠集め',
                'description' => "ご視聴・ご参加していただきありがとうございます♪\n超絶下手なみちゃそがまったりぼちぼちやっていきます♪\nチャンネル登録・高評価していただけると今後の励みになりますのでお願いします♪\n\n✿配信時間\n9：30～12：00\n\n✿お休み\n月曜日・木曜日\n\n✿配信内容\n火・水\nDBD APEX PCゲーム\n金・土・日\nMHRSB\n配信予定・内容はTwitterで報告させていただきますので登録お願いします♪",
                'image' => '4bh1EfX3amhMdoqNlUW7zlptV420TIs2FgncKupM.png',
                'published_at' => DB::raw('NOW()'),
                'finished_at' => null,
                'stream_time' => null,
                'max_view' => 4,
                'rank' => 1,
                'status' => 1,
                'game_id' => 20,
                'wowza_id' => 4,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => '2022-06-21 20:00:02'
            ],
        ]);
    }
}
