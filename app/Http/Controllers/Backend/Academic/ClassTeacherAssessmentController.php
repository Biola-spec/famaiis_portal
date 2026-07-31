<?php

namespace App\Http\Controllers\Backend\Academic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AssignStudent;
use App\Models\StudentAssessment;
use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\AssignClassTeacher;

use App\Models\SchoolSection;
use App\Models\StudentSection;
use Illuminate\Support\Facades\Auth;

class ClassTeacherAssessmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('Admin') || $user->role === 'Admin') {
            $classes = StudentClass::query()->orderBy('name')->get();
            $sections = SchoolSection::all();
        } else {
            $assignments = AssignClassTeacher::query()
                ->where('teacher_id', $user->id)
                ->get();
            
            $classIds = $assignments->pluck('class_id')->unique()->values();
            $sectionIds = $assignments->pluck('section_id')->unique()->values();
            
            $classes = StudentClass::query()
                ->whereIn('id', $classIds)
                ->orderBy('name')
                ->get();

            $sections = SchoolSection::query()
                ->whereIn('id', $sectionIds)
                ->orderBy('name')
                ->get();
        }

        $years = StudentYear::query()
            ->orderBy('name', 'desc')
            ->get();
        
        $terms = ['1st Term', '2nd Term', '3rd Term'];

        $academicSetting = \App\Models\AcademicSetting::first();
        $assessmentAreas = $academicSetting->assessment_areas ?? ['Punctuality', 'Attendance', 'Neatness', 'Politeness', 'Honesty', 'Relationship_with_peers'];

        return view('backend.academic.assessment.entry', [
            'classes' => $classes,
            'sections' => $sections,
            'years' => $years,
            'terms' => $terms,
            'currentSession' => getCurrentSession(),
            'assessmentAreas' => $assessmentAreas,
        ]);
    }

    public function loadStudents(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'year_id' => 'required|exists:student_years,id',
            'term' => ['required', \Illuminate\Validation\Rule::in(['1st Term', '2nd Term', '3rd Term'])],
        ]);

        $user = Auth::user();
        $isSectionHead = SchoolSection::where('head_teacher_id', $user->id)->exists();

        if (!$user->hasRole('Admin') && $user->role !== 'Admin') {
            $assignmentQuery = AssignClassTeacher::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $validated['class_id']);

            if ($validated['section_id']) {
                $assignmentQuery->where(function($q) use ($validated) {
                    $q->where('section_id', $validated['section_id'])
                      ->orWhereNull('section_id');
                });
            }

            if (!$assignmentQuery->exists() && !$isSectionHead) {
                abort(403, 'Unauthorized. You are not assigned as a Class Teacher or Section Head for this class.');
            }
        }

        $studentQuery = AssignStudent::query()
            ->with('student')
            ->where('year_id', $validated['year_id'])
            ->where('class_id', $validated['class_id']);

        if ($validated['section_id']) {
            $studentIds = StudentSection::where('section_id', $validated['section_id'])->pluck('student_id');
            $studentQuery->whereIn('student_id', $studentIds);
        }

        $students = $studentQuery->get();

        $existingQuery = StudentAssessment::query()
            ->where('class_id', $validated['class_id'])
            ->where('year_id', $validated['year_id'])
            ->where('term', $validated['term']);

        if ($validated['section_id']) {
            $existingQuery->where('section_id', $validated['section_id']);
        }

        $existing = $existingQuery->get()->keyBy('student_id');

        return response()->json([
            'is_section_head' => $isSectionHead || $user->hasRole('Admin') || $user->role === 'Admin',
            'students' => $students->map(function ($assigned) use ($existing) {
                $saved = $existing->get($assigned->student_id);

                return [
                    'student_id' => $assigned->student_id,
                    'id_no' => optional($assigned->student)->id_no,
                    'name' => optional($assigned->student)->name,
                    'existing' => $saved,
                ];
            })->values(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'year_id' => 'required|exists:student_years,id',
            'term' => ['required', \Illuminate\Validation\Rule::in(['1st Term', '2nd Term', '3rd Term'])],
            'assessments' => 'required|array|min:1',
            'assessments.*.student_id' => 'required|exists:users,id',
            'assessments.*.teacher_comment' => 'nullable|string',
            'assessments.*.head_teacher_comment' => 'nullable|string',
            'assessments.*.cognitive_areas' => 'nullable|array',
        ]);

        $user = Auth::user();
        $isSectionHead = \App\Models\SchoolSection::where('head_teacher_id', $user->id)->exists();

        if (!$user->hasRole('Admin') && $user->role !== 'Admin') {
            $assignmentQuery = \App\Models\AssignClassTeacher::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $validated['class_id']);

            if ($validated['section_id']) {
                $assignmentQuery->where(function($q) use ($validated) {
                    $q->where('section_id', $validated['section_id'])
                      ->orWhereNull('section_id');
                });
            }

            if (!$assignmentQuery->exists() && !$isSectionHead) {
                return response()->json(['message' => 'Unauthorized access. You must be a Class Teacher or Section Head.'], 403);
            }
        }

        foreach ($validated['assessments'] as $row) {
            StudentAssessment::query()->updateOrCreate(
                [
                    'student_id' => $row['student_id'],
                    'class_id' => $validated['class_id'],
                    'section_id' => $validated['section_id'],
                    'year_id' => $validated['year_id'],
                    'term' => $validated['term'],
                ],
                [
                    'teacher_comment' => $row['teacher_comment'] ?? null,
                    'head_teacher_comment' => $row['head_teacher_comment'] ?? null,
                    'cognitive_areas' => $row['cognitive_areas'] ?? null,
                ]
            );
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Assessments saved successfully.']);
        }

        return redirect()->back()->with([
            'message' => 'Assessments saved successfully.',
            'alert-type' => 'success',
        ]);
    }
}
