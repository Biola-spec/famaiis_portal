@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Create Fee</h4>
                        </div>
                        <div class="box-body">
                            <form method="POST" action="{{ route('fees.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Class</label>
                                        <select name="class_id" class="form-control" required>
                                            <option value="">Select Class</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Term</label>
                                        <input type="text" class="form-control" name="term" placeholder="e.g. First Term" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Session</label>
                                        <input type="text" class="form-control" name="session" placeholder="e.g. 2025/2026" required>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label>Fee Type / Title</label>
                                        <input type="text" class="form-control" name="title" placeholder="Tuition / Exam / Development" required>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label>Amount (₦)</label>
                                        <input type="number" class="form-control" min="100" step="0.01" name="amount" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success btn-sm">Save Fee</button>
                                    <a href="{{ route('fees.index') }}" class="btn btn-secondary btn-sm">Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
