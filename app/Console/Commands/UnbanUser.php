<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UnbanUser extends Command
{
    protected $signature = 'user:unban {userId : BAN を解除するユーザーID}';

    protected $description = '指定したユーザーのBANを解除する';

    public function handle()
    {
        $userId = (int) $this->argument('userId');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User ID {$userId} が見つかりません");
            return 1;
        }

        if ($user->isActive()) {
            $this->warn("User ID {$userId} ({$user->name}) は既にアクティブ状態です");
            return 0;
        }

        $user->account_status = User::ACCOUNT_STATUS_ACTIVE;
        $user->account_status_changed_at = now();
        $user->save();

        $this->info("User ID {$userId} ({$user->name}) のBANを解除しました");
        return 0;
    }
}
