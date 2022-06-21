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
                'id' => 1, 'user_id' => 3,
                'name' => '【スマブラSP】トナメ配信', 'description' => "Youtube\nhttps://www.youtube.com/watch?v=tB5kKAtiC28",
                'image' => 'YWPdigjIgogeiZxQ2XzqaFkc0nABkG066AXRcGfR.png', 'published_at' => '2022-06-17 11:52:43', 'finished_at' => null,
                'stream_time' => null, 'max_view' => 1, 'rank' => 1, 'status' => 1, 'game_id' => 5, 'wowza_id' => 1, 'created_at' => '2022-06-17 11:52:43', 'updated_at' => '2022-06-17 11:52:59'
            ],
            [
                'id' => 2, 'user_id' => 3,
                'name' => '【スマブラSP】視聴者参加型ライブ配信　対戦しましょうー', 'description' => "Youtube\nhttps://www.youtube.com/watch?v=tB5kKAtiC28",
                'image' => 'MUFAiqHU-2.png', 'published_at' => '2022-05-24 15:28:12', 'finished_at' => '2022-05-24 16:42:12',
                'stream_time' => '01:14:00', 'max_view' => 4, 'rank' => 1, 'status' => 2, 'game_id' => 5, 'wowza_id' => 1, 'created_at' => '2022-05-24 15:28:12', 'updated_at' => '2022-05-24 16:42:12'
            ],
        ]);
    }
}
