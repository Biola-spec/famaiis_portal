<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SchoolSection;
use App\Models\SchoolSubject;
use App\Models\SchoolTimetable;
use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolTimetableController extends Controller
{
    public function index()
    {
        return view('backend.timetable.index', $this->formData([
            'timetables' => SchoolTimetable::with(['year', 'section', 'studentClass', 'subject', 'teacher'])
                ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                ->orderBy('start_time')->get(),
        ]));
    }

    public function edit(SchoolTimetable $timetable)
    {
        return view('backend.timetable.index', $this->formData(['editing' => $timetable]));
    }

    public function store(Request $request)
    {
        SchoolTimetable::create($this->validated($request));
        return redirect()->route('timetable.index')->with(['message' => 'Timetable entry created.', 'alert-type' => 'success']);
    }

    public function update(Request $request, SchoolTimetable $timetable)
    {
        $timetable->update($this->validated($request));
        return redirect()->route('timetable.index')->with(['message' => 'Timetable entry updated.', 'alert-type' => 'success']);
    }

    public function destroy(SchoolTimetable $timetable)
    {
        $timetable->delete();
        return redirect()->route('timetable.index')->with(['message' => 'Timetable entry deleted.', 'alert-type' => 'info']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'year_id' => ['nullable', 'exists:student_years,id'],
            'section_id' => ['nullable', 'exists:school_sections,id'],
            'class_id' => ['required', 'exists:student_classes,id'],
            'subject_id' => ['required', 'exists:school_subjects,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function formData(array $data = []): array
    {
        return array_merge([
            'years' => StudentYear::orderByDesc('id')->get(),
            'sections' => SchoolSection::orderBy('name')->get(),
            'classes' => StudentClass::with('section')->orderBy('name')->get(),
            'subjects' => SchoolSubject::with('section')->orderBy('name')->get(),
            'teachers' => User::query()->where(function ($query) {
                $query->whereIn('usertype', ['Employee', 'employee', 'Teacher', 'teacher'])
                    ->orWhereIn('role', ['Teacher', 'teacher']);
            })->orderBy('name')->get(),
            'editing' => null,
        ], $data);
    }
}
