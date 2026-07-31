@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ $subject->name }} — FamaiisStudyHub</h3>
                            <a href="{{ route('learnhub.index') }}" class="btn btn-sm btn-default" style="float:right">← Back to Subjects</a>
                        </div>
                        <div class="box-body">
                            @if(session('message'))
                                <div class="alert alert-success">{{ session('message') }}</div>
                            @endif

                            <ul class="nav nav-tabs mb-4">
                                <li class="{{ $tab === 'lessons' ? 'active' : '' }}">
                                    <a href="{{ route('learnhub.manage', ['id' => $subject->id, 'tab' => 'lessons']) }}">Lessons</a>
                                </li>
                                <li class="{{ $tab === 'live' ? 'active' : '' }}">
                                    <a href="{{ route('learnhub.manage', ['id' => $subject->id, 'tab' => 'live']) }}">Live Video</a>
                                </li>
                                <li class="{{ $tab === 'progress' ? 'active' : '' }}">
                                    <a href="{{ route('learnhub.manage', ['id' => $subject->id, 'tab' => 'progress']) }}">Progress</a>
                                </li>
                            </ul>

                            @if($tab === 'live')
                                <button type="button" class="btn btn-sm btn-success mb-3" data-toggle="modal" data-target="#liveSessionModal">+ Schedule Live Video</button>

                                @forelse($subject->liveSessions as $session)
                                <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <strong>{{ $session->title }}</strong>
                                        @if($session->lesson)
                                            <br><small class="text-muted">Linked to: {{ $session->lesson->title }}</small>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            Created: {{ $session->created_at->format('d M Y, h:i A') }}
                                            @if($session->scheduled_at)
                                                · Scheduled: {{ $session->scheduled_at->format('d M Y, h:i A') }}
                                            @endif
                                            @if($session->started_at)
                                                · Started: {{ $session->started_at->format('d M Y, h:i A') }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="mt-2 mt-md-0">
                                        @if($session->status === 'live')
                                            <span class="badge badge-danger mr-2">LIVE</span>
                                            <a href="{{ route('learnhub.live.join', $session->id) }}" class="btn btn-xs btn-success">Join Room</a>
                                            <form action="{{ route('learnhub.live.end', [$subject->id, $session->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-danger">End</button>
                                            </form>
                                        @elseif($session->status === 'scheduled')
                                            <span class="badge badge-info mr-2">Scheduled</span>
                                            <form action="{{ route('learnhub.live.start', [$subject->id, $session->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success">Start Now</button>
                                            </form>
                                        @else
                                            <span class="badge badge-secondary">Ended {{ $session->ended_at?->format('d M, h:i A') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                    <p class="text-muted">No live video sessions yet. Schedule one for students to join and interact with you in real time.</p>
                                @endforelse

                                <div class="modal fade" id="liveSessionModal">
                                    <div class="modal-dialog">
                                        <form action="{{ route('learnhub.live.store', $subject->id) }}" method="POST" class="modal-content">
                                            @csrf
                                            <div class="modal-header"><h4>Schedule Live Video Session</h4></div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Session Title</label>
                                                    <input type="text" name="title" class="form-control" required placeholder="e.g. Week 3 Q&A Live Class">
                                                </div>
                                                <div class="form-group">
                                                    <label>Link to Lesson (optional)</label>
                                                    <select name="lesson_id" class="form-control">
                                                        <option value="">— General subject session —</option>
                                                        @foreach($lessonsForLive as $lesson)
                                                            <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Schedule For (optional)</label>
                                                    <input type="datetime-local" name="scheduled_at" class="form-control">
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="start_now" value="1" id="startNow" class="form-check-input">
                                                    <label for="startNow" class="form-check-label">Start live session immediately</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Create Session</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            @elseif($tab === 'lessons')
                                <button type="button" class="btn btn-sm btn-primary mb-3" data-toggle="modal" data-target="#weekModal">+ Add Week</button>

                                @forelse($subject->weeks as $week)
                                <div class="box box-body b-1 mb-3">
                                    <h5 class="d-flex justify-content-between align-items-center flex-wrap">
                                        <span>
                                            <span class="badge badge-info">Week {{ $week->week_number }}</span> 
                                            {{ $week->title }}
                                        </span>
                                        @if($week->lessons->isNotEmpty())
                                            <span class="text-muted font-size-12">
                                                <i class="fa fa-calendar"></i> Note Uploaded: {{ $week->lessons->first()->created_at->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted font-size-12">
                                                <i class="fa fa-calendar-o"></i> No Note Uploaded
                                            </span>
                                        @endif
                                    </h5>
                                    @foreach($week->lessons as $lesson)
                                        <div class="border rounded p-3 mb-2 bg-light">
                                            <strong>{{ $lesson->title }}</strong>
                                            <p class="text-muted small mb-1">
                                                Uploaded: {{ $lesson->created_at->format('d M Y, h:i A') }}
                                                @if($lesson->updated_at->gt($lesson->created_at))
                                                    · Updated: {{ $lesson->updated_at->format('d M Y, h:i A') }}
                                                @endif
                                            </p>
                                            <p class="text-muted small mb-2">{{ Str::limit($lesson->content, 120) }}</p>
                                            <button type="button" class="btn btn-xs btn-default" data-toggle="modal" data-target="#lessonModal{{ $lesson->id }}">Edit Lesson</button>
                                            <form action="{{ route('learnhub.quiz.generate', [$subject->id, $lesson->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-info">Generate Quiz ({{ $lesson->cbtQuestions->count() }} Qs)</button>
                                            </form>

                                            <div class="modal fade" id="lessonModal{{ $lesson->id }}">
                                                <div class="modal-dialog modal-lg">
                                                    <form action="{{ route('learnhub.lesson.store', [$subject->id, $week->id]) }}" method="POST" class="modal-content">
                                                        @csrf
                                                        <div class="modal-header"><h4>Edit Lesson</h4></div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Title</label>
                                                                <input type="text" name="title" class="form-control" value="{{ $lesson->title }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Content</label>
                                                                <textarea name="content" class="form-control" rows="12" required>{{ $lesson->content }}</textarea>
                                                                <small class="text-muted">Use ## for headings, - for bullet points</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success">Save Lesson</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if($week->lessons->isEmpty())
                                        <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#newLessonModal{{ $week->id }}">+ Add Lesson</button>
                                        <div class="modal fade" id="newLessonModal{{ $week->id }}">
                                            <div class="modal-dialog modal-lg">
                                                <form action="{{ route('learnhub.lesson.store', [$subject->id, $week->id]) }}" method="POST" class="modal-content">
                                                    @csrf
                                                    <div class="modal-header"><h4>Add Lesson</h4></div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Title</label>
                                                            <input type="text" name="title" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Content</label>
                                                            <textarea name="content" class="form-control" rows="12" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Save Lesson</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @empty
                                    <p class="text-muted">No weeks yet. Add your first week to start creating lessons.</p>
                                @endforelse
                            @else
                                @if($insights)
                                    {{-- Class Summary with Progress Bars --}}
                                    <div class="row mb-4">
                                        <div class="col-md-3 col-6 mb-3">
                                            <div class="box box-body text-center b-1">
                                                <h3 class="mb-0">{{ $insights['class_summary']['completion_rate'] }}%</h3>
                                                <small class="text-muted">Lesson Completion</small>
                                                <div class="progress progress-sm mt-2">
                                                    <div class="progress-bar progress-bar-success" style="width:{{ $insights['class_summary']['completion_rate'] }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 mb-3">
                                            <div class="box box-body text-center b-1">
                                                <h3 class="mb-0">{{ $insights['class_summary']['average_lessons_read'] }}</h3>
                                                <small class="text-muted">Avg Lessons Read</small>
                                                <div class="progress progress-sm mt-2">
                                                    @php $lessonPct = !empty($insights['lessons']) ? min(100, round(($insights['class_summary']['average_lessons_read'] / count($insights['lessons'])) * 100)) : 0; @endphp
                                                    <div class="progress-bar progress-bar-info" style="width:{{ $lessonPct }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 mb-3">
                                            <div class="box box-body text-center b-1">
                                                <h3 class="mb-0">{{ $insights['class_summary']['all_cbt_attempted_pct'] }}%</h3>
                                                <small class="text-muted">All Quizzes Done</small>
                                                <div class="progress progress-sm mt-2">
                                                    <div class="progress-bar progress-bar-warning" style="width:{{ $insights['class_summary']['all_cbt_attempted_pct'] }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 mb-3">
                                            <div class="box box-body text-center b-1">
                                                <h3 class="mb-0">{{ $insights['class_summary']['average_cbt_pass_rate'] }}%</h3>
                                                <small class="text-muted">Quiz Pass Rate</small>
                                                <div class="progress progress-sm mt-2">
                                                    <div class="progress-bar progress-bar-{{ $insights['class_summary']['average_cbt_pass_rate'] >= 60 ? 'success' : 'danger' }}" style="width:{{ $insights['class_summary']['average_cbt_pass_rate'] }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if(count($insights['students_on_track']))
                                    <div class="box box-body b-1 border-success mb-3">
                                        <h5 class="text-success">Students On Track ({{ count($insights['students_on_track']) }})</h5>
                                        @foreach($insights['students_on_track'] as $s)
                                            <div class="d-flex justify-content-between py-1 border-bottom">
                                                <span>{{ $s['name'] }}</span>
                                                <small>{{ $s['lessons_read'] }} lessons · {{ $s['cbt_passed'] }}/{{ $s['cbt_attempted'] }} quizzes</small>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    @if(count($insights['students_needing_attention']))
                                    <div class="box box-body b-1 border-warning mb-3">
                                        <h5 class="text-warning">Needing Attention ({{ count($insights['students_needing_attention']) }})</h5>
                                        @foreach($insights['students_needing_attention'] as $s)
                                            <div class="d-flex justify-content-between py-1 border-bottom">
                                                <div>
                                                    <span>{{ $s['name'] }}</span>
                                                    <br><small class="text-warning">{{ $s['missing'] ?? '' }}</small>
                                                </div>
                                                <small>{{ $s['lessons_read'] }} lessons · {{ $s['cbt_attempted'] }} quizzes</small>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    <div class="box box-body b-1">
                                        <h5>Insights</h5>
                                        <ul>
                                            @foreach($insights['insights_and_suggestions'] as $tip)
                                                <li>{{ $tip }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    {{-- Detailed Student Tracking Table --}}
                                    <div class="box box-body b-1 mt-3">
                                        <h5 class="mb-3">Student Tracker — Per Lesson</h5>
                                        @if(!empty($insights['lessons']) && !empty($insights['student_details']))
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" style="min-width:600px">
                                                <thead>
                                                    <tr>
                                                        <th style="min-width:150px">Student</th>
                                                        @foreach($insights['lessons'] as $lesson)
                                                        <th class="text-center" style="min-width:120px">
                                                            <small class="d-block text-muted">W{{ $lesson->week->week_number ?? '?' }}</small>
                                                            {{ Str::limit($lesson->title, 20) }}
                                                        </th>
                                                        @endforeach
                                                        <th class="text-center">Lessons Read</th>
                                                        <th class="text-center">Quizzes Passed</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($insights['student_details'] as $student)
                                                    <tr>
                                                        <td><strong>{{ $student['name'] }}</strong></td>
                                                        @foreach($insights['lessons'] as $lesson)
                                                        @php $lb = $student['lesson_breakdown'][$lesson->id] ?? []; @endphp
                                                        <td class="text-center">
                                                            @if(!empty($lb['read']))
                                                                <span class="text-success" title="Read on {{ $lb['read_at'] }}">&#10003;</span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                            @if(!empty($lb['quiz_attempts']))
                                                                <br>
                                                                @if($lb['passed'])
                                                                    <span class="badge badge-success">{{ $lb['best_score'] }}/{{ $lb['best_total'] }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ $lb['best_score'] }}/{{ $lb['best_total'] }}</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        @endforeach
                                                        <td class="text-center">{{ $student['lessons_read'] }}/{{ count($insights['lessons']) }}</td>
                                                        <td class="text-center">{{ $student['cbt_passed'] }}/{{ $student['cbt_attempted'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            &#10003; = Lesson read &nbsp;|&nbsp;
                                            <span class="badge badge-success" style="font-size:10px">5/5</span> = Quiz passed &nbsp;|&nbsp;
                                            <span class="badge badge-danger" style="font-size:10px">1/5</span> = Quiz failed
                                        </small>
                                        @elseif(empty($insights['lessons']))
                                        <p class="text-muted text-center py-3">No lessons in this subject yet. Add weeks and lessons on the Lessons tab, then student progress will appear here.</p>
                                        @else
                                        <p class="text-muted text-center py-3">No student activity yet. The tracker will show each student's lesson reads and quiz scores once they start learning.</p>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="weekModal">
    <div class="modal-dialog">
        <form action="{{ route('learnhub.week.store', $subject->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h4>Add Week</h4></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Week Number</label>
                    <input type="number" name="week_number" class="form-control" value="{{ $subject->weeks->count() + 1 }}" min="1" required>
                </div>
                <div class="form-group">
                    <label>Week Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Introduction to Algebra">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Add Week</button>
            </div>
        </form>
    </div>
</div>
@endsection
