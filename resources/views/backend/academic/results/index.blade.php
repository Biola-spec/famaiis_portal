@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Results</h4>
                            <p class="mb-0">
                                Default Session: <strong>{{ optional($currentSession)->name }}</strong>
                            </p>
                        </div>
                        <div class="box-body">
                            <form method="GET" action="{{ route('academic.results.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Session</label>
                                        <select name="session_id" class="form-control">
                                            <option value="">Current Session</option>
                                            @foreach($sessions as $session)
                                                <option value="{{ $session->id }}" {{ ($filters['session_id'] ?? optional($currentSession)->id) == $session->id ? 'selected' : '' }}>
                                                    {{ $session->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Section</label>
                                        <select name="section_id" id="section_id" class="form-control">
                                            <option value="">All Sections</option>
                                            @foreach($sections as $section)
                                                <option value="{{ $section->id }}" {{ ($filters['section_id'] ?? null) == $section->id ? 'selected' : '' }}>
                                                    {{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Term</label>
                                        <select name="term" class="form-control">
                                            <option value="">All Terms</option>
                                            @foreach($terms as $term)
                                                <option value="{{ $term }}" {{ ($filters['term'] ?? null) == $term ? 'selected' : '' }}>
                                                    {{ $term }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Class</label>
                                        <select name="class_id" id="class_id" class="form-control">
                                            <option value="">All Classes</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ ($filters['class_id'] ?? null) == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Subject</label>
                                        <select name="subject_id" class="form-control">
                                            <option value="">All Subjects</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ ($filters['subject_id'] ?? null) == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2" style="padding-top: 25px;">
                                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Subject</th>
                                            <th>Term</th>
                                            <th>Session</th>
                                            <th>CA</th>
                                            <th>Exam</th>
                                            <th>Total</th>
                                            <th>Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $result)
                                            <tr>
                                                 <td>{{ optional($result->student)->name }}</td>
                                                <td>{{ optional($result->student_class)->name }}</td>
                                                <td>{{ $result->section->name ?? 'N/A' }}</td>
                                                <td>{{ optional($result->subject)->name }}</td>
                                                <td>{{ $result->term }}</td>
                                                <td>{{ optional($result->year)->name }}</td>
                                                <td>{{ $result->ca_score }}</td>
                                                <td>{{ $result->exam_score }}</td>

                                                <td>{{ $result->total_score }}</td>
                                                <td>{{ $result->grade }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $results->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#section_id').on('change', function() {
        var section_id = $(this).val();
        if (section_id) {
            $.ajax({
                url: "{{ route('academic.marks.classes') }}",
                type: "GET",
                data: { section_id: section_id },
                success: function(data) {
                    $('#class_id').empty();
                    $('#class_id').append('<option value="">All Classes</option>');
                    $.each(data, function(key, value) {
                        $('#class_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            // If section is cleared, you might want to reload all classes or keep it empty
            // For now, let's just leave it as is or empty it.
        }
    });
});
</script>
@endsection
