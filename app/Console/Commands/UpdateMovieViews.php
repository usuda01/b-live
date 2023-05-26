<?php

/*
 * movie_view_logsをもとに集計する
 * 実行ごとに再生数がカウントアップされるため、1分に1回のみ実行する
 */

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\MovieViewLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMovieViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-movie-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // データが肥大化してしまうので、3ヶ月前のログは削除する
        $movieViewLogs = MovieViewLog::where('created_at', '<', date('Y-m-d H:i:s', strtotime('-3 month')))->delete();

        // 過去1日のログを集計し、カウントアップ
        $movieViewLogs = MovieViewLog::select(
                'movie_id',
                DB::raw('count(movie_id) as views'),
            )
            ->where('created_at', '>', date('Y-m-d H:i:00', strtotime('-1 minute')))
            ->groupBy('movie_id')
            ->get();

        // カウントアップ処理
        foreach ($movieViewLogs as $movieViewLog) {
            $movie = Movie::where('id', $movieViewLog->movie_id)->first();
            $movie->views += $movieViewLog->views;
            $movie->save();
        }
        return 0;
    }
}
