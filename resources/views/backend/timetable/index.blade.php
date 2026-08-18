@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full"><section class="content">
        <div class="box">
            <div class="box-header with-border"><h4 class="box-title">School Subject Timetable</h4></div>
            <div class="box-body">
                <p class="text-muted">Create the timetable once here. It will appear on every user dashboard.</p>
                @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                @php($editing = $editing ?? null)
                <form method="POST" action="{{ $editing ? route('timetable.update', $editing) : route('timetable.store') }}">
                    @csrf
                    @if($editing) @method('PUT') @endif
                    <div class="row">
                        <div class="col-md-2 form-group"><label>Session</label><select name="year_id" class="form-control"><option value="">All sessions</option>@foreach($years as $year)<option value="{{ $year->id }}" {{ old('year_id', optional($editing)->year_id) == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>Section</label><select name="section_id" class="form-control"><option value="">All sections</option>@foreach($sections as $section)<option value="{{ $section->id }}" {{ old('section_id', optional($editing)->section_id) == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>Class <span class="text-danger">*</span></label><select name="class_id" class="form-control" required><option value="">Select class</option>@foreach($classes as $class)<option value="{{ $class->id }}" {{ old('class_id', optional($editing)->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}{{ $class->section ? ' - '.$class->section->name : '' }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>Subject <span class="text-danger">*</span></label><select name="subject_id" class="form-control" required><option value="">Select subject</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" {{ old('subject_id', optional($editing)->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>Teacher</label><select name="teacher_id" class="form-control"><option value="">Unassigned</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" {{ old('teacher_id', optional($editing)->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>Day <span class="text-danger">*</span></label><select name="day_of_week" class="form-control" required>@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)<option value="{{ $day }}" {{ old('day_of_week', optional($editing)->day_of_week) === $day ? 'selected' : '' }}>{{ $day }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>Start <span class="text-danger">*</span></label><input type="time" name="start_time" class="form-control" value="{{ old('start_time', optional($editing)->start_time ? substr($editing->start_time, 0, 5) : '') }}" required></div>
                        <div class="col-md-2 form-group"><label>End <span class="text-danger">*</span></label><input type="time" name="end_time" class="form-control" value="{{ old('end_time', optional($editing)->end_time ? substr($editing->end_time, 0, 5) : '') }}" required></div>
                        <div class="col-md-2 form-group"><label>Room</label><input name="room" class="form-control" value="{{ old('room', optional($editing)->room) }}" placeholder="Room / venue"></div>
                        <div class="col-md-2 form-group"><label>Status</label><select name="is_active" class="form-control"><option value="1" {{ old('is_active', optional($editing)->is_active ?? true) ? 'selected' : '' }}>Published</option><option value="0" {{ old('is_active', optional($editing)->is_active ?? true) ? '' : 'selected' }}>Hidden</option></select></div>
                        <div class="col-md-2 form-group d-flex align-items-end"><button class="btn btn-primary" type="submit">{{ $editing ? 'Update Entry' : 'Add Entry' }}</button>@if($editing)<a href="{{ route('timetable.index') }}" class="btn btn-light ml-2">Cancel</a>@endif</div>
                    </div>
                </form>
            </div>
        </div>

        @if(!$editing)
        @php($timetableGroups = collect($timetables)->groupBy(fn ($entry) => optional($entry->section)->name ?: 'All Sections'))
        @forelse($timetableGroups as $sectionName => $sectionEntries)
        <div class="box"><div class="box-header with-border"><h4 class="box-title">{{ $sectionName }} Timetable</h4></div><div class="box-body"><div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Day</th><th>Time</th><th>Class</th><th>Subject</th><th>Teacher</th><th>Room</th><th>Status</th><th>Action</th></tr></thead><tbody>@foreach($sectionEntries as $entry)<tr><td>{{ $entry->day_of_week }}</td><td>{{ substr($entry->start_time, 0, 5) }} - {{ substr($entry->end_time, 0, 5) }}</td><td>{{ optional($entry->studentClass)->name }}</td><td>{{ optional($entry->subject)->name }}</td><td>{{ optional($entry->teacher)->name ?: '-' }}</td><td>{{ $entry->room ?: '-' }}</td><td><span class="badge badge-{{ $entry->is_active ? 'success' : 'secondary' }}">{{ $entry->is_active ? 'Published' : 'Hidden' }}</span></td><td><a class="btn btn-sm btn-info" href="{{ route('timetable.edit', $entry) }}" title="Edit"><i class="fa fa-edit"></i></a><form method="POST" action="{{ route('timetable.destroy', $entry) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button></form></td></tr>@endforeach</tbody></table></div></div></div>
        @empty
        <div class="box"><div class="box-body text-center text-muted">No timetable entries created.</div></div>
        @endforelse
        @endif
    </section></div>
</div>
@endsection


