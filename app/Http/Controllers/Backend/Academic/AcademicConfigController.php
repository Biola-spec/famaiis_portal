<?php

namespace App\Http\Controllers\Backend\Academic;

use App\Http\Controllers\Controller;
use App\Models\ClassMarkingSetting;
use App\Models\SchoolSubject;
use App\Models\StudentClass;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicConfigController extends Controller
{
    public function index()
    {
        return view('backend.academic.config.index', [
            'teachers' => User::query()->where('usertype', 'Employee')->orderBy('name')->get(),
            'classes' => StudentClass::query()->orderBy('name')->get(),
            'subjects' => SchoolSubject::query()->orderBy('name')->get(),
            'assignments' => TeacherAssignment::query()->with(['teacher', 'studentClass', 'subject'])->latest()->get(),
            'markingSettings' => ClassMarkingSetting::query()->with(['studentClass', 'subject', 'session'])->latest()->get(),
            'terms' => ['1st Term', '2nd Term', '3rd Term'],
        ]);
    }

    public function storeTeacherAssignment(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => ['required', Rule::exists('users', 'id')->where('usertype', 'Employee')],
            'class_id' => 'required|exists:student_classes,id',
            'subject_id' => 'required|exists:school_subjects,id',
        ]);

        TeacherAssignment::query()->firstOrCreate($validated);

        return redirect()->back()->with([
            'message' => 'Teacher assignment saved.',
            'alert-type' => 'success',
        ]);
    }

    public function storeClassMarkingSetting(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'subject_id' => 'nullable|exists:school_subjects,id',
            'session_id' => 'nullable|exists:student_years,id',
            'term' => 'nullable|string|in:1st Term,2nd Term,3rd Term',
            'ca_count' => 'required|integer|min:1',
            'ca_labels' => 'nullable|array',
            'ca_weights' => 'nullable|array',
            'ca_weights.*' => 'numeric|min:0',
            'exam_weight' => 'required|numeric|min:0|max:100',
            'exam_label' => 'nullable|string|max:50',
            'project_enabled' => 'nullable|boolean',
            'total_score' => 'required|numeric|min:1|max:1000',
        ]);


        $calculatedCaWeight = 0;
        if (!empty($validated['ca_weights'])) {
            $calculatedCaWeight = array_sum($validated['ca_weights']);
        }

        $weightSum = $calculatedCaWeight + (float) $validated['exam_weight'];
        if ($weightSum > (float) $validated['total_score']) {
            return redirect()->back()->withErrors([
                'exam_weight' => 'Total CA weight (' . $calculatedCaWeight . ') + Exam weight (' . $validated['exam_weight'] . ') must not exceed total score (' . $validated['total_score'] . ').',
            ])->withInput();
        }

        ClassMarkingSetting::query()->updateOrCreate(
            [
                'class_id' => $validated['class_id'],
                'subject_id' => $validated['subject_id'] ?? null,
                'session_id' => $validated['session_id'] ?? null,
                'term' => $validated['term'] ?? null,
            ],
            [
                'ca_count' => $validated['ca_count'],
                'ca_labels' => $validated['ca_labels'] ?? null,
                'ca_weights' => $validated['ca_weights'] ?? null,
                'ca_weight' => $calculatedCaWeight,
                'exam_weight' => $validated['exam_weight'],
                'exam_label' => $validated['exam_label'] ?? 'Exam',
                'project_enabled' => (bool) ($validated['project_enabled'] ?? false),
                'total_score' => $validated['total_score'],
            ]
        );

        return redirect()->back()->with([
            'message' => 'Class marking setting saved.',
            'alert-type' => 'success',
        ]);
    }



    public function editClassMarkingSetting($id)
    {
        $setting = ClassMarkingSetting::findOrFail($id);
        
        return view('backend.academic.config.edit', [
            'setting' => $setting,
            'classes' => StudentClass::query()->orderBy('name')->get(),
            'subjects' => SchoolSubject::query()->orderBy('name')->get(),
            'terms' => ['1st Term', '2nd Term', '3rd Term'],
        ]);
    }

    public function updateClassMarkingSetting(Request $request, $id)
    {
        $setting = ClassMarkingSetting::findOrFail($id);

        $validated = $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'subject_id' => 'nullable|exists:school_subjects,id',
            'session_id' => 'nullable|exists:student_years,id',
            'term' => 'nullable|string|in:1st Term,2nd Term,3rd Term',
            'ca_count' => 'required|integer|min:1',
            'ca_labels' => 'nullable|array',
            'ca_weights' => 'nullable|array',
            'ca_weights.*' => 'numeric|min:0',
            'exam_weight' => 'required|numeric|min:0|max:100',
            'exam_label' => 'nullable|string|max:50',
            'project_enabled' => 'nullable|boolean',
            'total_score' => 'required|numeric|min:1|max:1000',
        ]);

        $calculatedCaWeight = 0;
        if (!empty($validated['ca_weights'])) {
            $calculatedCaWeight = array_sum($validated['ca_weights']);
        }

        $weightSum = $calculatedCaWeight + (float) $validated['exam_weight'];
        if ($weightSum > (float) $validated['total_score']) {
            return redirect()->back()->withErrors([
                'exam_weight' => 'Total CA weight (' . $calculatedCaWeight . ') + Exam weight (' . $validated['exam_weight'] . ') must not exceed total score (' . $validated['total_score'] . ').',
            ])->withInput();
        }

        $setting->update([
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'] ?? null,
            'session_id' => $validated['session_id'] ?? null,
            'term' => $validated['term'] ?? null,
            'ca_count' => $validated['ca_count'],
            'ca_labels' => $validated['ca_labels'] ?? null,
            'ca_weights' => $validated['ca_weights'] ?? null,
            'ca_weight' => $calculatedCaWeight,
            'exam_weight' => $validated['exam_weight'],
            'exam_label' => $validated['exam_label'] ?? 'Exam',
            'project_enabled' => (bool) ($validated['project_enabled'] ?? false),
            'total_score' => $validated['total_score'],
        ]);

        return redirect()->route('academic.config.index')->with([
            'message' => 'Class marking setting updated successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function destroyClassMarkingSetting($id)
    {
        $setting = ClassMarkingSetting::findOrFail($id);
        $setting->delete();

        return redirect()->route('academic.config.index')->with([
            'message' => 'Class marking setting deleted successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function toggleActiveClassMarkingSetting($id)
    {
        $setting = ClassMarkingSetting::findOrFail($id);
        $setting->is_active = !$setting->is_active;
        $setting->save();

        $status = $setting->is_active ? 'enabled' : 'disabled';
        return redirect()->route('academic.config.index')->with([
            'message' => "Class marking setting $status.",
            'alert-type' => 'info',
        ]);
    }

    public function storeAssessmentAreas(Request $request)
    {
        $request->validate([
            'assessment_areas' => 'required|string',
        ]);

        $areas = array_map('trim', explode(',', $request->assessment_areas));
        $areas = array_filter($areas); // Remove empty values

        $setting = \App\Models\AcademicSetting::first();
        if (!$setting) {
            $setting = new \App\Models\AcademicSetting();
        }
        
        $setting->assessment_areas = array_values($areas);
        $setting->save();

        return redirect()->back()->with([
            'message' => 'Assessment areas updated successfully.',
            'alert-type' => 'success',
        ]);
    }
}
