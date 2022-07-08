<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            GameSeeder::class,
            UserSeeder::class,
            UserDataSeeder::class,
            GroupSeeder::class,
            WowzaSeeder::class,
            RoomSeeder::class,
            MovieSeeder::class,
            MovieGoodSeeder::class,
            MovieViewLogSeeder::class,
        ]);
    }
}
