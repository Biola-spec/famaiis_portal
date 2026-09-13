@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">My Reports / Activities</h3>
                            <a href="{{ route('teacher.report.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5">Create New Report</a>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th>Date</th>
                                            <th>Class</th>
                                            <th>Subject</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Title</th>
                                            <th>Attachments</th>
                                            <th>Seen By</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $key => $report)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $report->created_at->format('d M Y') }}</td>
                                            <td>{{ $report->studentClass->name }}</td>
                                            <td>{{ $report->subject->name ?? 'General' }}</td>
                                            <td><span class="badge badge-info">{{ ucfirst($report->report_type) }}</span></td>
                                            <td>
                                                @php($statusMap = ['pending' => 'warning', 'approved' => 'success', 'recalled' => 'danger'])
                                                <span class="badge badge-{{ $statusMap[$report->status ?? 'pending'] ?? 'secondary' }}">
                                                    {{ ucfirst($report->status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>{{ $report->title }}</td>
                                            <td>
                                                @if($report->video_path) <i class="fa fa-video-camera text-primary"></i> @endif
                                                @if($report->media->where('file_type', 'image')->count() > 0) <i class="fa fa-image text-success"></i> @endif
                                                @if($report->media->where('file_type', 'document')->count() > 0) <i class="fa fa-file-text text-warning"></i> @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-primary">
                                                    {{ $report->students->whereNotNull('pivot.seen_at')->count() }} / {{ $report->students->count() }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('teacher.report.show', $report->id) }}" class="btn btn-primary btn-sm" title="View"><i class="fa fa-eye"></i></a>
                                                @if(($report->status ?? 'pending') === 'approved')
                                                    <form action="{{ route('teacher.report.recall', $report->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm" title="Recall for amendment">
                                                            <i class="fa fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('teacher.report.edit', $report->id) }}" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                                                @endif
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
