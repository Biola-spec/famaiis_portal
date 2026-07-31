@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">All Fee Payments</h4>
                        </div>
                        <div class="box-body">
                            <form method="GET" action="{{ route('payments.admin.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Class</label>
                                        <select name="class_id" class="form-control">
                                            <option value="">All</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ ($filters['class_id'] ?? null) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Term</label>
                                        <select name="term" class="form-control">
                                            <option value="">All</option>
                                            @foreach($terms as $term)
                                                <option value="{{ $term }}" {{ ($filters['term'] ?? null) == $term ? 'selected' : '' }}>{{ $term }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">All</option>
                                            <option value="pending" {{ ($filters['status'] ?? null) === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="success" {{ ($filters['status'] ?? null) === 'success' ? 'selected' : '' }}>Success</option>
                                            <option value="failed" {{ ($filters['status'] ?? null) === 'failed' ? 'selected' : '' }}>Failed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 pt-4">
                                        <button class="btn btn-primary btn-sm">Filter</button>
                                        <a href="{{ route('payments.admin.export', request()->query()) }}" class="btn btn-success btn-sm">Export Report</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Student</th>
                                            <th>Class</th>
                                            <th>Term</th>
                                            <th>Amount (₦)</th>
                                            <th>Status</th>
                                            <th>Method</th>
                                            <th>Provider</th>
                                            <th>Paid At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->reference }}</td>
                                                <td>{{ optional($payment->student)->name }}</td>
                                                <td>{{ optional($payment->fee->studentClass)->name }}</td>
                                                <td>{{ optional($payment->fee)->term }}</td>
                                                <td>{{ number_format((float)$payment->amount, 2) }}</td>
                                                <td>{{ ucfirst($payment->status) }}</td>
                                                <td>{{ strtoupper($payment->payment_method ?? '-') }}</td>
                                                <td>{{ ucfirst($payment->provider) }}</td>
                                                <td>{{ optional($payment->paid_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="9">No payments found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
