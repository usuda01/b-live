@extends('layouts.app')
@section('title', '配信スケジュール - ')
@section('content')
    @php
        // 曜日ヘッダー
        $weekdayLabels = ['月', '火', '水', '木', '金', '土', '日'];
        $today = now()->startOfDay();
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $weekStart->copy()->addDays($i);
        }
        // 表示時間帯（24時間）
        $hours = range(0, 23);

        // - 開始時刻のセルに配置し、span（占有行数）を計算
        // - rowspan で複数行にまたがって表示。スキップセルは <td> を描画しない
        // - 日跨ぎの予定は翌日列の0:00から「続き」エントリも配置
        $cellMap = [];      // key: "Y-m-d_H" => 開始時刻のスケジュール配列
        $skipMap = [];      // key: "Y-m-d_H" => true: 上のセルからの rowspan で埋まっている
        $cellSpans = [];    // key: "Y-m-d_H" => そのセルでの最大span

        $placeEntry = function ($entry, $dateKey, $hour, $span) use (&$cellMap, &$skipMap, &$cellSpans) {
            $key = $dateKey . '_' . $hour;
            $cellMap[$key][] = $entry;
            $cellSpans[$key] = max($cellSpans[$key] ?? 1, $span);
            for ($i = 1; $i < $span; $i++) {
                $skipMap[$dateKey . '_' . ($hour + $i)] = true;
            }
        };

        foreach ($schedules as $sch) {
            $start = $sch->scheduled_start_at;
            $end = $sch->scheduled_end_at;
            $startHour = (int) $start->format('G');

            if ($end && !$end->isSameDay($start)) {
                // 日跨ぎ: 開始日と終了日に分けて配置
                $primarySpan = 24 - $startHour;
                $primary = clone $sch;
                $primary->__span = $primarySpan;
                $primary->__continuation = false;
                $placeEntry($primary, $start->format('Y-m-d'), $startHour, $primarySpan);

                $endHour = (int) $end->format('G') + ($end->minute > 0 ? 1 : 0);
                if ($endHour > 0) {
                    $contSpan = min(24, $endHour);
                    $continuation = clone $sch;
                    $continuation->__span = $contSpan;
                    $continuation->__continuation = true;
                    $placeEntry($continuation, $end->format('Y-m-d'), 0, $contSpan);
                }
            } else {
                // 同日または終了時刻なし
                if ($end) {
                    $endHour = (int) $end->format('G') + ($end->minute > 0 ? 1 : 0);
                } else {
                    $endHour = $startHour + 1;
                }
                $span = max(1, min(24 - $startHour, $endHour - $startHour));
                $sch->__span = $span;
                $sch->__continuation = false;
                $placeEntry($sch, $start->format('Y-m-d'), $startHour, $span);
            }
        }
    @endphp

    <div class="schedule-page">
        <div class="schedule-header-bar">
            <h2 class="page-title"><i class="far fa-calendar-alt"></i> 配信スケジュール</h2>
            <div class="page-subtitle">配信予定の一覧</div>
        </div>

        <div class="schedule-week-nav">
            <a href="?week={{ $weekStart->copy()->subWeek()->format('Y-m-d') }}" class="nav-btn">
                <i class="fas fa-chevron-left"></i> 前週
            </a>
            <span class="week-label">
                {{ $weekStart->format('Y/n/j') }} 〜 {{ $weekStart->copy()->addDays(6)->format('n/j') }}
            </span>
            <a href="?week={{ $weekStart->copy()->addWeek()->format('Y-m-d') }}" class="nav-btn">
                次週 <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div class="schedule-timetable-wrapper">
            <table class="schedule-timetable">
                <thead>
                    <tr>
                        <th class="time-col">時刻</th>
                        @foreach ($days as $day)
                            <th class="day-col @if ($day->isToday()) today @endif @if ($day->isSaturday()) saturday @endif @if ($day->isSunday()) sunday @endif">
                                <div class="day-name">
                                    @if ($day->isToday())今日
                                    @else{{ $weekdayLabels[$day->dayOfWeekIso - 1] }}
                                    @endif
                                </div>
                                <div class="day-date">{{ $day->format('n/j') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hours as $h)
                        <tr>
                            <td class="time-cell">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</td>
                            @foreach ($days as $day)
                                @php
                                    $key = $day->format('Y-m-d') . '_' . $h;
                                    $skipped = isset($skipMap[$key]);
                                    $rowspan = $cellSpans[$key] ?? 1;
                                @endphp
                                @continue($skipped)
                                <td class="schedule-cell @if ($day->isToday()) today @endif" @if ($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    @if (isset($cellMap[$key]))
                                        @foreach ($cellMap[$key] as $sch)
                                            @php
                                                $catClass = 'cat-other';
                                                if ($sch->game) {
                                                    $catClass = 'cat-game';
                                                }
                                                $isReminded = in_array($sch->id, $reminderIds, true);
                                                $isLive = $sch->status === \App\Models\StreamSchedule::STATUS_LIVE;
                                            @endphp
                                            <a href="/schedule/{{ $sch->id }}" class="slot {{ $catClass }} @if ($isReminded) reminded @endif @if ($isLive) live @endif @if (!empty($sch->__continuation)) continuation @endif"
                                               style="--span: {{ $sch->__span }};"
                                               title="{{ $sch->title }} / {{ $sch->user->name }}">
                                                @if ($isLive)
                                                    <span class="live-dot"></span>
                                                @endif
                                                @if ($isReminded)
                                                    <i class="fas fa-bell bell"></i>
                                                @endif
                                                @if (!empty($sch->__continuation))
                                                    <div class="slot-time">続き 〜 {{ $sch->scheduled_end_at->format('H:i') }}</div>
                                                @else
                                                    <div class="slot-time">
                                                        {{ $sch->scheduled_start_at->format('H:i') }}
                                                        @if ($sch->scheduled_end_at)
                                                            〜 {{ $sch->getEndTimeLabel() }}
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="slot-title">{{ $sch->title }}</div>
                                                <div class="slot-user">{{ $sch->user->name }}</div>
                                            </a>
                                        @endforeach
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="schedule-legend">
            <div class="legend-item"><span class="swatch cat-game"></span>ゲーム</div>
            <div class="legend-item"><span class="swatch cat-other"></span>その他</div>
            <div class="legend-item"><span class="swatch reminded-mark"></span>リマインドON</div>
            <div class="legend-item"><span class="swatch live-mark"></span>配信中</div>
        </div>

        @if ($schedules->isEmpty())
            <div class="no-schedules">
                <p>この週には予定がまだありません</p>
            </div>
        @endif
    </div>
@endsection
