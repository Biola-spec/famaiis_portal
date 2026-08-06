@extends('admin.admin_master')
@section('admin')
@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $badgeMap = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'secondary'];
@endphp
<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="page-title">{{ $isAdmin ? 'Teacher Leave Requests' : 'My Leave Requests' }}</h3>
                @unless($isAdmin)
                    <a href="{{ route('leave.requests.create') }}" class="btn btn-success">
                        <i class="fa fa-plus"></i> Request Leave
                    </a>
                @endunless
            </div>
        </div>

        <section class="content">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    @if($isAdmin)<th>Teacher</th>@endif
                                    <th>Type</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                    <th>Document</th>
                                    <th>Comment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaveRequests as $leaveRequest)
                                    <tr>
                                        @if($isAdmin)<td>{{ $leaveRequest->teacher->name }}</td>@endif
                                        <td>{{ $leaveRequest->leave_type }}</td>
                                        <td>{{ $leaveRequest->start_date->format('M d, Y') }} - {{ $leaveRequest->end_date->format('M d, Y') }}</td>
                                        <td><span class="badge badge-{{ $badgeMap[$leaveRequest->status] ?? 'secondary' }}">{{ ucfirst($leaveRequest->status) }}</span></td>
                                        <td>
                                            @if($leaveRequest->document_path)
                                                <a href="{{ route('leave.requests.download', $leaveRequest) }}" class="btn btn-sm btn-info">Download</a>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>{{ $leaveRequest->admin_comment ?: '-' }}</td>
                                        <td>
                                            @if($isAdmin && $leaveRequest->status === 'pending')
                                                <form action="{{ route('leave.requests.status', $leaveRequest) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                                <form action="{{ route('leave.requests.status', $leaveRequest) }}" method="POST" class="mt-1">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <input type="text" name="admin_comment" class="form-control form-control-sm mb-1" placeholder="Rejection reason">
                                                    <button class="btn btn-sm btn-danger">Reject</button>
                                                </form>
                                            @elseif(!$isAdmin && $leaveRequest->status === 'pending')
                                                <form action="{{ route('leave.requests.cancel', $leaveRequest) }}" method="POST">
                                                    @csrf
                                                    <button class="btn btn-sm btn-warning">Cancel</button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center">No leave requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $leaveRequests->links() }}
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
