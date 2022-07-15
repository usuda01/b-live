<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_datas')->insert([
            ['id' => 1, 'user_id' => 1, 'stripe_id' => '', 'point' => 0, 'rank' => 1],
            ['id' => 2, 'user_id' => 2, 'stripe_id' => '', 'point' => 5000, 'rank' => 5],
            ['id' => 3, 'user_id' => 3, 'stripe_id' => 'cus_HUZrWhauomejio', 'point' => 100000, 'rank' => 1],
            ['id' => 4, 'user_id' => 4, 'stripe_id' => '', 'point' => 4430, 'rank' => 2],
            ['id' => 5, 'user_id' => 5, 'stripe_id' => '', 'point' => 1300, 'rank' => 1],
            ['id' => 6, 'user_id' => 6, 'stripe_id' => '', 'point' => 10, 'rank' => 1],
            ['id' => 7, 'user_id' => 7, 'stripe_id' => '', 'point' => 600, 'rank' => 1],
            ['id' => 8, 'user_id' => 8, 'stripe_id' => '', 'point' => 0, 'rank' => 1],
            ['id' => 9, 'user_id' => 9, 'stripe_id' => '', 'point' => 660, 'rank' => 1],
        ]);
    }
}
