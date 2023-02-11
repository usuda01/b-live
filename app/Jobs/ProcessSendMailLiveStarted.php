<?php

namespace App\Jobs;

use App\Mail\LiveStarted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSendMailLiveStarted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $follower;
    public $room;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($follower, $room)
    {
        $this->follower = $follower;
        $this->room = $room;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // LiveStartedのメールを送信する処理
        $data = [
            'followerName' => $this->follower->followerUser->name,
            'imageUrl' => $this->room->getImagePath(),
            'roomName' => $this->room->name,
            'roomId' => $this->room->id,
            'userName' => $this->room->user->name,
        ];
        \Mail::to($this->follower->followerUser->email)->send(new LiveStarted($data));
        Log::debug($this->follower->followerUser->email);
    }
}
