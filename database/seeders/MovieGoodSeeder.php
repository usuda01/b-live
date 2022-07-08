<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieGoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('movie_goods')->insert([
            ['id' => 1, 'user_id' => 3, 'movie_id' => 1, 'created_at' => '2022-07-08 12:36:59', 'updated_at' => '2022-07-08 12:36:59'],
            ['id' => 2, 'user_id' => 3, 'movie_id' => 5, 'created_at' => '2022-07-08 12:36:59', 'updated_at' => '2022-07-08 12:36:59'],
            ['id' => 3, 'user_id' => 3, 'movie_id' => 2, 'created_at' => '2022-07-08 12:36:59', 'updated_at' => '2022-07-08 12:36:59'],
            ['id' => 4, 'user_id' => 3, 'movie_id' => 6, 'created_at' => '2022-07-08 12:53:12', 'updated_at' => '2022-07-08 12:53:12'],
            ['id' => 5, 'user_id' => 6, 'movie_id' => 6, 'created_at' => '2022-07-08 12:53:12', 'updated_at' => '2022-07-08 12:53:12'],
        ]);
    }
}
