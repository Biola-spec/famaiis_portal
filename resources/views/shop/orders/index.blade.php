@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">{{ auth()->user()->hasRole('Admin', 'Accountant') ? 'Manage All Orders' : 'My Order History' }}</h3>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                @if(auth()->user()->hasRole('Admin', 'Accountant'))
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Filters</h4>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('orders.index') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select name="role" class="form-control">
                                                <option value="">All Roles</option>
                                                <option value="parent" {{ request('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                                                <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select name="status" class="form-control">
                                                <option value="">All Statuses</option>
                                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Clear</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Orders List</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            @if(auth()->user()->hasRole('Admin', 'Accountant'))
                                                <th>User</th>
                                                <th>Role</th>
                                            @endif
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Date</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            @if(auth()->user()->hasRole('Admin', 'Accountant'))
                                                <td>{{ $order->user->name }}</td>
                                                <td><span class="badge badge-secondary capitalize">{{ $order->role_type }}</span></td>
                                            @endif
                                            <td>₦{{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                @php
                                                    $statusBadges = [
                                                        'pending' => 'badge-warning',
                                                        'approved' => 'badge-info',
                                                        'completed' => 'badge-success',
                                                        'rejected' => 'badge-danger',
                                                    ];
                                                @endphp
                                                <span class="badge badge-pill {{ $statusBadges[$order->status] ?? 'badge-secondary' }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $paymentBadges = [
                                                        'pending' => 'badge-warning',
                                                        'submitted' => 'badge-info',
                                                        'success' => 'badge-success',
                                                        'failed' => 'badge-danger',
                                                    ];
                                                @endphp
                                                <span class="badge badge-pill {{ $paymentBadges[$order->payment_status ?? 'pending'] ?? 'badge-secondary' }}">
                                                    {{ ucfirst($order->payment_status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i></a>
                                                <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-success btn-sm" title="Invoice"><i class="fa fa-file-pdf-o"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
</div>

@endsection
