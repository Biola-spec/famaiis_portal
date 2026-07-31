@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                
                <!-- Filter -->
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Fee Report Filter</h3>
                        </div>
                        <div class="box-body">
                            <form method="GET" action="{{ route('fee.report') }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Section</label>
                                            <select name="section_id" class="form-control">
                                                <option value="">All Sections</option>
                                                @foreach($sections as $s)
                                                    <option value="{{ $s->id }}" {{ request('section_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="padding-top: 25px;">
                                        <button type="submit" class="btn btn-rounded btn-primary">Filter</button>
                                        <a href="{{ route('fee.report') }}" class="btn btn-rounded btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="col-md-4">
                    <div class="box box-body bg-primary">
                        <h6 class="text-white text-uppercase">Total Expected Due</h6>
                        <h1 class="text-white font-weight-bold">₦{{ number_format($totalDue, 2) }}</h1>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box box-body bg-success">
                        <h6 class="text-white text-uppercase">Total Collected</h6>
                        <h1 class="text-white font-weight-bold">₦{{ number_format($totalPaid, 2) }}</h1>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box box-body bg-danger">
                        <h6 class="text-white text-uppercase">Total Outstanding Balance</h6>
                        <h1 class="text-white font-weight-bold">₦{{ number_format($totalBalance, 2) }}</h1>
                    </div>
                </div>

                <!-- Detail Table -->
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Student Balances</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Section</th>
                                            <th>Term/Year</th>
                                            <th>Total Due</th>
                                            <th>Total Paid</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($studentFees as $sf)
                                        <tr>
                                            <td>{{ $sf->student->id_no }} - {{ $sf->student->name }}</td>
                                            <td>{{ $sf->section->name ?? 'N/A' }}</td>
                                            <td>{{ $sf->feeStructure->term->name ?? 'Annual' }} / {{ $sf->feeStructure->year->name ?? 'N/A' }}</td>
                                            <td>₦{{ number_format($sf->total_due, 2) }}</td>
                                            <td><span class="text-success">₦{{ number_format($sf->total_paid, 2) }}</span></td>
                                            <td><span class="text-danger font-weight-bold">₦{{ number_format($sf->balance, 2) }}</span></td>
                                            <td>
                                                @if($sf->balance <= 0)
                                                    <span class="badge badge-success">Cleared</span>
                                                @else
                                                    <span class="badge badge-danger">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $studentFees->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>

@endsection
