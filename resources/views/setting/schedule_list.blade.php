@extends('layouts.app')
@section('title', '配信予定 - ')
@section('content')
    <div class="setting-schedule-list">
        @include('parts.mypage_menu')
        <div class="main-content">
            <h2>配信予定</h2>

            <div class="notice">
                <ul>
                    <li>登録できる予定は{{ $maxPerUser }}件までです（公開予定+下書きの合計）。</li>
                    <li>3ヶ月先までの予定を登録できます。</li>
                </ul>
            </div>

            @if (session('flash_message'))
                <div class="flash_message">{{ session('flash_message') }}</div>
            @endif
            @if ($errors->any())
                <ul class="error">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="schedule-actions">
                <button type="button" class="btn-default" onclick="openCreateModal()">
                    <i class="far fa-calendar-plus"></i> 新しい予定を追加
                </button>
            </div>

            <div class="schedule-list-content">
                @if ($schedules->isEmpty())
                    <div class="no-content">
                        <p>まだ予定が登録されていません</p>
                        <p>「新しい予定を追加」から登録してください</p>
                    </div>
                @else
                    <div class="schedule-header">
                        <div class="column thumb">サムネ</div>
                        <div class="column when">配信日時</div>
                        <div class="column title">タイトル</div>
                        <div class="column status">ステータス</div>
                        <div class="column reminders">リマインド</div>
                        <div class="column actions">操作</div>
                    </div>
                    @foreach ($schedules as $schedule)
                        <div class="schedule-row">
                            <div class="thumb">
                                <img src="{{ $schedule->getThumbnailPath() }}">
                            </div>
                            <div class="when">
                                <div class="date">{{ $schedule->scheduled_start_at->format('Y/m/d') }}</div>
                                <div class="time">
                                    {{ $schedule->scheduled_start_at->format('H:i') }}
                                    @if ($schedule->scheduled_end_at)
                                        〜 {{ $schedule->getEndTimeLabel() }}
                                    @endif
                                </div>
                            </div>
                            <div class="title">
                                <div class="name">{{ $schedule->title }}</div>
                                @if ($schedule->game)
                                    <div class="game">{{ $schedule->game->name }}</div>
                                @endif
                            </div>
                            <div class="status">
                                @switch($schedule->status)
                                    @case(\App\Models\StreamSchedule::STATUS_PUBLISHED)
                                        <span class="status-tag published">公開</span>
                                        @break
                                    @case(\App\Models\StreamSchedule::STATUS_PRIVATE)
                                        <span class="status-tag private">非公開</span>
                                        @break
                                    @case(\App\Models\StreamSchedule::STATUS_CANCELLED)
                                        <span class="status-tag cancelled">キャンセル</span>
                                        @break
                                    @case(\App\Models\StreamSchedule::STATUS_LIVE)
                                        <span class="status-tag live">配信中</span>
                                        @break
                                    @case(\App\Models\StreamSchedule::STATUS_FINISHED)
                                        <span class="status-tag finished">終了</span>
                                        @break
                                @endswitch
                            </div>
                            <div class="reminders">
                                <i class="fas fa-bell"></i> {{ $schedule->reminders()->count() }}人
                            </div>
                            <div class="actions">
                                @if (!in_array($schedule->status, [\App\Models\StreamSchedule::STATUS_LIVE, \App\Models\StreamSchedule::STATUS_FINISHED], true))
                                    <button type="button" class="btn-edit" onclick='openEditModal(@json($schedule))'>編集</button>
                                @endif
                                <form method="post" action="/setting/schedules" style="display:inline;" onsubmit="return confirm('この予定を複製しますか？（非公開状態で作成されます）');">
                                    @csrf
                                    <input type="hidden" name="mode" value="duplicate">
                                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                    <button type="submit" class="btn-edit">複製</button>
                                </form>
                                @if ($schedule->status !== \App\Models\StreamSchedule::STATUS_LIVE)
                                    <form method="post" action="/setting/schedules" style="display:inline;" onsubmit="return confirm('この予定を削除しますか？\n（リマインド登録者には通知されません）');">
                                        @csrf
                                        <input type="hidden" name="mode" value="delete">
                                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                        <button type="submit" class="btn-delete">削除</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- 登録/編集モーダル --}}
    <div class="schedule-modal" id="schedule-modal" style="display:none;">
        <div class="schedule-modal-backdrop" onclick="closeModal()"></div>
        <div class="schedule-modal-content">
            <form method="post" action="/setting/schedules" enctype="multipart/form-data" id="schedule-form">
                @csrf
                <input type="hidden" name="mode" id="form-mode" value="create">
                <input type="hidden" name="schedule_id" id="form-schedule-id" value="">

                <div class="schedule-modal-header">
                    <h3 id="modal-title">配信予定を追加</h3>
                    <button type="button" class="close" onclick="closeModal()">&times;</button>
                </div>

                <div class="schedule-modal-body">
                    <div id="copy-prev-area" style="margin-bottom:14px;">
                        @if ($schedules->isNotEmpty())
                            <button type="button" class="btn-copy-prev" onclick="copyPrev()">
                                <i class="fas fa-copy"></i> 直近の予定からコピー
                            </button>
                        @endif
                    </div>

                    <div class="form-row">
                        <label>タイトル <span class="required">*</span></label>
                        <input type="text" name="title" id="form-title" maxlength="64" required placeholder="例：ゲーム実況">
                    </div>

                    <div class="form-row form-row-2col">
                        <div>
                            <label>配信日 <span class="required">*</span></label>
                            <input type="date" name="scheduled_start_date" id="form-start-date" required>
                        </div>
                        <div>
                            <label>開始時刻 <span class="required">*</span></label>
                            <input type="time" name="scheduled_start_time" id="form-start-time" required>
                        </div>
                    </div>

                    <div class="form-row form-row-2col">
                        <div>
                            <label>終了予定時刻（任意）</label>
                            <input type="time" name="scheduled_end_time" id="form-end-time">
                        </div>
                        <div>
                            <label>カテゴリ（任意）</label>
                            <select name="game_id" id="form-game-id">
                                <option value="">未選択</option>
                                @foreach ($games as $game)
                                    <option value="{{ $game->id }}">{{ $game->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <label>説明（任意）</label>
                        <textarea name="description" id="form-description" maxlength="1000" rows="3" placeholder="配信内容の詳細"></textarea>
                    </div>

                    <div class="form-row">
                        <label>サムネイル（任意）</label>
                        <input type="file" name="thumbnail" id="form-thumbnail" accept="image/*">
                        <div class="form-help">未設定の場合、プロフィール画像を使用します</div>
                    </div>

                    <div class="form-row">
                        <label>公開設定</label>
                        <div class="radio-group">
                            <label class="radio">
                                <input type="radio" name="status" value="1" id="form-status-1" checked>
                                公開（誰でも見られる）
                            </label>
                            <label class="radio">
                                <input type="radio" name="status" value="2" id="form-status-2">
                                非公開（下書き）
                            </label>
                        </div>
                    </div>

                    {{-- 結合用の隠しフィールド --}}
                    <input type="hidden" name="scheduled_start_at" id="form-start-at">
                    <input type="hidden" name="scheduled_end_at" id="form-end-at">
                </div>

                <div class="schedule-modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">キャンセル</button>
                    <button type="submit" class="btn-default" onclick="return prepareSubmit()">保存</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@php
    $copySources = $schedules->take(1)->map(function ($s) {
        return [
            'title' => $s->title,
            'description' => $s->description,
            'game_id' => $s->game_id,
        ];
    })->values();
@endphp

@push('scripts')
<script>
    const SCHEDULES_FOR_COPY = {!! json_encode($copySources) !!};

    function openCreateModal() {
        document.getElementById('modal-title').textContent = '配信予定を追加';
        document.getElementById('form-mode').value = 'create';
        document.getElementById('form-schedule-id').value = '';
        document.getElementById('schedule-form').reset();
        document.getElementById('schedule-modal').style.display = 'flex';
    }

    function openEditModal(schedule) {
        document.getElementById('modal-title').textContent = '配信予定を編集';
        document.getElementById('form-mode').value = 'edit';
        document.getElementById('form-schedule-id').value = schedule.id;
        document.getElementById('form-title').value = schedule.title || '';
        document.getElementById('form-description').value = schedule.description || '';
        document.getElementById('form-game-id').value = schedule.game_id || '';

        if (schedule.scheduled_start_at) {
            const start = new Date(schedule.scheduled_start_at);
            document.getElementById('form-start-date').value = formatDate(start);
            document.getElementById('form-start-time').value = formatTime(start);
        }
        if (schedule.scheduled_end_at) {
            const end = new Date(schedule.scheduled_end_at);
            document.getElementById('form-end-time').value = formatTime(end);
        } else {
            document.getElementById('form-end-time').value = '';
        }

        document.getElementById('form-status-' + schedule.status).checked = true;
        document.getElementById('schedule-modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('schedule-modal').style.display = 'none';
    }

    function copyPrev() {
        if (SCHEDULES_FOR_COPY.length === 0) return;
        const prev = SCHEDULES_FOR_COPY[0];
        document.getElementById('form-title').value = prev.title || '';
        document.getElementById('form-description').value = prev.description || '';
        if (prev.game_id) document.getElementById('form-game-id').value = prev.game_id;
    }

    function prepareSubmit() {
        const date = document.getElementById('form-start-date').value;
        const startTime = document.getElementById('form-start-time').value;
        const endTime = document.getElementById('form-end-time').value;
        if (!date || !startTime) {
            alert('配信日と開始時刻を入力してください');
            return false;
        }
        document.getElementById('form-start-at').value = date + ' ' + startTime + ':00';
        if (endTime) {
            // 終了時刻が開始時刻以前なら翌日扱い (例: 23:00開始 → 01:00終了)
            let endDate = date;
            if (endTime <= startTime) {
                const d = new Date(date + 'T00:00:00');
                d.setDate(d.getDate() + 1);
                endDate = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            }
            document.getElementById('form-end-at').value = endDate + ' ' + endTime + ':00';
        } else {
            document.getElementById('form-end-at').value = '';
        }
        return true;
    }

    function formatDate(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    function formatTime(d) {
        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    }
</script>
@endpush
