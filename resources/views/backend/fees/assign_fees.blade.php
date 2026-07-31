@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <!-- Assign Fee Form -->
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Assign Fee to Student</h3>
                        </div>
                        <div class="box-body">
                            <form method="post" action="{{ route('fee.assign.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Student <span class="text-danger">*</span></label>
                                    <select name="student_id" class="form-control select2" style="width: 100%;" required>
                                        <option value="" selected disabled>Select Student</option>
                                        @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->id_no }} - {{ $student->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Fee Structure (Section/Class) <span class="text-danger">*</span></label>
                                    <select name="fee_structure_id" class="form-control select2" style="width: 100%;" required>
                                        <option value="" selected disabled>Select Structure</option>
                                        @foreach($sections as $s)
                                            <optgroup label="{{ $s->name }}">
                                                @foreach($s->feeStructures as $fs)
                                                    <option value="{{ $fs->id }}">
                                                        {{ $fs->year->name ?? '' }} - {{ $fs->studentClass->name ?? 'All Classes' }} - {{ $fs->term->name ?? 'Annual' }} (₦{{ $fs->total_amount }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-rounded btn-primary">Assign Fee</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Assigned Fees List -->
                <div class="col-md-8">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Assigned Fees List</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Section</th>
                                            <th>Structure (Term/Year)</th>
                                            <th>Total Due</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($studentFees as $sf)
                                        <tr>
                                            <td>{{ $sf->student->id_no }} - {{ $sf->student->name }}</td>
                                            <td>{{ $sf->section->name ?? 'N/A' }}</td>
                                            <td>
                                                {{ $sf->feeStructure->term->name ?? 'Annual' }} / {{ $sf->feeStructure->year->name ?? 'N/A' }}
                                            </td>
                                            <td>₦{{ number_format($sf->total_due, 2) }}</td>
                                            <td>
                                                @if($sf->balance <= 0)
                                                    <span class="badge badge-success">Cleared</span>
                                                @else
                                                    <span class="badge badge-danger">₦{{ number_format($sf->balance, 2) }}</span>
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
