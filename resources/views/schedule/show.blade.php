@extends('layouts.app')
@section('title', $schedule->title . ' - 配信予定 - ')
@section('content')
    @php
        $reminderCount = $schedule->reminders()->count();
    @endphp

    <div class="schedule-detail">
        <div class="breadcrumbs">
            <a href="/schedule">配信スケジュール</a> &gt; {{ $schedule->title }}
        </div>

        @if ($activeRoom)
            <div class="live-banner">
                <span class="live-icon"></span>
                <span class="live-text">{{ $schedule->user->name }}さんの配信が始まっています</span>
                <a class="live-btn" href="/room/{{ $activeRoom->id }}">
                    <i class="fas fa-broadcast-tower"></i> 視聴する
                </a>
            </div>
        @endif

        <div class="schedule-card">
            <img class="thumb" src="{{ $schedule->getThumbnailPath() }}" alt="">
            <div class="body">
                <div class="schedule-time">
                    <i class="far fa-clock"></i>
                    {{ $schedule->scheduled_start_at->format('Y年n月j日 (D) H:i') }}
                    @if ($schedule->scheduled_end_at)
                        〜 {{ $schedule->getEndTimeLabel() }}
                    @endif
                </div>
                <div class="schedule-title">{{ $schedule->title }}</div>
                <div class="schedule-meta">
                    <img class="user-avatar" src="{{ $schedule->user->getImagePath() }}" alt="">
                    <div class="user-name">
                        <a href="/user/{{ $schedule->user->id }}">{{ $schedule->user->name }}</a>
                    </div>
                    @if ($schedule->game)
                        <span class="game-tag">{{ $schedule->game->name }}</span>
                    @endif
                </div>
                @if ($schedule->description)
                    <div class="schedule-desc">{!! nl2br(e($schedule->description)) !!}</div>
                @endif

                <div class="reminder-action">
                    @auth
                        @if ($schedule->status === \App\Models\StreamSchedule::STATUS_PUBLISHED)
                            <button id="reminder-btn" class="btn-bell @if (!$isReminded) off @endif"
                                    data-schedule-id="{{ $schedule->id }}"
                                    data-state="{{ $isReminded ? 'on' : 'off' }}"
                                    onclick="toggleReminder(this)">
                                @if ($isReminded)
                                    <i class="fas fa-bell"></i> リマインドON
                                @else
                                    <i class="far fa-bell"></i> リマインドする
                                @endif
                            </button>
                        @endif
                    @else
                        <a class="btn-bell off" href="/login"><i class="far fa-bell"></i> ログインしてリマインド</a>
                    @endauth
                    <span class="reminder-count" id="reminder-count">
                        <i class="fas fa-bell" style="color:#f5b400;"></i>
                        <span id="reminder-count-num">{{ $reminderCount }}</span>人がリマインド中
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    async function toggleReminder(btn) {
        const id = btn.getAttribute('data-schedule-id');
        const state = btn.getAttribute('data-state');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        btn.disabled = true;
        try {
            const res = await fetch(`/api/schedule/${id}/reminder`, {
                method: state === 'on' ? 'DELETE' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('リクエスト失敗');
            const newState = state === 'on' ? 'off' : 'on';
            btn.setAttribute('data-state', newState);
            const countEl = document.getElementById('reminder-count-num');
            const current = parseInt(countEl.textContent, 10) || 0;
            countEl.textContent = newState === 'on' ? current + 1 : Math.max(0, current - 1);
            if (newState === 'on') {
                btn.classList.remove('off');
                btn.innerHTML = '<i class="fas fa-bell"></i> リマインドON';
            } else {
                btn.classList.add('off');
                btn.innerHTML = '<i class="far fa-bell"></i> リマインドする';
            }
        } catch (e) {
            alert('リマインドの切替に失敗しました。再度お試しください。');
        } finally {
            btn.disabled = false;
        }
    }
</script>
@endpush
