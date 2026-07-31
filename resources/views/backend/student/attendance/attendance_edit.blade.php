@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Student Attendance</h3>
                </div>
                <div class="box-body">
                    <form method="post" action="{{ route('student.attendance.store') }}">
                        @csrf
                        @php
                            $first = $editData->first();
                        @endphp
                        <input type="hidden" name="year_id" value="{{ $first->year_id }}">
                        <input type="hidden" name="class_id" value="{{ $first->class_id }}">
                        <input type="hidden" name="section_id" value="{{ $first->section_id }}">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h5>Attendance Date <span class="text-danger">*</span></h5>
                                    <input type="date" name="date" class="form-control" required value="{{ $first->date }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h5>Class</h5>
                                    <input type="text" class="form-control" readonly value="{{ $first->student_class->name }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h5>Section</h5>
                                    <input type="text" class="form-control" readonly value="{{ $first->section->name ?? 'All Sections' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center" style="vertical-align: middle;">SL</th>
                                            <th rowspan="2" class="text-center" style="vertical-align: middle;">Student Name</th>
                                            <th rowspan="2" class="text-center" style="vertical-align: middle;">ID No</th>
                                            <th colspan="3" class="text-center" style="vertical-align: middle; width: 30%">Attendance Status</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center btn present_all" style="display: table-cell; background-color: #000000; color:white; cursor: pointer;">Present</th>
                                            <th class="text-center btn leave_all" style="display: table-cell; background-color: #000000; color:white; cursor: pointer;">Leave</th>
                                            <th class="text-center btn absent_all" style="display: table-cell; background-color: #000000; color:white; cursor: pointer;">Absent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($editData as $key => $v)
                                        <tr>
                                            <td>{{ $key + 1 }}<input type="hidden" name="student_id[]" value="{{ $v->student_id }}"></td>
                                            <td>{{ $v->student->name }}</td>
                                            <td>{{ $v->student->id_no }}</td>
                                            <td colspan="3">
                                                <div class="switch-toggle switch-3 switch-candy">
                                                    <input name="attend_status{{ $key }}" type="radio" value="Present" id="present{{ $key }}" {{ $v->attend_status == 'Present' ? 'checked' : '' }} class="with-gap">
                                                    <label for="present{{ $key }}">Present</label>
                                                    <input name="attend_status{{ $key }}" type="radio" value="Leave" id="leave{{ $key }}" {{ $v->attend_status == 'Leave' ? 'checked' : '' }} class="with-gap">
                                                    <label for="leave{{ $key }}">Leave</label>
                                                    <input name="attend_status{{ $key }}" type="radio" value="Absent" id="absent{{ $key }}" {{ $v->attend_status == 'Absent' ? 'checked' : '' }} class="with-gap">
                                                    <label for="absent{{ $key }}">Absent</label>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="text-xs-right mt-3">
                            <button type="submit" class="btn btn-rounded btn-info">Update Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.present_all', function() {
            $("input[value=Present]").prop('checked', true);
        });
        $(document).on('click', '.leave_all', function() {
            $("input[value=Leave]").prop('checked', true);
        });
        $(document).on('click', '.absent_all', function() {
            $("input[value=Absent]").prop('checked', true);
        });
    });
</script>

<style type="text/css">
    .switch-toggle {
        display: flex;
        justify-content: space-around;
        align-items: center;
        background: #f8f9fa;
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    .switch-toggle label {
        margin-bottom: 0;
        cursor: pointer;
        padding: 2px 10px;
    }
</style>
@endsection
