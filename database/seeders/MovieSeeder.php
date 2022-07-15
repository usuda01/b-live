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
                'image' => 'Wpva7bIYa8OzSZaoZVY55t5JiICMq4NxGWnrpsZD.png',
                'path' => '2022-06-17-10-52-39.mp4',
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
                'image' => 'TCgQd3KaeFEH9GSATnTDsORonqTT4OM9oBxaVqkh.png',
                'path' => '2021-08-19-01-41-30.mp4',
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
                'image' => 'LSDXpE0VapaLuRYJXoDEgPNHLM9MP4THAOiEA0N3.png',
                'path' => '2021-08-18-01-19-48.mp4',
                'is_publish' => 1,
                'duration' => '00:00:05',
                'created_at' => '2021-08-18 01:19:49',
                'updated_at' => '2021-08-18 01:20:51'
            ],
            [
                'id' => 4,
                'user_id' => 3,
                'game_id' => 5,
                'name' => 'サイマグ復帰阻止-2',
                'image' => 'yMYFWcNavbaEFayNQGTz6W4uGyoniJGIDWtLwJKD.png',
                'path' => '2021-08-15-20-54-14.mp4',
                'is_publish' => 1,
                'duration' => '00:00:11',
                'created_at' => '2021-08-15 20:54:14',
                'updated_at' => '2021-08-22 00:00:50'
            ],
            [
                'id' => 5,
                'user_id' => 3,
                'game_id' => 5,
                'name' => 'サイマグ復帰阻止',
                'image' => 'ac8rptPjTPXvKR4BgKn0z3MZgtSKcPndvyVRLMBn.png',
                'path' => '2021-08-19-23-33-53.mp4',
                'is_publish' => 1,
                'duration' => '00:00:12',
                'created_at' => '2021-08-19 23:33:55',
                'updated_at' => '2021-08-19 23:33:55'
            ],
            [
                'id' => 6,
                'user_id' => 6,
                'game_id' => 20,
                'name' => '狙い撃ち',
                'image' => '2RC9tYHSzvUWV4oNMfxyjx9ickR6d4l9CzqddBsU.jpg',
                'path' => '2022-06-30-18-12-36.mp4',
                'is_publish' => 1,
                'duration' => '00:00:11',
                'created_at' => '2022-06-30 18:12:37',
                'updated_at' => '2022-06-30 18:12:37'
            ],
            [
                'id' => 7,
                'user_id' => 8,
                'game_id' => 22,
                'name' => '#1 今作一のトラウマシーン',
                'image' => 'GF2Kj7bQP4HEtpGPPUizEiNX5yrRe8PiQHESCgpI.jpg',
                'path' => '2022-07-11-11-21-39.mp4',
                'is_publish' => 1,
                'duration' => '00:00:28',
                'created_at' => '2022-07-11 11:21:47',
                'updated_at' => '2022-07-11 12:05:02'
            ],
            [
                'id' => 8,
                'user_id' => 8,
                'game_id' => 22,
                'name' => '#2 今作一のトラウマシーン！！',
                'image' => 'XS6NnY97FvDFV4MCJoViXSeXETo8eKNaYrOUoitV.jpg',
                'path' => '2022-07-11-11-24-37.mp4',
                'is_publish' => 1,
                'duration' => '00:00:25',
                'created_at' => '2022-07-11 11:24:45',
                'updated_at' => '2022-07-11 12:05:02'
            ],
            [
                'id' => 9,
                'user_id' => 9,
                'game_id' => 23,
                'name' => 'そんなことある！？',
                'image' => 'BPFpQ6BkJYXZ6KT5pRUFTMKGiki2hc7hlb1pDgTB.png',
                'path' => '2022-07-14-23-46-54.mp4',
                'is_publish' => 1,
                'duration' => '00:00:20',
                'created_at' => '2022-07-14 23:46:56',
                'updated_at' => '2022-07-15 15:03:01'
            ],
        ]);
    }
}
