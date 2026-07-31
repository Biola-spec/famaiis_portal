@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">FamaiisStudyHub — My Subjects</h3>
                        </div>
                        <div class="box-body">
                            @if($subjects->isEmpty())
                                <p class="text-muted text-center py-5">No subjects available yet. Check back soon!</p>
                            @else
                                <div class="row">
                                    @foreach($subjects as $subject)
                                    <div class="col-md-4 mb-4">
                                        <a href="{{ route('learnhub.student.subject', $subject->id) }}" class="box box-body b-1 bg-light d-block text-dark" style="text-decoration:none">
                                            <h4>{{ $subject->name }}</h4>
                                            <div class="mb-2">
                                                @if($subject->studentClass)
                                                    <span class="badge badge-info">{{ $subject->studentClass->name }}</span>
                                                @endif
                                                @if($subject->year)
                                                    <span class="badge badge-success">{{ $subject->year->name }}</span>
                                                @endif
                                                @if($subject->term)
                                                    <span class="badge badge-warning">{{ $subject->term->name }}</span>
                                                @endif
                                            </div>
                                            <p class="text-muted mb-0">{{ $subject->description ?: $subject->total_weeks.' weeks of learning' }}</p>
                                            <small class="text-primary">Start learning →</small>
                                        </a>
                                    </div>
                                    @endforeach
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
