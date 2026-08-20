@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Quiz Setup</h3>
                        </div>
                        <div class="box-body">
                            <form method="post" action="{{ route('academic.cbt.update', $quiz->id) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quiz Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" required value="{{ $quiz->title }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Duration (Minutes) <span class="text-danger">*</span></label>
                                            <input type="number" name="duration" class="form-control" required min="1" value="{{ $quiz->duration }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Retake Limit <span class="text-danger">*</span></label>
                                            <input type="number" name="retake_limit" class="form-control" required min="1" value="{{ $quiz->retake_limit }}">
                                            <small class="text-muted">Attempts allowed.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Class <span class="text-danger">*</span></label>
                                            <select name="class_id" id="class_id" class="form-control" required>
                                                <option value="" disabled>Select Class</option>
                                                @foreach($classes as $class)
                                                    <option value="{{ $class->id }}" {{ $quiz->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Section <span class="text-info">(Optional)</span></label>
                                            <select name="section_id" id="section_id" class="form-control">
                                                <option value="">All Sections</option>
                                                <!-- Populated via AJAX -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Subject <span class="text-danger">*</span></label>
                                            <select name="subject_id" id="subject_id" class="form-control" required>
                                                <option value="" disabled>Select Subject</option>
                                                <!-- Populated via AJAX -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Term <span class="text-danger">*</span></label>
                                            <select name="term" class="form-control" required>
                                                @foreach($terms as $term)
                                                    <option value="{{ $term }}" {{ $quiz->term == $term ? 'selected' : '' }}>{{ $term }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs-right mt-3">
                                    <button type="submit" class="btn btn-info btn-rounded">Update Quiz Setup</button>
                                    <a href="{{ route('academic.cbt.index') }}" class="btn btn-secondary btn-rounded">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var selectedSectionId = "{{ $quiz->section_id }}";
    var selectedSubjectId = "{{ $quiz->subject_id }}";

    function updateSections(class_id, callback) {
        $.ajax({
            url: "{{ route('academic.marks.sections') }}",
            type: "GET",
            data: {class_id: class_id},
            dataType: "json",
            success: function(data){
                var d = '<option value="">All Sections</option>';
                $.each(data, function(key, value){
                    d += '<option value="'+ value.id +'" '+ (value.id == selectedSectionId ? 'selected' : '') +'>'+ value.name +'</option>';
                });
                $('#section_id').html(d);
                if(callback) callback();
            }
        });
    }

    function updateSubjects() {
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        if(class_id) {
            $.ajax({
                url: "{{ route('marks.getsubject') }}",
                type: "GET",
                data: {class_id: class_id, section_id: section_id},
                dataType: "json",
                success: function(data){
                    var d = '<option value="" disabled>Select Subject</option>';
                    $.each(data, function(key, value){
                        var s_id = value.school_subject ? value.school_subject.id : value.id;
                        var s_name = value.school_subject ? value.school_subject.name : value.name;
                        d += '<option value="'+ s_id +'" '+ (s_id == selectedSubjectId ? 'selected' : '') +'>'+ s_name +'</option>';
                    });
                    $('#subject_id').html(d);
                }
            });
        }
    }

    // Initial Load
    var initialClassId = $('#class_id').val();
    if(initialClassId) {
        updateSections(initialClassId, updateSubjects);
    }

    $('#class_id').on('change', function(){
        selectedSectionId = "";
        selectedSubjectId = "";
        updateSections($(this).val(), updateSubjects);
    });

    $('#section_id').on('change', function(){
        updateSubjects();
    });
});
</script>
@endsection
