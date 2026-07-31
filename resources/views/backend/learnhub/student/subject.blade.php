@extends('admin.admin_master')
@section('admin')
@php
    $totalLessons = $subject->weeks->sum(fn($w) => $w->lessons->count());
    $completedLessons = $progress->count();
@endphp
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ $subject->name }}</h3>
                            <a href="{{ route('learnhub.index') }}" class="btn btn-sm btn-default" style="float:right">← Back</a>
                        </div>
                        <div class="box-body">
                            <p class="text-muted">{{ $totalLessons }} lessons · Progress: {{ $completedLessons }}/{{ $totalLessons }}</p>

                            @if($liveSessions->isNotEmpty())
                            <div class="alert alert-success mb-3">
                                <strong><i class="fa fa-video-camera"></i> Live Video Sessions</strong>
                                @foreach($liveSessions as $session)
                                <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                                    <span>
                                        {{ $session->title }}
                                        @if($session->status === 'live')
                                            <span class="badge badge-danger">LIVE NOW</span>
                                        @else
                                            <span class="badge badge-info">Upcoming</span>
                                        @endif
                                    </span>
                                    <a href="{{ route('learnhub.live.join', $session->id) }}" class="btn btn-sm btn-success">Join Video</a>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @foreach($subject->weeks as $week)
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
                                @forelse($week->lessons as $lesson)
                                    @php
                                        $isRead = $progress->has($lesson->id);
                                        $attempt = $attempts->get($lesson->id);
                                        $isPassed = $attempt && $attempt->passed;
                                    @endphp
                                    <a href="{{ route('learnhub.lesson', $lesson->id) }}" class="d-flex justify-content-between align-items-center p-2 border-bottom text-dark" style="text-decoration:none">
                                        <span>
                                            @if($isPassed)
                                                <i class="fa fa-check-circle text-success"></i>
                                            @elseif($isRead)
                                                <i class="fa fa-check text-primary"></i>
                                            @else
                                                <i class="fa fa-clock-o text-muted"></i>
                                            @endif
                                            {{ $lesson->title }}
                                            <br><small class="text-muted">Uploaded {{ $lesson->created_at->format('d M Y') }}</small>
                                        </span>
                                        @if($isPassed)
                                            <span class="badge badge-success">Passed</span>
                                        @endif
                                    </a>
                                @empty
                                    <p class="text-muted small mb-0">No lesson content yet</p>
                                @endforelse
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
