<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([
            [
                'id' => 1,
                'user_id' => 7,
                'game_title' => '色々！　',
                'name' => '配信参加可能人材募集って書いたらかっこよくない？',
                'image' => 'E2qWmMAvQcP3wo1OuvX13btm8O2rCBJI3U4vq7dm.png',
                'member_number' => 1,
                'description' => "配信でパーティーゲームなど、大人数参加系ゲーム（golf itとか）をしたりできる人を探しています！\n\n配信者、視聴者問いません！\n\nゲームの上手い下手も問いません！\n\nもし、視点配信がしたい場合も大歓迎です！\n\n興味がある方は@izu_gamerのツイッターDMまたは配信中のコメント欄までお越しください！\n\nクラン人数？\n１だよこんチキショー！",
                'is_publish' => 1,
                'created_at' => '2021-07-01 01:34:39',
                'updated_at' => '2021-07-01 01:35:29'
            ],
        ]);
    }
}
