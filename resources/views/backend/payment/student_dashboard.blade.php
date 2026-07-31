@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">School Fees Payment</h4>
                        </div>
                        <div class="box-body">
                            @if(auth()->user()->hasRole('Parent'))
                                <form method="GET" action="{{ route('payment.student.dashboard') }}" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Select Child</label>
                                            <select name="student_id" class="form-control" onchange="this.form.submit()">
                                                @foreach($children as $child)
                                                    <option value="{{ $child->id }}" {{ $selectedStudentId == $child->id ? 'selected' : '' }}>
                                                        {{ $child->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            <div class="row">
                                <div class="col-md-4"><strong>Total Fees:</strong> ₦{{ number_format($totalFees, 2) }}</div>
                                <div class="col-md-4"><strong>Paid:</strong> ₦{{ number_format($paidAmount, 2) }}</div>
                                <div class="col-md-4"><strong>Balance:</strong> ₦{{ number_format($balance, 2) }}</div>
                            </div>

                            <hr>
                            <h5>Fee Items</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Term</th>
                                            <th>Session</th>
                                            <th>Fee Type</th>
                                            <th>Amount (₦)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($fees as $fee)
                                            <tr>
                                                <td>{{ optional($fee->studentClass)->name }}</td>
                                                <td>{{ $fee->term }}</td>
                                                <td>{{ $fee->session }}</td>
                                                <td>{{ $fee->title }}</td>
                                                <td>{{ number_format((float)$fee->amount, 2) }}</td>
                                                <td>
                                                    <form action="{{ route('payment.initialize.post', $fee) }}" method="POST" class="d-flex">
                                                        @csrf
                                                        <input type="hidden" name="student_id" value="{{ $selectedStudentId }}">
                                                        <input type="number" step="0.01" min="100" max="{{ $fee->amount }}" name="amount" class="form-control form-control-sm mr-1" placeholder="Partial/Full">
                                                        <button class="btn btn-success btn-sm">Pay Now</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6">No fee setup found for this student class.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <hr>
                            <h5>Payment History</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Fee</th>
                                            <th>Amount (₦)</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->reference }}</td>
                                                <td>{{ optional($payment->fee)->title }}</td>
                                                <td>{{ number_format((float)$payment->amount, 2) }}</td>
                                                <td>{{ strtoupper($payment->payment_method ?? '-') }}</td>
                                                <td>
                                                    @if($payment->status === 'success')
                                                        <span class="badge badge-success">Success</span>
                                                    @elseif($payment->status === 'failed')
                                                        <span class="badge badge-danger">Failed</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ optional($payment->paid_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                                <td>
                                                    @if($payment->status === 'success')
                                                        <a href="{{ route('payment.receipt', $payment) }}" class="btn btn-info btn-sm">Download</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7">No payment records yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
