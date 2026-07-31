@php
    $calendarDate = now();
    $monthStart = $calendarDate->copy()->startOfMonth();
    $daysInMonth = $calendarDate->daysInMonth;
    $leadingBlankDays = $monthStart->dayOfWeek;
    $calendarEvents = collect($calendar_events ?? []);
    $upcomingEventList = collect($upcoming_events ?? []);
    $eventsByDate = $calendarEvents->groupBy(function ($event) {
        return \Carbon\Carbon::parse($event->event_date)->format('Y-m-d');
    });
@endphp

<div class="col-xl-4 col-12">
    <div class="box dashboard-event-calendar">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <div>
                <h4 class="box-title mb-0">{{ __('ui.calendar') }}</h4>
                <small class="subtitle">{{ $calendarDate->format('F Y') }}</small>
            </div>
            @if(Auth::user()->role == 'Admin' || Auth::user()->hasRole('Admin'))
                <a href="{{ route('event.view') }}" class="btn btn-sm btn-info-light">{{ __('ui.manage') }}</a>
            @endif
        </div>

        <div class="box-body">
            <div class="event-calendar-grid">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                    <div class="event-calendar-weekday">{{ $dayName }}</div>
                @endforeach

                @for($cell = 0; $cell < 42; $cell++)
                    @php
                        $dayNumber = $cell - $leadingBlankDays + 1;
                        $isInMonth = $dayNumber >= 1 && $dayNumber <= $daysInMonth;
                        $dateKey = $isInMonth ? $calendarDate->copy()->day($dayNumber)->format('Y-m-d') : null;
                        $dayEvents = $dateKey ? $eventsByDate->get($dateKey, collect()) : collect();
                        $hasEvent = $dayEvents->isNotEmpty();
                        $isToday = $dateKey === now()->format('Y-m-d');
                    @endphp

                    <div class="event-calendar-day {{ !$isInMonth ? 'is-empty' : '' }} {{ $hasEvent ? 'has-event' : '' }} {{ $isToday ? 'is-today' : '' }}"
                         title="{{ $hasEvent ? $dayEvents->pluck('title')->implode(', ') : '' }}">
                        @if($isInMonth)
                            <span>{{ $dayNumber }}</span>
                        @endif
                    </div>
                @endfor
            </div>

            <div class="event-list mt-20">
                @forelse($upcomingEventList as $event)
                    <div class="event-list-item">
                        <div class="event-date-pill">
                            <strong>{{ date('d', strtotime($event->event_date)) }}</strong>
                            <span>{{ date('M', strtotime($event->event_date)) }}</span>
                        </div>
                        <div class="event-list-copy">
                            <p class="mb-0 font-weight-600">{{ $event->title }}</p>
                            <small>
                                {{ $event->event_time ? date('H:i', strtotime($event->event_time)) : __('ui.all_day') }}
                                @if($event->location)
                                    <span class="mx-5">|</span>{{ $event->location }}
                                @endif
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="event-empty-state">{{ __('ui.no_upcoming_events') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-event-calendar .box-body {
        padding-top: 14px;
    }

    .event-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 6px;
    }

    .event-calendar-weekday {
        color: #64748b !important;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
    }

    .event-calendar-day {
        align-items: center;
        aspect-ratio: 1;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #334155 !important;
        display: flex;
        font-size: 13px;
        font-weight: 700;
        justify-content: center;
        min-height: 34px;
        position: relative;
    }

    .event-calendar-day.is-empty {
        background: transparent;
        border-color: transparent;
    }

    .event-calendar-day.is-today {
        border-color: #0b1f3a;
        color: #0b1f3a !important;
    }

    .event-calendar-day.has-event {
        background: linear-gradient(135deg, #0b1f3a, #164e78);
        border-color: #164e78;
        color: #ffffff !important;
        box-shadow: 0 8px 16px rgba(11, 31, 58, 0.18);
    }

    .event-calendar-day.has-event::after {
        background: #38bdf8;
        border-radius: 999px;
        bottom: 5px;
        content: "";
        height: 4px;
        position: absolute;
        width: 16px;
    }

    .event-list {
        display: grid;
        gap: 10px;
    }

    .event-list-item {
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        display: flex;
        gap: 12px;
        padding: 10px;
    }

    .event-date-pill {
        align-items: center;
        background: #e0f2fe;
        border-radius: 6px;
        color: #0b1f3a !important;
        display: flex;
        flex-direction: column;
        flex: 0 0 46px;
        justify-content: center;
        min-height: 46px;
    }

    .event-date-pill strong,
    .event-date-pill span,
    .event-list-copy p,
    .event-list-copy small,
    .event-empty-state {
        color: inherit !important;
    }

    .event-date-pill span {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .event-list-copy {
        color: #334155 !important;
        min-width: 0;
    }

    .event-list-copy p {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .event-list-copy small,
    .event-empty-state {
        color: #64748b !important;
    }

    .event-empty-state {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 6px;
        font-weight: 600;
        padding: 14px;
        text-align: center;
    }

    body.dark-skin .event-calendar-weekday,
    body.dark-skin .event-list-copy small,
    body.dark-skin .event-empty-state {
        color: #8a99b5 !important;
    }

    body.dark-skin .event-calendar-day {
        background: #272e48;
        border-color: rgba(255, 255, 255, 0.12);
        color: #e1e6f2 !important;
    }

    body.dark-skin .event-calendar-day.is-empty {
        background: transparent;
        border-color: transparent;
    }

    body.dark-skin .event-list-item,
    body.dark-skin .event-empty-state {
        background: #272e48;
        border-color: rgba(255, 255, 255, 0.12);
    }

    body.dark-skin .event-list-copy {
        color: #e1e6f2 !important;
    }
</style>
