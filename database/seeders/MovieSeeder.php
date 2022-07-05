<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('movies')->insert([
            [
                'id' => 1,
                'user_id' => 3,
                'game_id' => 5,
                'name' => 'ネス　メテオ集',
                'image' => 'Wpva7bIYa8OzSZaoZVY55t5JiICMq4NxGWnrpsZD.png', 'path' => '2022-06-17-10-52-39.mp4',
                'is_publish' => 1,
                'duration' => '00:00:16',
                'created_at' => '2022-06-17 10:52:42',
                'updated_at' => '2022-06-17 11:52:00'
            ],
            [
                'id' => 2,
                'user_id' => 5,
                'game_id' => 14,
                'name' => '防衛命令',
                'image' => 'TCgQd3KaeFEH9GSATnTDsORonqTT4OM9oBxaVqkh.png', 'path' => '2021-08-19-01-41-30.mp4',
                'is_publish' => 1,
                'duration' => '00:00:19',
                'created_at' => '2021-08-19 01:41:32',
                'updated_at' => '2021-08-19 01:41:32'
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'game_id' => 5,
                'name' => 'PKサンダーバースト',
                'image' => 'LSDXpE0VapaLuRYJXoDEgPNHLM9MP4THAOiEA0N3.png', 'path' => '2021-08-18-01-19-48.mp4',
                'is_publish' => 1,
                'duration' => '00:00:05',
                'created_at' => '2021-08-18 01:19:49',
                'updated_at' => '2021-08-18 01:20:51'
            ],
        ]);
    }
}
