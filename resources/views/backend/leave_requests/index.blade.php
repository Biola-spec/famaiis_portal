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
                                            @if($isAdmin && in_array($leaveRequest->status, ['pending', 'approved', 'rejected'], true))
                                                <form action="{{ route('leave.requests.status', $leaveRequest) }}" method="POST">
                                                    @csrf
                                                    <select name="status" class="form-control form-control-sm mb-1" aria-label="Leave decision">
                                                        <option value="approved" {{ $leaveRequest->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                                        <option value="rejected" {{ $leaveRequest->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                                    </select>
                                                    <textarea name="admin_comment" class="form-control form-control-sm mb-1" rows="2" maxlength="2000" required placeholder="Decision comment">{{ $leaveRequest->admin_comment }}</textarea>
                                                    <button class="btn btn-sm {{ $leaveRequest->status === 'pending' ? 'btn-primary' : 'btn-secondary' }}">
                                                        <i class="fa {{ $leaveRequest->status === 'pending' ? 'fa-gavel' : 'fa-pencil' }}"></i>
                                                        {{ $leaveRequest->status === 'pending' ? 'Review' : 'Edit decision' }}
                                                    </button>
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
