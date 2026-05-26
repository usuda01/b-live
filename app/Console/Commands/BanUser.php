<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BanUser extends Command
{
    protected $signature = 'user:ban {userId : BAN するユーザーID}';

    protected $description = '指定したユーザーをBAN状態にする';

    public function handle()
    {
        $userId = (int) $this->argument('userId');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User ID {$userId} が見つかりません");
            return 1;
        }

        if ($user->isBanned()) {
            $this->warn("User ID {$userId} ({$user->name}) は既にBAN状態です");
            return 0;
        }

        $user->account_status = User::ACCOUNT_STATUS_BANNED;
        $user->account_status_changed_at = now();
        $user->save();

        $this->info("User ID {$userId} ({$user->name}) をBANしました");
        return 0;
    }
}
