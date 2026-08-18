<?php

namespace App\Services;

use App\Models\AssignClassTeacher;
use App\Models\AcademicSetting;
use App\Models\ClassMarkingSetting;
use App\Models\MarksGrade;
use App\Models\SiteSetting;
use App\Models\SchoolSetting;
use App\Models\StudentAssessment;
use App\Models\StudentMarks;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\Schema;

class ReportCardService
{
    /**
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function render(int $yearId, int $classId, ?int $sectionId, string $term, string $idNo, bool $forPdf = false)
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

        $attendance = [
            'opened' => 0,
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
            'percentage' => 0,
        ];

        if (Schema::hasTable('student_attendances')) {
            $attendanceQuery = StudentAttendance::where('student_id', $singleStudent->student_id)
                ->where('year_id', $yearId)
                ->where('class_id', $classId);

            if ($sectionId) {
                $attendanceQuery->where(function ($query) use ($sectionId) {
                    $query->where('section_id', $sectionId)->orWhereNull('section_id');
                });
            }

            $attendanceRows = $attendanceQuery->get();
            $attendance['opened'] = $attendanceRows->count();
            $attendance['present'] = $attendanceRows->filter(fn ($row) => strtolower($row->attend_status) === 'present')->count();
            $attendance['absent'] = $attendanceRows->filter(fn ($row) => strtolower($row->attend_status) === 'absent')->count();
            $attendance['leave'] = $attendanceRows->filter(fn ($row) => strtolower($row->attend_status) === 'leave')->count();
            $attendance['percentage'] = $attendance['opened'] > 0
                ? round(($attendance['present'] / $attendance['opened']) * 100, 2)
                : 0;
        }

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

        $setting = SchoolSetting::first() ?: SiteSetting::first();
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
            'setting',
            'forPdf',
            'assessmentAreas',
            'attendance'
        ) + [
            'year_id' => $yearId,
            'class_id' => $classId,
            'term' => $term,
            'section_id' => $sectionId,
        ]);
    }
}
