<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WowzaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wowzas')->insert([
            [
                'id' => 1, 'wowza_id' => null, 'user_id' => 3,
                'server_url' => 'rtmps://5f1ee0e19125e.streamlock.net/blive', 'stream_key' => 'dsnjaUHP', 'hls_url' => 'https://5f1ee0e19125e.streamlock.net/blive/dsnjaUHP/playlist.m3u8',
                'started_at' => '2022-06-17 11:52:43',
                'finished_at' => null,
                'status' => 1,
                'created_at' => '2022-06-17 10:30:26', 'updated_at' => '2022-06-17 11:52:43'
            ],
            [
                'id' => 2, 'wowza_id' => null, 'user_id' => 4,
                'server_url' => 'rtmps://5f1ee0e19125e.streamlock.net/blive', 'stream_key' => 'zUyFaXJt', 'hls_url' => 'https://5f1ee0e19125e.streamlock.net/blive/zUyFaXJt/playlist.m3u8',
                'started_at' => '2022-06-21 18:06:49',
                'finished_at' => '2022-06-21 20:00:02',
                'status' => 2,
                'created_at' => '2021-06-15 17:27:49', 'updated_at' => '2022-06-21 20:00:02'
            ],
            [
                'id' => 3, 'wowza_id' => null, 'user_id' => 5,
                'server_url' => 'rtmps://5f1ee0e19125e.streamlock.net/blive', 'stream_key' => '85ly8RBM', 'hls_url' => 'https://5f1ee0e19125e.streamlock.net/blive/85ly8RBM/playlist.m3u8',
                'started_at' => '2022-05-06 22:12:42',
                'finished_at' => '2022-05-07 00:22:04',
                'status' => 2,
                'created_at' => '2021-06-03 01:07:07', 'updated_at' => '2022-05-07 00:22:04'
            ],
            [
                'id' => 4, 'wowza_id' => null, 'user_id' => 10,
                'server_url' => 'rtmps://5f1ee0e19125e.streamlock.net/blive', 'stream_key' => '532rm9Ms', 'hls_url' => 'https://5f1ee0e19125e.streamlock.net/blive/532rm9Ms/playlist.m3u8',
                'started_at' => '2022-07-31 09:31:48',
                'finished_at' => null,
                'status' => 1,
                'created_at' => '2021-06-03 01:07:07', 'updated_at' => '2022-05-07 00:22:04'
            ],
        ]);
    }
}
