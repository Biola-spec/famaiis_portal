@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">All Reports Monitoring</h3>
                        </div>
                        <div class="box-body">
                            <!-- Filters -->
                            <form action="{{ route('admin.report.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="class_id" class="form-control">
                                            <option value="">All Classes</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="teacher_id" class="form-control">
                                            <option value="">All Teachers</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="report_type" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="weekly" {{ request('report_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="yearly" {{ request('report_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary btn-block">Filter Results</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Teacher</th>
                                            <th>Class</th>
                                            <th>Subject</th>
                                            <th>Type</th>
                                            <th>Title</th>
                                            <th>Stats</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $report)
                                        <tr>
                                            <td>{{ $report->created_at->format('d M Y') }}</td>
                                            <td>{{ $report->teacher->name }}</td>
                                            <td>{{ $report->studentClass->name }}</td>
                                            <td>{{ $report->subject->name ?? 'General' }}</td>
                                            <td><span class="badge badge-info">{{ ucfirst($report->report_type) }}</span></td>
                                            <td>{{ $report->title }}</td>
                                            <td>
                                                @php
                                                    $seenCount = $report->students->whereNotNull('pivot.seen_at')->count();
                                                    $totalCount = $report->students->count();
                                                @endphp
                                                <span class="badge badge-pill badge-success" title="Seen by Parents">{{ $seenCount }} / {{ $totalCount }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.report.delete', $report->id) }}" class="btn btn-danger btn-sm" id="delete"><i class="fa fa-trash"></i> Delete</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $reports->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
