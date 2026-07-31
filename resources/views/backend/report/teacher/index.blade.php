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
                                            <td>{{ $report->title }}</td>
                                            <td>
                                                @if($report->video_path) <i class="fa fa-video-camera text-primary"></i> @endif
                                                @if($report->media->where('file_type', 'image')->count() > 0) <i class="fa fa-image text-success"></i> @endif
                                                @if($report->media->where('file_type', 'document')->count() > 0) <i class="fa fa-file-text text-warning"></i> @endif
                                            </td>
                                            <td>
                                                @php
                                                    $seenCount = $report->students->whereNotNull('pivot.seen_at')->count();
                                                    $totalCount = $report->students->count();
                                                @endphp
                                                <span class="badge badge-pill badge-primary">{{ $seenCount }} / {{ $totalCount }}</span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i></a>
                                                <a href="{{ route('admin.report.delete', $report->id) }}" class="btn btn-danger btn-sm" id="delete" title="Delete"><i class="fa fa-trash"></i></a>
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
