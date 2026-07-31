@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Create Objective Quiz</h3>
                        </div>
                        <div class="box-body">
                            <form method="post" action="{{ route('academic.cbt.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quiz Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Duration (Minutes) <span class="text-danger">*</span></label>
                                            <input type="number" name="duration" class="form-control" required min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Retake Limit <span class="text-danger">*</span></label>
                                            <input type="number" name="retake_limit" class="form-control" required min="1" value="1">
                                            <small class="text-muted">How many times a student can attempt this quiz.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Class <span class="text-danger">*</span></label>
                                            <select name="class_id" id="class_id" class="form-control" required>
                                                <option value="" selected disabled>Select Class</option>
                                                @foreach($classes as $class)
                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Section <span class="text-info">(Optional)</span></label>
                                            <select name="section_id" id="section_id" class="form-control">
                                                <option value="" selected>All Sections</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Subject <span class="text-danger">*</span></label>
                                            <select name="subject_id" id="subject_id" class="form-control" required>
                                                <option value="" selected disabled>Select Subject</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Term <span class="text-danger">*</span></label>
                                            <select name="term" class="form-control" required>
                                                <option value="" selected disabled>Select Term</option>
                                                @foreach($terms as $term)
                                                    <option value="{{ $term }}">{{ $term }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Assign to Student <span class="text-info">(Optional - Leave blank for all)</span></label>
                                            <select name="student_id" id="student_id" class="form-control">
                                                <option value="" selected>All Students</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs-right mt-3">
                                    <button type="submit" class="btn btn-info btn-rounded">Create Quiz</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    var activeYearId = "{{ $activeYear->id ?? '' }}";

    function updateSubjectsAndStudents() {
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();

        if(class_id) {
            // Get Subjects
            $.ajax({
                url: "{{ route('marks.getsubject') }}",
                type: "GET",
                data: {class_id: class_id, section_id: section_id},
                dataType: "json",
                success: function(data){
                    var d = '<option value="" selected disabled>Select Subject</option>';
                    $.each(data, function(key, value){
                        if (value.school_subject) {
                            d += '<option value="'+ value.school_subject.id +'">'+ value.school_subject.name +'</option>';
                        } else if (value.name) { // Fallback if direct subject list
                            d += '<option value="'+ value.id +'">'+ value.name +'</option>';
                        }
                    });
                    $('#subject_id').html(d);
                }
            });

            // Get Students
            if (activeYearId) {
                $.ajax({
                    url: "{{ route('student.marks.getstudents') }}",
                    type: "GET",
                    data: {year_id: activeYearId, class_id: class_id, section_id: section_id},
                    dataType: "json",
                    success: function(data){
                        var d = '<option value="" selected>All Students</option>';
                        $.each(data, function(key, value){
                            var student = value.student || value;
                            d += '<option value="'+ student.id +'">'+ student.name + ' (' + student.id_no + ')</option>';
                        });
                        $('#student_id').html(d);
                    }
                });
            }
        }
    }

    $('#class_id').on('change', function(){
        var class_id = $(this).val();
        if(class_id) {
            // Get Sections
            $.ajax({
                url: "{{ route('academic.marks.sections') }}",
                type: "GET",
                data: {class_id: class_id},
                dataType: "json",
                success: function(data){
                    var d = '<option value="" selected>All Sections</option>';
                    $.each(data, function(key, value){
                        d += '<option value="'+ value.id +'">'+ value.name +'</option>';
                    });
                    $('#section_id').html(d);
                    
                    // After sections are loaded, update subjects and students
                    updateSubjectsAndStudents();
                }
            });
        }
    });

    $('#section_id').on('change', function(){
        updateSubjectsAndStudents();
    });
});
</script>
@endsection
