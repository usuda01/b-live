<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'ゲスト', 'email' => 'gtSYULvc1FIXMO4@pseudo.twitter.com', 'password' => '1', 'profile' => null, 'twitter_url' => 'https://twitter.com/BLIVE77191685', 'status' => 2, 'image' => 'mdhYzvjTCyq6qPgzRdcgXxMwMmcN37uaQZi95TSh.png'],
            ['id' => 2, 'name' => '【公式】B-LIVE', 'email' => 'BLIVE77191685@pseudo.twitter.com', 'password' => '1', 'profile' => 'とにかく赤字にならなければいい。', 'twitter_url' => null, 'status' => 2, 'image' => 'iq17OfCyvQAjuINKgnwiPNUAG0Eyjq5pYN4Hyps9.jpeg'],
            ['id' => 3, 'name' => 'うっすー', 'email' => 'sumaburaness12@gmail.com', 'password' => '1', 'profile' => "※このサイトの管理人です。\nスマブラ配信してますー", 'twitter_url' => 'https://twitter.com/sumaburaness12', 'status' => 2, 'image' => 'IVPSIwr5K27CLFHlNhqs0dnALeVQFMXsMcdJvspX.png'],
        ]);
    }
}
