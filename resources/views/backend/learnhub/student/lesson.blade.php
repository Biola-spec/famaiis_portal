@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <a href="{{ route('learnhub.student.subject', $lesson->week->subject_id) }}" class="btn btn-sm btn-default">← Back</a>
                            <div style="float:right">
                                <a href="{{ route('learnhub.chat', $lesson->id) }}" class="btn btn-sm btn-info">Ask AI Tutor</a>
                                @if($hasQuiz)
                                    <a href="{{ route('learnhub.quiz.game', $lesson->id) }}" class="btn btn-sm btn-warning">
                                        🎮 Play Quiz Game
                                    </a>
                                    <a href="{{ route('learnhub.quiz', $lesson->id) }}" class="btn btn-sm btn-{{ $passed ? 'success' : 'primary' }}">
                                        {{ $passed ? 'Quiz Passed ✓' : 'Take Quiz' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            @if(session('error'))
                                <div class="alert alert-warning">{{ session('error') }}</div>
                            @endif
                            <h2>{{ $lesson->title }}</h2>
                            <p class="text-muted small mb-3">
                                <i class="fa fa-calendar"></i>
                                Note uploaded: <strong>{{ $lesson->created_at->format('d M Y, h:i A') }}</strong>
                                @if($lesson->updated_at->gt($lesson->created_at))
                                    · Last updated: {{ $lesson->updated_at->format('d M Y, h:i A') }}
                                @endif
                            </p>

                            @if($liveSessions->isNotEmpty())
                            <div class="alert alert-success mb-3">
                                <strong><i class="fa fa-video-camera"></i> Live Video Sessions</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach($liveSessions as $session)
                                    <li class="mb-1">
                                        {{ $session->title }}
                                        @if($session->status === 'live')
                                            <span class="badge badge-danger">LIVE NOW</span>
                                        @else
                                            <span class="badge badge-info">Scheduled{{ $session->scheduled_at ? ' · '.$session->scheduled_at->format('d M, h:i A') : '' }}</span>
                                        @endif
                                        <a href="{{ route('learnhub.live.join', $session->id) }}" class="btn btn-xs btn-success ml-2">Join Video</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if($bestAttempt && $bestAttempt->game_points > 0)
                            <div class="alert alert-info py-2">
                                🏆 Your best game score: <strong>{{ $bestAttempt->game_points }} XP</strong>
                                · Best streak: {{ $bestAttempt->max_streak }}
                                @if($bestAttempt->time_seconds)
                                    · Time: {{ gmdate('i:s', $bestAttempt->time_seconds) }}
                                @endif
                            </div>
                            @endif

                            <hr>
                            <div class="lesson-content">
                                @foreach(explode("\n", $lesson->content) as $line)
                                    @if(str_starts_with($line, '## '))
                                        <h3>{{ Str::after($line, '## ') }}</h3>
                                    @elseif(str_starts_with($line, '### '))
                                        <h4>{{ Str::after($line, '### ') }}</h4>
                                    @elseif(preg_match('/^[-*]\s/', $line))
                                        <li>{{ preg_replace('/^[-*]\s/', '', $line) }}</li>
                                    @elseif(trim($line) === '')
                                        <br>
                                    @else
                                        <p>{{ $line }}</p>
                                    @endif
                                @endforeach
                            </div>

                            @if($hasQuiz)
                            <div class="text-center mt-4 p-4 bg-light rounded">
                                <h5>Ready to test what you learned?</h5>
                                <p class="text-muted small">Play the quiz game based on this note and earn XP!</p>
                                <a href="{{ route('learnhub.quiz.game', $lesson->id) }}" class="btn btn-warning btn-lg">🎮 Play Quiz Game</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
