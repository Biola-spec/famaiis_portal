@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <!-- Record Payment Form -->
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Record Payment</h3>
                        </div>
                        <div class="box-body">
                            <form method="post" action="{{ route('fee.payments.record') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Student's Outstanding Fee <span class="text-danger">*</span></label>
                                    <select name="student_fee_id" class="form-control select2" style="width: 100%;" required>
                                        <option value="" selected disabled>Search Student / Structure</option>
                                        @foreach($studentFees as $sf)
                                            @if($sf->balance > 0)
                                                <option value="{{ $sf->id }}">
                                                    {{ $sf->student->id_no }} - {{ $sf->student->name }} | {{ $sf->section->name ?? 'N/A' }} | Bal: ₦{{ $sf->balance }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Amount Paid (₦) <span class="text-danger">*</span></label>
                                    <input type="number" name="amount_paid" class="form-control" step="0.01" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="POS">POS</option>
                                        <option value="Online">Online</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-rounded btn-success">Record Payment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Payments List -->
                <div class="col-md-8">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Payment History</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Receipt No</th>
                                            <th>Date</th>
                                            <th>Student</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $p)
                                        <tr>
                                            <td>{{ $p->receipt_no }}</td>
                                            <td>{{ date('d-m-Y', strtotime($p->payment_date)) }}</td>
                                            <td>{{ $p->student->id_no }} - {{ $p->student->name }}</td>
                                            <td>₦{{ number_format($p->amount_paid, 2) }}</td>
                                            <td>{{ $p->payment_method }}</td>
                                            <td>
                                                <a href="{{ route('fee.payments.receipt', $p->id) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $payments->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>

<!-- Select2 Script -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Search for Student...',
            allowClear: true
        });
    });
</script>

@endsection
