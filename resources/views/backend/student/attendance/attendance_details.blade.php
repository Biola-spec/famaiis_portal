@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Student Attendance Details</h3>
                            <a href="{{ route('student.attendance.view') }}" style="float: right;" class="btn btn-rounded btn-primary mb-5"> Back to List</a>
                        </div>
                        <div class="box-body">
                            @php
                                $first = $details->first();
                            @endphp
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <strong>Date:</strong> {{ date('d-m-Y', strtotime($first->date)) }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Class:</strong> {{ $first->student_class->name }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Section:</strong> {{ $first->section->name ?? 'All Sections' }}
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th>Student Name</th>
                                            <th>ID No</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($details as $key => $v)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $v->student->name }}</td>
                                            <td>{{ $v->student->id_no }}</td>
                                            <td>
                                                @if($v->attend_status == 'Present')
                                                    <span class="badge badge-success">Present</span>
                                                @elseif($v->attend_status == 'Leave')
                                                    <span class="badge badge-info">Leave</span>
                                                @else
                                                    <span class="badge badge-danger">Absent</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
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
