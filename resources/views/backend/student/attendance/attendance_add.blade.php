@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Add Student Attendance</h3>
                </div>
                <div class="box-body">
                    <form method="post" action="{{ route('student.attendance.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <h5>Attendance Date <span class="text-danger">*</span></h5>
                                    <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <h5>Year <span class="text-danger">*</span></h5>
                                    <select name="year_id" id="year_id" required class="form-control">
                                        <option value="" selected="" disabled="">Select Year</option>
                                        @foreach($years as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <h5>Class <span class="text-danger">*</span></h5>
                                    <select name="class_id" id="class_id" required class="form-control">
                                        <option value="" selected="" disabled="">Select Class</option>
                                        @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <h5>Section <span class="text-info">(Optional)</span></h5>
                                    <select name="section_id" id="section_id" class="form-control">
                                        <option value="" selected="">All Sections</option>
                                        <!-- Sections will be loaded via AJAX if needed, but for now we can just show default -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row d-none" id="student-list-container">
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
                                    <tbody id="student-list-body">
                                        <!-- AJAX Loaded Students -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="text-xs-right mt-3">
                            <button type="submit" class="btn btn-rounded btn-info" id="submit-btn" disabled>Submit Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Load Sections when Class is selected
        $('#class_id').on('change', function() {
            var class_id = $(this).val();
            if (class_id) {
                $.ajax({
                    url: "{{ route('academic.marks.sections') }}",
                    type: "GET",
                    data: {class_id: class_id},
                    dataType: "json",
                    success: function(data) {
                        var html = '<option value="">All Sections</option>';
                        $.each(data, function(key, v) {
                            html += '<option value="' + v.id + '">' + v.name + '</option>';
                        });
                        $('#section_id').html(html);
                    }
                });
                loadStudents();
            }
        });

        $('#year_id, #section_id').on('change', function() {
            loadStudents();
        });

        function loadStudents() {
            var year_id = $('#year_id').val();
            var class_id = $('#class_id').val();
            var section_id = $('#section_id').val();

            if (year_id && class_id) {
                $.ajax({
                    url: "{{ route('student.attendance.getstudents') }}",
                    type: "GET",
                    data: {
                        year_id: year_id,
                        class_id: class_id,
                        section_id: section_id
                    },
                    beforeSend: function() {
                        $('#student-list-body').html('<tr><td colspan="4" class="text-center">Loading Students...</td></tr>');
                        $('#student-list-container').removeClass('d-none');
                    },
                    success: function(data) {
                        var html = '';
                        if (data.length > 0) {
                            $.each(data, function(key, v) {
                                html += '<tr>' +
                                    '<td>' + (key + 1) + '<input type="hidden" name="student_id[]" value="' + v.student_id + '"></td>' +
                                    '<td>' + v.student.name + '</td>' +
                                    '<td>' + v.student.id_no + '</td>' +
                                    '<td colspan="3">' +
                                    '<div class="switch-toggle switch-3 switch-candy">' +
                                    '<input name="attend_status' + key + '" type="radio" value="Present" id="present' + key + '" checked="checked" class="with-gap">' +
                                    '<label for="present' + key + '">Present</label>' +
                                    '<input name="attend_status' + key + '" type="radio" value="Leave" id="leave' + key + '" class="with-gap">' +
                                    '<label for="leave' + key + '">Leave</label>' +
                                    '<input name="attend_status' + key + '" type="radio" value="Absent" id="absent' + key + '" class="with-gap">' +
                                    '<label for="absent' + key + '">Absent</label>' +
                                    '</div>' +
                                    '</td>' +
                                    '</tr>';
                            });
                            $('#submit-btn').prop('disabled', false);
                        } else {
                            html = '<tr><td colspan="4" class="text-center text-danger">No students found for this selection.</td></tr>';
                            $('#submit-btn').prop('disabled', true);
                        }
                        $('#student-list-body').html(html);
                    }
                });
            }
        }

        // Bulk Selection
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
    .with-gap {
        margin-right: 5px;
    }
</style>
@endsection
