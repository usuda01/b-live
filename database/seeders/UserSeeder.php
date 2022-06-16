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
            ['id' => 1, 'name' => 'ゲスト', 'email' => 'gtSYULvc1FIXMO4@pseudo.twitter.com', 'password' => '1', 'status' => 2, 'image' => 'mdhYzvjTCyq6qPgzRdcgXxMwMmcN37uaQZi95TSh.png'],
        ]);
    }
}
