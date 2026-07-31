@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">My Quizzes</h3>
                        </div>
                        <div class="box-body">
                            @if(session('message'))
                                <div class="alert alert-{{ session('alert-type') }}">{{ session('message') }}</div>
                            @endif
                            <div class="row">
                                @forelse($quizzes as $quiz)
                                    @php 
                                        $userAttempts = $attempts->get($quiz->id) ?? collect();
                                        $completedAttempts = $userAttempts->where('status', 'completed');
                                        $latestCompleted = $completedAttempts->sortByDesc('created_at')->first();
                                        $used = $completedAttempts->count();
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="box box-solid box-primary">
                                            <div class="box-header">
                                                <h4 class="box-title">{{ $quiz->title }}</h4>
                                            </div>
                                            <div class="box-body">
                                                <p><strong>Subject:</strong> {{ $quiz->subject->name }}</p>
                                                <p><strong>Term:</strong> {{ $quiz->term }}</p>
                                                <p><strong>Duration:</strong> {{ $quiz->duration }} Minutes</p>
                                                <p><strong>Attempts:</strong> {{ $used }} / {{ $quiz->retake_limit }}</p>
                                                <hr>
                                                @if($latestCompleted)
                                                    <p class="text-success font-weight-bold">Latest Score: {{ $latestCompleted->score }}</p>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <a href="{{ route('student.cbt.result', $latestCompleted->id) }}" class="btn btn-block btn-info btn-sm">Result</a>
                                                        </div>
                                                        <div class="col-6">
                                                            @if($used < $quiz->retake_limit)
                                                                <a href="{{ route('student.cbt.take', $quiz->id) }}" class="btn btn-block btn-success btn-sm">Retake</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <a href="{{ route('student.cbt.take', $quiz->id) }}" class="btn btn-block btn-success">Take Quiz</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4">
                                        <h4 class="text-muted">No quizzes available for your class at this time.</h4>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
