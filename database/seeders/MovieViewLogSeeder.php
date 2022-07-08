<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieViewLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('movie_view_logs')->insert([
            [
                'id' => 1,
                'movie_id' => 1,
                'ip_address' => '172.18.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 month')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 month'))
            ],
            [
                'id' => 2,
                'movie_id' => 1,
                'ip_address' => '172.18.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'id' => 3,
                'movie_id' => 2,
                'ip_address' => '172.18.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'id' => 4,
                'movie_id' => 1,
                'ip_address' => '172.18.0.1',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
        ]);
    }
}
