<?php

namespace App\Http\Controllers\Backend\Academic;

use App\Http\Controllers\Controller;
use App\Models\AssignStudent;
use App\Models\ClassMarkingSetting;

use App\Models\MarksGrade;
use App\Models\SchoolSubject;
use App\Models\StudentClass;
use App\Models\StudentMarks;
use App\Models\SchoolSection;
use App\Models\StudentYear;
use App\Models\AssignClassTeacher;
use App\Models\AssignSubject;
use App\Models\StudentSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class StructuredMarksController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('Parent')) {
            \Log::warning("StructuredMarksController::create - User is Parent: " . $user->id);
            abort(403, 'Unauthorized access to Academic Management');
        }

        $years = StudentYear::query()
            ->orderBy('name', 'desc')
            ->get();

        $sections = SchoolSection::query()
            ->orderBy('name')
            ->get();

        return view('backend.academic.marks.entry', [
            'sections' => $sections,
            'years' => $years,
            'currentSession' => getCurrentSession(),
        ]);
    }

    public function getClassesBySection(Request $request)
    {
        $request->validate([
            'section_id' => 'nullable|exists:school_sections,id',
        ]);

        $user = Auth::user();
        if ($user->hasRole('Admin') || $user->role === 'Admin') {
            $query = StudentClass::query();
            if ($request->section_id) {
                $query->where('section_id', $request->section_id);
            }
            $classes = $query->orderBy('name')->get();
        } else {
            $query = AssignSubject::query()
                ->where('teacher_id', $user->id);

            if ($request->section_id) {
                $query->where(function($q) use ($request) {
                    $q->where('section_id', $request->section_id)
                      ->orWhereNull('section_id');
                });
            }

            $classIds = $query->pluck('class_id')
                ->unique()
                ->values();

            $classes = StudentClass::query()
                ->whereIn('id', $classIds);
            
            if ($request->section_id) {
                $classes->where('section_id', $request->section_id);
            }

            $classes = $classes->orderBy('name')->get();
        }

        return response()->json($classes);
    }

    public function getSectionsByClass(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:student_classes,id',
        ]);

        $user = Auth::user();
        if ($user->hasRole('Admin') || $user->role === 'Admin') {
            $query = SchoolSection::query();
            if ($request->class_id) {
                $query->whereHas('classes', function($q) use ($request) {
                    $q->where('id', $request->class_id);
                });
            }
            $sections = $query->orderBy('name')->get();
        } else {
            $query = AssignClassTeacher::query()
                ->where('teacher_id', $user->id);

            if ($request->class_id) {
                $query->where('class_id', $request->class_id);
            }

            $sectionIds = $query->pluck('section_id')->unique()->values();
            $sections = SchoolSection::whereIn('id', $sectionIds)->orderBy('name')->get();
        }

        return response()->json($sections);
    }

    public function getAssignedSubjects(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
        ]);

        $user = Auth::user();
        if ($user->hasRole('Parent')) {
            abort(403, 'Unauthorized access to Academic Management');
        }

        $subjects = SchoolSubject::query()
            ->whereIn('id', function($q) use ($request, $user) {
                $q->select('subject_id')
                  ->from('assign_subjects')
                  ->where('class_id', $request->class_id);
                
                if ($request->section_id) {
                    $q->where(function($sq) use ($request) {
                        $sq->where('section_id', $request->section_id)
                           ->orWhereNull('section_id');
                    });
                }
                
                if (!$user->hasRole('Admin') && $user->role !== 'Admin') {
                    $q->where('teacher_id', $user->id);
                }
            })
            ->get();

        return response()->json($subjects);
    }

    public function getTerms(Request $request)
    {
        return response()->json([
            ['id' => '1st Term', 'name' => '1st Term'],
            ['id' => '2nd Term', 'name' => '2nd Term'],
            ['id' => '3rd Term', 'name' => '3rd Term']
        ]);
    }

    public function loadEntryContext(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'subject_id' => 'required|exists:school_subjects,id',
            'term' => ['required', Rule::in(['1st Term', '2nd Term', '3rd Term'])],
        ]);

        $user = Auth::user();
        if ($user->hasRole('Parent')) {
            abort(403, 'Unauthorized access to Academic Management');
        }
        if (!$user->hasRole('Admin', 'Super Admin')) {
            $assignmentQuery = AssignSubject::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $validated['class_id'])
                ->where('subject_id', $validated['subject_id']);

            if ($validated['section_id']) {
                $assignmentQuery->where(function($q) use ($validated) {
                    $q->where('section_id', $validated['section_id'])
                      ->orWhereNull('section_id');
                });
            }

            $exists = $assignmentQuery->exists();
            \Log::info("Teacher Assignment Check - User: " . $user->id . " | Class: " . $validated['class_id'] . " | Subject: " . $validated['subject_id'] . " | Section: " . ($validated['section_id'] ?? 'NULL') . " | Exists: " . ($exists ? 'YES' : 'NO'));

            abort_unless($exists, 403, 'Unauthorized class/subject assignment.');
        }

        $session = getCurrentSession();

        $setting = ClassMarkingSetting::query()
            ->where('class_id', $validated['class_id'])
            ->where(function ($query) use ($validated) {
                $query->whereNull('section_id')
                    ->orWhere('section_id', $validated['section_id']);
            })
            ->where(function ($query) use ($validated) {
                $query->whereNull('subject_id')
                    ->orWhere('subject_id', $validated['subject_id']);
            })
            ->where(function ($query) use ($session) {
                $query->whereNull('session_id')
                    ->orWhere('session_id', optional($session)->id);
            })
            ->where(function ($query) use ($request) {
                $query->whereNull('term')
                    ->orWhere('term', $request->term);
            })
            ->where('is_active', true)
            ->orderByDesc('term')
            ->orderByDesc('subject_id')
            ->orderByDesc('section_id')
            ->orderByDesc('session_id')
            ->first();

        if (!$setting) {
            return response()->json([
                'message' => 'No class marking setting found for this class/subject.',
            ], 422);
        }

        $studentQuery = AssignStudent::query()
            ->with('student')
            ->where('year_id', optional($session)->id)
            ->where('class_id', $validated['class_id']);

        if ($validated['section_id']) {
            $studentIds = StudentSection::where('section_id', $validated['section_id'])
                ->pluck('student_id');
            $studentQuery->whereIn('student_id', $studentIds);
        }

        $students = $studentQuery->get();

        $existingQuery = StudentMarks::query()
            ->where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('session_id', optional($session)->id)
            ->where('term', $validated['term']);

        if ($validated['section_id']) {
            $existingQuery->where('section_id', $validated['section_id']);
        }

        $existing = $existingQuery->get()->keyBy('student_id');

        return response()->json([
            'setting' => $setting,
            'students' => $students->map(function ($assigned) use ($existing) {
                $saved = $existing->get($assigned->student_id);

                return [
                    'student_id' => $assigned->student_id,
                    'id_no' => optional($assigned->student)->id_no,
                    'name' => optional($assigned->student)->name,
                    'fname' => optional($assigned->student)->fname,
                    'gender' => optional($assigned->student)->gender,
                    'existing' => $saved,
                ];
            })->values(),
        ]);
    }

    public function store(Request $request)
    {
        $session = getCurrentSession();

        $validated = $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'subject_id' => 'required|exists:school_subjects,id',
            'term' => ['required', Rule::in(['1st Term', '2nd Term', '3rd Term'])],
            'student_marks' => 'required|array|min:1',
            'student_marks.*.student_id' => 'required|exists:users,id',
            'student_marks.*.id_no' => 'nullable|string|max:255',
            'student_marks.*.ca' => 'required|array|min:1|max:10',
            'student_marks.*.ca.*' => 'nullable|numeric|min:0|max:100',
            'student_marks.*.exam_score' => 'nullable|numeric|min:0|max:100',
            'student_marks.*.project_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = Auth::user();
        if (!$user->hasRole('Admin', 'Super Admin')) {
            $assignmentQuery = AssignSubject::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $validated['class_id'])
                ->where('subject_id', $validated['subject_id']);

            if ($validated['section_id']) {
                $assignmentQuery->where(function($q) use ($validated) {
                    $q->where('section_id', $validated['section_id'])
                      ->orWhereNull('section_id');
                });
            }

            abort_unless($assignmentQuery->exists(), 403, 'Unauthorized class/subject assignment.');
        }

        $setting = ClassMarkingSetting::query()
            ->where('class_id', $validated['class_id'])
            ->where(function ($query) use ($validated) {
                $query->whereNull('section_id')
                    ->orWhere('section_id', $validated['section_id']);
            })
            ->where(function ($query) use ($validated) {
                $query->whereNull('subject_id')
                    ->orWhere('subject_id', $validated['subject_id']);
            })
            ->where(function ($query) use ($session) {
                $query->whereNull('session_id')
                    ->orWhere('session_id', optional($session)->id);
            })
            ->where(function ($query) use ($validated) {
                $query->whereNull('term')
                    ->orWhere('term', $validated['term']);
            })
            ->where('is_active', true)
            ->orderByDesc('term')
            ->orderByDesc('subject_id')
            ->orderByDesc('section_id')
            ->orderByDesc('session_id')
            ->firstOrFail();

        $validStudentQuery = AssignStudent::query()
            ->where('year_id', optional($session)->id)
            ->where('class_id', $validated['class_id']);

        if ($validated['section_id']) {
            $studentIds = StudentSection::where('section_id', $validated['section_id'])
                ->pluck('student_id');
            $validStudentQuery->whereIn('student_id', $studentIds);
        }

        $validStudentIds = $validStudentQuery->pluck('student_id')->flip();

        DB::transaction(function () use ($validated, $setting, $validStudentIds, $session) {
            foreach ($validated['student_marks'] as $row) {
                if (!$validStudentIds->has((int) $row['student_id'])) {
                    throw ValidationException::withMessages([
                        'student_marks' => 'One or more students are not in the selected class for the current session.',
                    ]);
                }

                $caInputsRaw = array_slice($row['ca'], 0, (int) $setting->ca_count);
                
                $isCaEmpty = true;
                foreach ($caInputsRaw as $caVal) {
                    if ($caVal !== null && $caVal !== '') {
                        $isCaEmpty = false;
                        break;
                    }
                }
                
                $examRaw = $row['exam_score'] ?? null;
                $projectRaw = $row['project_score'] ?? null;
                
                if ($isCaEmpty && ($examRaw === null || $examRaw === '') && ($projectRaw === null || $projectRaw === '')) {
                    StudentMarks::query()->where([
                        'student_id' => $row['student_id'],
                        'subject_id' => $validated['subject_id'],
                        'class_id' => $validated['class_id'],
                        'section_id' => $validated['section_id'],
                        'session_id' => optional($session)->id,
                        'term' => $validated['term'],
                    ])->delete();
                    
                    continue;
                }

                $caInputs = array_map(static fn ($value) => (float) ($value ?? 0), $caInputsRaw);
                $examScore = (float) $examRaw;
                $projectScore = (float) ($projectRaw ?? 0);

                if (count($caInputs) !== (int) $setting->ca_count) {
                    throw ValidationException::withMessages([
                        'student_marks' => 'Each student must have exactly '.$setting->ca_count.' CA entries.',
                    ]);
                }

                if (!$setting->project_enabled) {
                    $projectScore = 0;
                }

                if ($setting->ca_weights) {
                    $weightedCa = 0;
                    foreach ($caInputs as $i => $val) {
                        $maxW = (float) ($setting->ca_weights[$i] ?? 100);
                        if ($val > $maxW) {
                            throw ValidationException::withMessages([
                                'student_marks' => "Score {$val} exceeds max weight {$maxW} for CA " . ($i + 1) . " for student " . ($row['id_no'] ?? ''),
                            ]);
                        }
                        $weightedCa += $val;
                    }
                } else {
                    // Legacy fallback
                    $rawCaTotal = array_sum($caInputs);
                    if ($rawCaTotal > 100) {
                        throw ValidationException::withMessages([
                            'student_marks' => 'Total CA inputs per student cannot exceed 100.',
                        ]);
                    }
                    $weightedCa = $rawCaTotal;
                }

                if ($examScore > (float) $setting->exam_weight) {
                    throw ValidationException::withMessages([
                        'student_marks' => "Exam score {$examScore} exceeds max exam weight {$setting->exam_weight} for student " . ($row['id_no'] ?? ''),
                    ]);
                }
                
                $weightedExam = $examScore;
                
                $availableProjectWeight = max(0, (float) $setting->total_score - ((float) $setting->ca_weight + (float) $setting->exam_weight));
                if ($projectScore > $availableProjectWeight) {
                    throw ValidationException::withMessages([
                        'student_marks' => "Project score {$projectScore} exceeds max project weight {$availableProjectWeight} for student " . ($row['id_no'] ?? ''),
                    ]);
                }
                $weightedProject = $setting->project_enabled ? $projectScore : 0;
                
                $total = min((float) $setting->total_score, round($weightedCa + $weightedExam + $weightedProject, 2));

                $grade = $this->resolveGrade($total);

                StudentMarks::query()->updateOrCreate(
                    [
                        'student_id' => $row['student_id'],
                        'subject_id' => $validated['subject_id'],
                        'class_id' => $validated['class_id'],
                        'section_id' => $validated['section_id'],
                        'session_id' => optional($session)->id,
                        'term' => $validated['term'],
                    ],
                    [
                        'year_id' => optional($session)->id,
                        'id_no' => $row['id_no'] ?? null,
                        'ca_score' => round($weightedCa, 2),
                        'exam_score' => round($weightedExam, 2),
                        'project_score' => round($weightedProject, 2),
                        'ca_breakdown' => $caInputs,
                        'total_score' => $total,
                        'grade' => $grade,
                        'marks' => $total,
                    ]
                );
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Results saved successfully.',
            ]);
        }

        return redirect()->back()->with([
            'message' => 'Results saved successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function results(Request $request)
    {
        $session = getCurrentSession();

        $filters = $request->validate([
            'session_id' => ['nullable', Rule::exists('student_years', 'id')],
            'class_id' => ['nullable', Rule::exists('student_classes', 'id')],
            'section_id' => ['nullable', Rule::exists('school_sections', 'id')],
            'subject_id' => ['nullable', Rule::exists('school_subjects', 'id')],
            'term' => ['nullable', Rule::in(['1st Term', '2nd Term', '3rd Term'])],
        ]);

        $query = StudentMarks::query()
            ->with(['student', 'student_class', 'subject', 'year', 'section'])
            ->where('session_id', $filters['session_id'] ?? optional($session)->id);

        foreach (['class_id', 'section_id', 'subject_id', 'term'] as $filterKey) {
            if (!empty($filters[$filterKey])) {
                $query->where($filterKey, $filters[$filterKey]);
            }
        }

        if (!Auth::user()->hasRole('Admin', 'Super Admin')) {
            $allowed = AssignSubject::query()
                ->where('teacher_id', Auth::id())
                ->get(['class_id', 'subject_id', 'section_id']);

            $query->where(function ($subQuery) use ($allowed) {
                foreach ($allowed as $pair) {
                    $subQuery->orWhere(function ($q) use ($pair) {
                        $q->where('class_id', $pair->class_id)
                            ->where('subject_id', $pair->subject_id);
                        if ($pair->section_id) {
                            $q->where('section_id', $pair->section_id);
                        }
                    });
                }
            });
        }

        return view('backend.academic.results.index', [
            'results' => $query->orderByDesc('id')->paginate(50),
            'sessions' => StudentYear::query()->orderByDesc('id')->get(),
            'classes' => StudentClass::query()->orderBy('name')->get(),
            'sections' => SchoolSection::query()->orderBy('name')->get(),
            'subjects' => SchoolSubject::query()->orderBy('name')->get(),
            'terms' => ['1st Term', '2nd Term', '3rd Term'],
            'filters' => $filters,
            'currentSession' => $session,
        ]);
    }

    private function resolveGrade(float $score): ?string
    {
        $grade = MarksGrade::query()
            ->where('start_marks', '<=', $score)
            ->where('end_marks', '>=', $score)
            ->first();

        return $grade?->grade_name;
    }
}
