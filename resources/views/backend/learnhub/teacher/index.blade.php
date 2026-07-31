@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">FamaiisStudyHub — My Subjects</h3>
                            <button type="button" class="btn btn-rounded btn-success mb-5" style="float:right" data-toggle="modal" data-target="#subjectModal">
                                + New Subject
                            </button>
                        </div>
                        <div class="box-body">
                            @if(session('message'))
                                <div class="alert alert-success">{{ session('message') }}</div>
                            @endif

                            @if($subjects->isEmpty())
                                <p class="text-muted text-center py-5">Create your first subject to start building lessons and quizzes.</p>
                            @else
                                <div class="row">
                                    @foreach($subjects as $subject)
                                    <div class="col-md-4 mb-4">
                                        <div class="box box-body b-1 bg-light">
                                            <h4>{{ $subject->name }}</h4>
                                            <p class="text-muted">{{ $subject->description ?: $subject->total_weeks.' weeks' }}</p>
                                            <div class="mb-2">
                                                @if($subject->studentClass)
                                                    <span class="badge badge-info">{{ $subject->studentClass->name }}</span>
                                                @endif
                                                @if($subject->year)
                                                    <span class="badge badge-success">{{ $subject->year->name }}</span>
                                                @endif
                                                @if($subject->term)
                                                    <span class="badge badge-warning">{{ $subject->term->name }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('learnhub.manage', $subject->id) }}" class="btn btn-sm btn-primary">Manage</a>
                                                <form action="{{ route('learnhub.subject.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Delete this subject and all content?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('learnhub.subject.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h4 class="modal-title">New Subject</h4></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Subject Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Mathematics">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Session <span class="text-danger">*</span></label>
                            <select name="year_id" id="subjectYearId" class="form-control" required>
                                <option value="">-- Select Session --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Term <span class="text-danger">*</span></label>
                            <select name="term_id" id="subjectTermId" class="form-control" required>
                                <option value="">-- Select Term --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Target Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-control" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Brief description">
                </div>
                <div class="form-group">
                    <label>Total Weeks</label>
                    <input type="number" name="total_weeks" class="form-control" value="12" min="1" max="52" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Create Subject</button>
            </div>
        </form>
    </div>
</div>

<script>
// All terms keyed by session (year) ID
var allTerms = @json($terms);

document.getElementById('subjectYearId').addEventListener('change', function() {
    var yearId = this.value;
    var termSelect = document.getElementById('subjectTermId');
    termSelect.innerHTML = '<option value="">-- Select Term --</option>';
    if (!yearId) return;
    allTerms.forEach(function(term) {
        if (String(term.student_year_id) === String(yearId)) {
            var opt = document.createElement('option');
            opt.value = term.id;
            opt.textContent = term.name;
            termSelect.appendChild(opt);
        }
    });
});
</script>
@endsection
