@extends('admin.admin_master')
@section('admin')
@php $pct = $total > 0 ? round(($score / $total) * 100) : 0; @endphp
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="box">
                        <div class="box-body text-center">
                            @if(!empty($isGameMode))
                                <h2>{{ $passed ? '🏆 Level Complete!' : '💪 Try Again!' }}</h2>
                                <h1 class="{{ $passed ? 'text-success' : 'text-warning' }}">{{ $attempt->game_points ?? 0 }} XP</h1>
                                <p>
                                    {{ $score }} of {{ $total }} correct ({{ $pct }}%)
                                    @if(($attempt->max_streak ?? 0) > 0)
                                        · Best streak: 🔥 {{ $attempt->max_streak }}
                                    @endif
                                    @if($attempt->time_seconds ?? null)
                                        · Time: {{ gmdate('i:s', $attempt->time_seconds) }}
                                    @endif
                                </p>
                            @else
                                <h2>{{ $passed ? 'Great job!' : 'Keep practicing!' }}</h2>
                                <h1 class="{{ $passed ? 'text-success' : 'text-danger' }}">{{ $pct }}%</h1>
                                <p>{{ $score }} of {{ $total }} correct</p>
                            @endif
                            <a href="{{ route('learnhub.lesson', $lesson->id) }}" class="btn btn-default">Back to Note</a>
                            @if(!empty($isGameMode) && !$passed)
                                <a href="{{ route('learnhub.quiz.game', $lesson->id) }}" class="btn btn-warning">🎮 Play Again</a>
                            @endif
                            <a href="{{ route('learnhub.student.subject', $lesson->week->subject_id) }}" class="btn btn-primary">All Lessons</a>
                        </div>
                    </div>

                    @foreach($questions as $q)
                        @php
                            $userAnswer = $answers['q'.$q->question_number] ?? null;
                            $isCorrect = $userAnswer === $q->correct_answer;
                        @endphp
                        <div class="box box-body b-1 mb-2 {{ $isCorrect ? 'border-success' : 'border-danger' }}">
                            <p><strong>{{ $q->question }}</strong></p>
                            @foreach(['A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c, 'D' => $q->option_d] as $opt => $text)
                                <div class="small {{ $q->correct_answer === $opt ? 'text-success font-weight-bold' : ($userAnswer === $opt ? 'text-danger' : 'text-muted') }}">
                                    {{ $opt }}. {{ $text }}
                                </div>
                            @endforeach
                            @if(!$isCorrect)
                                <p class="small text-muted mt-2"><em>{{ $q->explanation }}</em></p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
