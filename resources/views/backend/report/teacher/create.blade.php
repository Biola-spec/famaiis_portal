@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Create New Report / Activity</h3>
                        </div>
                        <div class="box-body">
                            <form method="post" action="{{ route('teacher.report.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Class <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="class_id" id="class_id" required class="form-control">
                                                    <option value="" selected disabled>Select Class</option>
                                                    @foreach($classes as $class)
                                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Subject <span class="text-danger">(Optional)</span></h5>
                                            <div class="controls">
                                                <select name="subject_id" id="subject_id" class="form-control">
                                                    <option value="" selected disabled>Select Subject</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Report Type <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="report_type" required class="form-control">
                                                    <option value="daily">Daily Report</option>
                                                    <option value="weekly">Weekly Report</option>
                                                    <option value="monthly">Monthly Report</option>
                                                    <option value="yearly">Yearly Report</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h5>Report Title <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <input type="text" name="title" class="form-control" required placeholder="e.g., Weekly Mathematics Progress">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h5>Description <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <textarea id="editor1" name="description" rows="10" cols="80" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Upload Video <span class="text-info">(Max 20MB)</span></h5>
                                            <div class="controls">
                                                <input type="file" name="video" class="form-control" accept="video/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Upload Images <span class="text-info">(Activities)</span></h5>
                                            <div class="controls">
                                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Upload Documents <span class="text-info">(PDF, Doc, Excel)</span></h5>
                                            <div class="controls">
                                                <input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Target Students <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="target" id="target" required class="form-control">
                                                    <option value="all">Whole Class</option>
                                                    <option value="specific">Specific Student(s)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8" id="specific_students_div" style="display: none;">
                                        <div class="form-group">
                                            <h5>Select Student(s) <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="student_ids[]" id="student_ids" class="form-control select2" multiple style="width: 100%;">
                                                    <!-- AJAX populated -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs-right mt-4">
                                    <input type="submit" class="btn btn-rounded btn-info mb-5" value="Submit Report">
                                    <a href="{{ route('teacher.report.index') }}" class="btn btn-rounded btn-secondary mb-5">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="{{ asset('assets/vendor_components/ckeditor/ckeditor.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select Student(s)",
            allowClear: true
        });

        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('editor1');
        }

        $('#class_id').on('change', function() {
            var class_id = $(this).val();
            if (class_id) {
                // Get Subjects
                $.ajax({
                    url: "{{ route('teacher.report.getSubjects') }}",
                    type: "GET",
                    data: { class_id: class_id },
                    success: function(data) {
                        $('#subject_id').empty();
                        $('#subject_id').append('<option value="" selected disabled>Select Subject</option>');
                        $.each(data, function(key, value) {
                            $('#subject_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });

                // Get Students for specific selection
                $.ajax({
                    url: "{{ route('teacher.report.getStudents') }}",
                    type: "GET",
                    data: { class_id: class_id },
                    success: function(data) {
                        $('#student_ids').empty();
                        $.each(data, function(key, value) {
                            $('#student_ids').append('<option value="' + value.id + '">' + value.name + ' (' + value.id_no + ')</option>');
                        });
                        $('#student_ids').trigger('change');
                    }
                });
            }
        });

        $('#target').on('change', function() {
            if ($(this).val() == 'specific') {
                $('#specific_students_div').show();
                $('#student_ids').attr('required', true);
            } else {
                $('#specific_students_div').hide();
                $('#student_ids').attr('required', false);
            }
        });
    });
</script>
@endsection
