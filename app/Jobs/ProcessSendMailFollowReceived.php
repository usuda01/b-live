<?php

namespace App\Jobs;

use App\Mail\FollowReceived;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSendMailFollowReceived implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $follower;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($follower)
    {
        $this->follower = $follower;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // FollowReceivedのメールを送信する処理
        $data = [
            'recipientName' => $this->follower->followUser->name,
            'followerName' => $this->follower->followerUser->name,
            'followerId' => $this->follower->followerUser->id,
        ];
        \Mail::to($this->follower->followUser->email)->send(new FollowReceived($data));
        Log::debug($this->follower->followUser->email);
    }
}
