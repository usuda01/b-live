<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountStatusToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('account_status')->unsigned()->default(1)->after('status')->comment('1:通常/2:BAN');
            $table->timestamp('account_status_changed_at')->nullable()->after('account_status')->comment('account_status の最終変更日時');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_status', 'account_status_changed_at']);
        });
    }
}
