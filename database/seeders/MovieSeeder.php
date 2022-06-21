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
                'id' => 1, 'user_id' => 3, 'game_id' => 5, 'name' => 'ネス　メテオ集',
                'image' => 'Wpva7bIYa8OzSZaoZVY55t5JiICMq4NxGWnrpsZD.png', 'path' => '2022-06-17-10-52-39.mp4', 'is_publish' => 1,
                'created_at' => '2022-06-17 10:52:42', 'updated_at' => '2022-06-17 11:52:00'
            ],
        ]);
    }
}
