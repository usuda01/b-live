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
        ]);
    }
}
