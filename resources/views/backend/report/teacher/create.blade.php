@extends('admin.admin_master')
@section('admin')
@php
    $isEdit = isset($report);
    $selectedTarget = old('target', $isEdit && !$report->is_for_all ? 'specific' : 'all');
    $selectedStudents = old('student_ids', $isEdit ? $report->students->pluck('id')->map(fn ($id) => (string) $id)->all() : []);
@endphp

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ $isEdit ? 'Edit Report / Activity' : 'Create New Report / Activity' }}</h3>
                        </div>
                        <div class="box-body">
                            @if(session('message'))
                                <div class="alert alert-{{ session('alert-type') === 'error' ? 'danger' : session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Please check the form:</strong>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form id="activity-report-form" method="post" action="{{ $isEdit ? route('teacher.report.update', $report->id) : route('teacher.report.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Class <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="class_id" id="class_id" required class="form-control">
                                                    <option value="" selected disabled>Select Class</option>
                                                    @foreach($classes as $class)
                                                        <option value="{{ $class->id }}" {{ (string) old('class_id', $isEdit ? $report->class_id : '') === (string) $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
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
                                                    @if($isEdit && $report->subject)
                                                        <option value="{{ $report->subject->id }}" selected>{{ $report->subject->name }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Report Type <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="report_type" required class="form-control">
                                                    <option value="daily" {{ old('report_type', $isEdit ? $report->report_type : 'daily') === 'daily' ? 'selected' : '' }}>Daily Report</option>
                                                    <option value="weekly" {{ old('report_type', $isEdit ? $report->report_type : '') === 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                                                    <option value="monthly" {{ old('report_type', $isEdit ? $report->report_type : '') === 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                                                    <option value="yearly" {{ old('report_type', $isEdit ? $report->report_type : '') === 'yearly' ? 'selected' : '' }}>Yearly Report</option>
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
                                                <input type="text" name="title" class="form-control" required placeholder="e.g., Weekly Mathematics Progress" value="{{ old('title', $isEdit ? $report->title : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h5>Description <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <textarea id="editor1" name="description" rows="10" cols="80">{{ old('description', $isEdit ? $report->description : '') }}</textarea>
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
                                                    <option value="all" {{ $selectedTarget === 'all' ? 'selected' : '' }}>Whole Class</option>
                                                    <option value="specific" {{ $selectedTarget === 'specific' ? 'selected' : '' }}>Specific Student(s)</option>
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

                                @if($isEdit && ($report->video_path || $report->media->isNotEmpty()))
                                    <div class="alert alert-info">
                                        Existing attachments will remain. Upload a new video to replace the current video, or add more images/documents below.
                                    </div>
                                @endif

                                <div class="text-xs-right mt-4">
                                    <input type="submit" class="btn btn-rounded btn-info mb-5" value="{{ $isEdit ? 'Update Report' : 'Submit Report' }}">
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

<link href="{{ asset('assets/vendor_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor_components/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.min.js') }}"></script>

<script>
    $(document).ready(function() {
        var selectedSubjectId = @json(old('subject_id', $isEdit ? $report->subject_id : null));
        var selectedStudentIds = @json(array_map('strval', $selectedStudents));

        $('.select2').select2({
            placeholder: "Select Student(s)",
            allowClear: true
        });

        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('editor1');
        }

        function fetchSubjectsForClass(class_id) {
            if (!class_id) {
                $('#subject_id').empty().append(new Option('Select Subject', '', true, true));
                return;
            }

            $.ajax({
                url: "{{ route('teacher.report.getSubjects') }}",
                type: "GET",
                data: { class_id: class_id },
                dataType: "json",
                success: function(data) {
                    $('#subject_id').empty();
                    $('#subject_id').append(new Option('Select Subject', '', !selectedSubjectId, !selectedSubjectId));
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            if (value && value.id) {
                                $('#subject_id').append(new Option(value.name, value.id, false, String(value.id) === String(selectedSubjectId)));
                            }
                        });
                    }
                }
            });
        }

        function fetchStudentsForClass(class_id) {
            if (!class_id) {
                $('#student_ids').empty();
                $('#student_ids').append(new Option('Please select a Class first', '', false, false));
                $('#student_ids').trigger('change');
                return;
            }

            $('#student_ids').empty().append(new Option('Loading students...', '', false, false)).trigger('change');

            $.ajax({
                url: "{{ route('teacher.report.getStudents') }}",
                type: "GET",
                data: { class_id: class_id },
                dataType: "json",
                success: function(data) {
                    $('#student_ids').empty();
                    var count = 0;
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            if (value && value.id) {
                                var label = value.name;
                                if (value.id_no) {
                                    label += ' (' + value.id_no + ')';
                                }
                                $('#student_ids').append(new Option(label, value.id, false, selectedStudentIds.indexOf(String(value.id)) !== -1));
                                count++;
                            }
                        });
                    }
                    
                    if (count === 0) {
                        $('#student_ids').append(new Option('No active students found in this class', '', false, false));
                    }

                    $('#student_ids').trigger('change');
                },
                error: function(xhr) {
                    $('#student_ids').empty();
                    $('#student_ids').append(new Option('Error loading students', '', false, false));
                    $('#student_ids').trigger('change');
                    if (typeof toastr !== 'undefined') {
                        var message = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Unable to load students for this class.';
                        toastr.error(message);
                    }
                }
            });
        }

        $('#class_id').on('change', function() {
            var class_id = $(this).val();
            selectedSubjectId = null;
            selectedStudentIds = [];
            if (class_id) {
                fetchSubjectsForClass(class_id);
                fetchStudentsForClass(class_id);
            } else {
                fetchSubjectsForClass(null);
                fetchStudentsForClass(null);
            }
        });

        $('#target').on('change', function() {
            if ($(this).val() == 'specific') {
                $('#specific_students_div').show();
                $('#student_ids').prop('required', true);
                var class_id = $('#class_id').val();
                fetchStudentsForClass(class_id);
            } else {
                $('#specific_students_div').hide();
                $('#student_ids').prop('required', false).val(null).trigger('change');
            }
        });

        $('#activity-report-form').on('submit', function(e) {
            if (typeof CKEDITOR !== 'undefined') {
                for (var instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            }

            if (!$.trim($('#editor1').val())) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please enter the report description.');
                } else {
                    alert('Please enter the report description.');
                }
                return;
            }

            var selectedStudents = $('#student_ids').val();
            if ($('#target').val() === 'specific' && (!selectedStudents || selectedStudents.length === 0)) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please select at least one student.');
                } else {
                    alert('Please select at least one student.');
                }
            }
        });

        if ($('#target').val() == 'specific') {
            $('#specific_students_div').show();
            $('#student_ids').prop('required', true);
            var class_id = $('#class_id').val();
            fetchStudentsForClass(class_id);
        }

        if ($('#class_id').val()) {
            fetchSubjectsForClass($('#class_id').val());
            if ($('#target').val() !== 'specific') {
                fetchStudentsForClass($('#class_id').val());
            }
        }
    });
</script>
@endpush
