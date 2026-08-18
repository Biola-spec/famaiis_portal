@php
    $calendarDate = now();
    $monthStart = $calendarDate->copy()->startOfMonth();
    $leadingBlankDays = $monthStart->dayOfWeek;
    $daysInMonth = $calendarDate->daysInMonth;
    $calendarEvents = collect($calendar_events ?? []);
    $upcomingEventList = collect($upcoming_events ?? []);
    $eventsByDate = $calendarEvents->groupBy(fn ($event) => \Carbon\Carbon::parse($event->event_date)->format('Y-m-d'));
    $timetable = collect($timetable_entries ?? []);
    $timetableGroups = $timetable->groupBy(fn ($entry) => optional($entry->section)->name ?: 'All Sections');
    $teacherTimetable = collect($teacher_timetable_entries ?? []);
    $isTeacher = Auth::user()->role === 'Teacher' || Auth::user()->role === 'Staff' || Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('Staff');
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
@endphp

<div class="col-12">
    <div class="box school-schedule-widget">
        <div class="box-header with-border d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h4 class="box-title mb-0">School Calendar & Timetable</h4>
                <small class="subtitle">Events and subject periods published by the Admin</small>
            </div>
            @if(Auth::user()->hasRole('Admin'))
                <div class="d-flex gap-2">
                    <a href="{{ route('event.view') }}" class="btn btn-sm btn-info-light"><i class="fa fa-calendar"></i> Manage Calendar</a>
                    <a href="{{ route('timetable.index') }}" class="btn btn-sm btn-primary"><i class="fa fa-clock-o"></i> Manage Timetable</a>
                </div>
            @endif
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xl-4 col-12 mb-20 mb-xl-0">
                    <div class="schedule-calendar-title">{{ $calendarDate->format('F Y') }}</div>
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
                            @endphp
                            <div class="event-calendar-day {{ !$isInMonth ? 'is-empty' : '' }} {{ $dayEvents->isNotEmpty() ? 'has-event' : '' }} {{ $dateKey === now()->format('Y-m-d') ? 'is-today' : '' }}" title="{{ $dayEvents->pluck('title')->implode(', ') }}">
                                @if($isInMonth)<span>{{ $dayNumber }}</span>@endif
                            </div>
                        @endfor
                    </div>
                    <div class="schedule-event-list mt-15">
                        @forelse($upcomingEventList as $event)
                            <div class="schedule-event-row">
                                <strong>{{ \Carbon\Carbon::parse($event->event_date)->format('d M') }}</strong>
                                <span>{{ $event->title }} <small>{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('h:i A') : 'All day' }}</small></span>
                            </div>
                        @empty
                            <div class="text-muted">No upcoming school events.</div>
                        @endforelse
                    </div>
                </div>
                <div class="col-xl-8 col-12">
                    @if($isTeacher)
                        <div class="teacher-schedule-panel">
                            <div class="teacher-schedule-heading"><div><span class="modal-eyebrow">MY TEACHING SCHEDULE</span><h5>Classes I Am Taking</h5></div><span class="teacher-period-count">{{ $teacherTimetable->count() }} periods</span></div>
                            <div class="table-responsive schedule-table-wrap">
                                <table class="table table-bordered table-sm mb-0 schedule-modal-table"><thead><tr><th>Day</th><th>Time</th><th>Section / Class</th><th>Subject</th><th>Room</th></tr></thead><tbody>
                                @forelse($teacherTimetable as $entry)<tr><td><strong>{{ $entry->day_of_week }}</strong></td><td>{{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}</td><td>{{ optional($entry->section)->name ?: 'All sections' }} / {{ optional($entry->studentClass)->name }}</td><td>{{ optional($entry->subject)->name }}</td><td>{{ $entry->room ?: '-' }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted">No teaching periods have been assigned to you yet.</td></tr>@endforelse
                                </tbody></table>
                            </div>
                        </div>
                    @endif
                    <h5 class="all-sections-heading">View Section Timetables</h5>
                    @forelse($timetableGroups as $sectionName => $sectionEntries)
                        <button type="button" class="section-timetable-button" data-timetable-modal="timetable-modal-{{ $loop->index }}">
                            <span class="section-timetable-icon"><i class="fa fa-calendar"></i></span>
                            <span><strong>{{ $sectionName }}</strong><small>{{ $sectionEntries->count() }} subject period(s)</small></span>
                            <i class="fa fa-chevron-right section-timetable-arrow"></i>
                        </button>
                    @empty
                        <div class="text-center text-muted p-20">The Admin has not published a timetable yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($timetableGroups as $sectionName => $sectionEntries)
    <div class="school-timetable-modal" id="timetable-modal-{{ $loop->index }}" aria-hidden="true">
        <div class="school-timetable-modal-backdrop" data-timetable-close></div>
        <div class="school-timetable-dialog" role="dialog" aria-modal="true" aria-labelledby="timetable-title-{{ $loop->index }}">
            <div class="school-timetable-modal-header">
                <div><span class="modal-eyebrow">WEEKLY SCHEDULE</span><h4 id="timetable-title-{{ $loop->index }}">{{ $sectionName }} Timetable</h4></div>
                <button type="button" class="school-timetable-close" data-timetable-close aria-label="Close timetable"><i class="fa fa-times"></i></button>
            </div>
            <div class="school-timetable-modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 schedule-modal-table">
                        <thead><tr><th>Day</th><th>Time</th><th>Class</th><th>Subject</th><th>Teacher</th><th>Room</th></tr></thead>
                        <tbody>@foreach($sectionEntries as $entry)<tr><td><strong>{{ $entry->day_of_week }}</strong></td><td>{{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}</td><td>{{ optional($entry->studentClass)->name }}</td><td>{{ optional($entry->subject)->name }}</td><td>{{ optional($entry->teacher)->name ?: 'Unassigned' }}</td><td>{{ $entry->room ?: '-' }}</td></tr>@endforeach</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
    .school-schedule-widget .event-calendar-grid { display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); gap:4px; }
    .school-schedule-widget .event-calendar-weekday { color:#64748b; font-size:10px; font-weight:700; text-align:center; padding:4px 0; }
    .school-schedule-widget .event-calendar-day { min-height:29px; border:1px solid #e5e7eb; border-radius:3px; padding:5px; font-size:11px; text-align:center; }
    .school-schedule-widget .event-calendar-day.is-empty { border-color:transparent; background:transparent; }
    .school-schedule-widget .event-calendar-day.has-event { background:#e8f2fc; color:#1d4ed8; font-weight:700; }
    .school-schedule-widget .event-calendar-day.is-today { outline:2px solid #2e86de; outline-offset:-2px; }
    .schedule-calendar-title { font-size:16px; font-weight:700; margin-bottom:8px; }
    .section-timetable-button { width:100%; display:flex; align-items:center; gap:10px; border:1px solid #dbe5f0; border-radius:7px; background:#fff; padding:12px 14px; margin-bottom:9px; text-align:left; color:#1e2e4a; transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease; cursor:pointer; }
    .section-timetable-button:hover { transform:translateY(-1px); border-color:#2e86de; box-shadow:0 5px 14px rgba(46,134,222,.14); }.section-timetable-button strong,.section-timetable-button small { display:block; }.section-timetable-button strong { font-size:12px; }.section-timetable-button small { color:#64748b; font-size:10px; margin-top:2px; }.section-timetable-icon { width:30px; height:30px; display:grid; place-items:center; border-radius:6px; color:#2563eb; background:#e8f2fc; font-size:13px; }.section-timetable-arrow { margin-left:auto; color:#94a3b8; font-size:11px; }
    .school-timetable-modal { display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:20px; }.school-timetable-modal.is-open { display:flex; }.school-timetable-modal-backdrop { position:absolute; inset:0; background:rgba(8,15,30,.72); backdrop-filter:blur(3px); }.school-timetable-dialog { position:relative; z-index:1; width:min(940px, 100%); max-height:min(680px, 90vh); overflow:hidden; border-radius:10px; background:#fff; box-shadow:0 20px 60px rgba(0,0,0,.28); }.school-timetable-modal-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:#132a46; color:#fff; }.school-timetable-modal-header h4 { margin:3px 0 0; color:#fff; font-size:18px; }.modal-eyebrow { font-size:9px; letter-spacing:1.2px; color:#9bc7f4; }.school-timetable-close { border:0; width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,.12); color:#fff; cursor:pointer; }.school-timetable-close:hover { background:#e66767; }.school-timetable-modal-body { max-height:calc(min(680px, 90vh) - 86px); overflow:auto; padding:18px 22px; }.schedule-modal-table th { font-size:10px; white-space:nowrap; background:#f1f6fb; }.schedule-modal-table td { font-size:11px; padding:7px 8px; vertical-align:middle; }
    .teacher-schedule-panel { margin-bottom:18px; padding:12px; border:1px solid #dbe5f0; border-radius:7px; background:#f8fbff; }.teacher-schedule-heading { display:flex; align-items:center; justify-content:space-between; margin-bottom:7px; }.teacher-schedule-heading h5,.all-sections-heading { margin:3px 0 8px; font-size:12px; font-weight:700; }.teacher-period-count { color:#2563eb; font-size:10px; font-weight:700; }.all-sections-heading { color:#334155; }
    body.dark-skin .section-timetable-button { background:#172131; border-color:#334155; color:#e5e7eb; }.dark-skin .teacher-schedule-panel { background:#172131; border-color:#334155; }.dark-skin .all-sections-heading { color:#e5e7eb; }.dark-skin .school-timetable-dialog { background:#111827; }.dark-skin .schedule-modal-table th { background:#1f2937; color:#e5e7eb; }.dark-skin .schedule-modal-table td { color:#d1d5db; }
    .schedule-event-row { display:flex; gap:10px; padding:7px 0; border-bottom:1px solid #e5e7eb; font-size:12px; }
    .schedule-event-row strong { min-width:42px; color:#2563eb; }
    .schedule-event-row span { flex:1; }.schedule-event-row small { display:block; color:#64748b; margin-top:2px; }
    .schedule-table-wrap { max-height:320px; overflow:auto; }.schedule-table-wrap th { white-space:nowrap; font-size:10px; }.schedule-table-wrap td { font-size:10px; vertical-align:middle; padding:5px 6px; }
    body.dark-skin .school-schedule-widget .event-calendar-day { border-color:#374151; }.gap-2 { gap:8px; }
    body.timetable-modal-open { overflow:hidden; }
</style>

<script>
    (function () {
        const openModal = function (id) { const modal = document.getElementById(id); if (modal) { modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); document.body.classList.add('timetable-modal-open'); } };
        const closeModal = function (modal) { if (modal) { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); document.body.classList.remove('timetable-modal-open'); } };
        document.querySelectorAll('[data-timetable-modal]').forEach(function (button) { button.addEventListener('click', function () { openModal(button.dataset.timetableModal); }); });
        document.querySelectorAll('[data-timetable-close]').forEach(function (button) { button.addEventListener('click', function () { closeModal(button.closest('.school-timetable-modal')); }); });
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape') document.querySelectorAll('.school-timetable-modal.is-open').forEach(closeModal); });
    })();
</script>
