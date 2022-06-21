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
                'started_at' => '2022-06-17 11:52:43', 'finished_at' => null, 'status' => 1, 'created_at' => '2022-06-17 10:30:26', 'updated_at' => '2022-06-17 11:52:43'
            ],
        ]);
    }
}
