<?php

namespace App\Services;

use App\Models\AssignClassTeacher;
use App\Models\AcademicSetting;
use App\Models\ClassMarkingSetting;
use App\Models\MarksGrade;
use App\Models\StudentAssessment;
use App\Models\StudentMarks;

class ReportCardService
{
    /**
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function render(int $yearId, int $classId, ?int $sectionId, string $term, string $idNo)
    {
        $query = StudentMarks::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('term', $term)
            ->where('id_no', $idNo);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $singleStudent = (clone $query)->first();

        if (!$singleStudent) {
            return redirect()->back()->with([
                'message' => 'No records found for this student in the selected session, class, and term.',
                'alert-type' => 'error',
            ]);
        }

        $allMarksQuery = StudentMarks::with(['assign_subject.school_subject', 'subject', 'year', 'student_class', 'student.section.headTeacher'])
            ->where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('term', $term)
            ->where('id_no', $idNo)
            ->whereNotNull('marks')
            ->where('marks', '!=', '');

        if ($sectionId) {
            $allMarksQuery->where('section_id', $sectionId);
        }

        $allMarks = $allMarksQuery->get();

        if ($allMarks->isEmpty()) {
            return redirect()->back()->with([
                'message' => 'No marks records found for this student in the selected criteria.',
                'alert-type' => 'error',
            ]);
        }

        $count_fail = $allMarks->where('marks', '<', 33)->count();

        $assessmentQuery = StudentAssessment::where('year_id', $yearId)
            ->where('class_id', $classId)
            ->where('term', $term)
            ->where('student_id', $singleStudent->student_id);

        if ($sectionId) {
            $assessmentQuery->where('section_id', $sectionId);
        }

        $assessment = $assessmentQuery->first();
        $allGrades = MarksGrade::all();

        $markingConfigQuery = ClassMarkingSetting::where('class_id', $classId)
            ->where(function ($query) use ($term) {
                $query->where('term', $term)->orWhereNull('term');
            })
            ->where('is_active', 1);

        if ($sectionId) {
            $markingConfigQuery->where(function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId)->orWhereNull('section_id');
            });
        }

        $markingConfig = $markingConfigQuery->orderBy('term', 'desc')
            ->orderBy('section_id', 'desc')
            ->first();

        $studentInfo = $singleStudent->student;
        $sectionInfo = $singleStudent->section;

        $classTeacher = AssignClassTeacher::with('teacher')
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->first();

        $academicSetting = AcademicSetting::first();
        $assessmentAreas = $academicSetting->assessment_areas ?? ['Punctuality', 'Attendance', 'Neatness', 'Politeness', 'Honesty', 'Relationship_with_peers'];

        return view('backend.report.marksheet.marksheet_pdf', compact(
            'allMarks',
            'allGrades',
            'count_fail',
            'assessment',
            'markingConfig',
            'studentInfo',
            'sectionInfo',
            'classTeacher',
            'assessmentAreas'
        ) + [
            'year_id' => $yearId,
            'class_id' => $classId,
            'term' => $term,
            'section_id' => $sectionId,
        ]);
    }
}
