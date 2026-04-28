<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamScheduleRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_schedule_reminders', function (Blueprint $table) {
            $table->increments('id')->comment('リマインドID');
            $table->integer('schedule_id')->unsigned()->comment('配信予定ID');
            $table->integer('user_id')->unsigned()->comment('リマインド購読者のユーザーID');
            $table->dateTime('notified_at')->nullable()->comment('通知済み日時（NULLなら未通知）');
            $table->timestamps();

            $table->foreign('schedule_id', 'reminders_schedule_id_foreign')
                ->references('id')->on('stream_schedules')
                ->onDelete('cascade');
            $table->foreign('user_id', 'reminders_user_id_foreign')
                ->references('id')->on('users');

            $table->unique(['schedule_id', 'user_id'], 'reminders_schedule_user_unique');
            $table->index('notified_at', 'reminders_notified_at_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stream_schedule_reminders');
    }
}
