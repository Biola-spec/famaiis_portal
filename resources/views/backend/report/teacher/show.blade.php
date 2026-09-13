@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border d-flex align-items-center justify-content-between">
                            <h3 class="box-title mb-0">Activity Report Details</h3>
                            <div>
                                @if(Auth::user()->hasRole('Admin', 'Super Admin') || ($report->status ?? 'pending') !== 'approved')
                                    <a href="{{ route('teacher.report.edit', $report->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                @elseif((int) Auth::id() === (int) $report->teacher_id)
                                    <form action="{{ route('teacher.report.recall', $report->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fa fa-undo"></i> Recall for Amendment
                                        </button>
                                    </form>
                                @endif
                                @if(Auth::user()->hasRole('Admin', 'Super Admin') || (int) Auth::id() === (int) $report->teacher_id)
                                    <a href="{{ Auth::user()->hasRole('Admin', 'Super Admin') ? route('admin.report.index') : route('teacher.report.index') }}" class="btn btn-secondary btn-sm">
                                        Back
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="box-body">
                            @php($statusMap = ['pending' => 'warning', 'approved' => 'success', 'recalled' => 'danger'])

                            <div class="row">
                                <div class="col-md-8">
                                    <h4>{{ $report->title }}</h4>
                                    <p>
                                        <span class="badge badge-info">{{ ucfirst($report->report_type) }}</span>
                                        <span class="badge badge-{{ $statusMap[$report->status ?? 'pending'] ?? 'secondary' }}">
                                            {{ ucfirst($report->status ?? 'pending') }}
                                        </span>
                                    </p>
                                    <div class="mb-3">{!! $report->description !!}</div>
                                </div>
                                <div class="col-md-4">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Teacher</th>
                                            <td>{{ optional($report->teacher)->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Class</th>
                                            <td>{{ optional($report->studentClass)->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Subject</th>
                                            <td>{{ optional($report->subject)->name ?? 'General' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Target</th>
                                            <td>{{ $report->is_for_all ? 'Whole Class' : 'Specific Student(s)' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Students</th>
                                            <td>{{ $report->students->count() }}</td>
                                        </tr>
                                        @if($report->approvedBy)
                                            <tr>
                                                <th>Approved By</th>
                                                <td>{{ $report->approvedBy->name }}</td>
                                            </tr>
                                        @endif
                                        @if($report->recalledBy)
                                            <tr>
                                                <th>Recalled By</th>
                                                <td>{{ $report->recalledBy->name }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <h5>Target Students</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>ID No</th>
                                            <th>Seen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($report->students as $student)
                                            <tr>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->id_no ?? '' }}</td>
                                                <td>{{ $student->pivot->seen_at ? \Carbon\Carbon::parse($student->pivot->seen_at)->format('d M Y h:i A') : 'Not seen' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No students linked to this report.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($report->video_path)
                                <hr>
                                <h5>Video</h5>
                                <video width="100%" controls preload="metadata">
                                    <source src="{{ url('storage/'.$report->video_path) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif

                            @if($report->media->isNotEmpty())
                                <hr>
                                <h5>Attachments</h5>
                                <div class="row">
                                    @foreach($report->media as $media)
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ url('storage/'.$media->file_path) }}" target="_blank" class="btn btn-outline-primary btn-block">
                                                <i class="fa {{ $media->file_type === 'image' ? 'fa-image' : 'fa-file-text' }}"></i>
                                                {{ ucfirst($media->file_type) }}
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
