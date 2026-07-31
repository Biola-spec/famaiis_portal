<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\StudentMarks;
use App\Models\AssignSubject;
use App\Models\MarksGrade;
use App\Models\AssignStudent;
use App\Models\SchoolSubject;
use App\Models\SchoolSection;
use DB;

class BroadsheetController extends Controller
{
    public function BroadsheetView()
    {
        $data['years'] = StudentYear::orderBy('id', 'desc')->get();
        $data['classes'] = StudentClass::all();
        $data['sections'] = SchoolSection::all();
        $data['terms'] = ['1st Term', '2nd Term', '3rd Term'];
        return view('backend.report.broadsheet.broadsheet_view', $data);
    }

    public function BroadsheetFullGet(Request $request)
    {
        $year_id = $request->year_id;
        $class_id = $request->class_id;
        $section_id = $request->section_id;
        $term = $request->term;

        $query = StudentMarks::with(['student', 'subject'])
            ->where('year_id', $year_id)
            ->where('class_id', $class_id)
            ->where('term', $term);

        if ($section_id) {
            $query->where('section_id', $section_id);
        }

        $marks = $query->get();

        if ($marks->isEmpty()) {
            return response()->json(['message' => 'No records found'], 404);
        }

        $subjects = $marks->pluck('subject')->unique('id')->values();
        $students = $marks->groupBy('student_id');

        $data = [];
        foreach ($students as $studentId => $studentMarks) {
            $student = $studentMarks->first()->student;
            $row = [
                'name' => $student->name ?? 'N/A',
                'id_no' => $studentMarks->first()->id_no,
                'subjects' => []
            ];
            foreach ($subjects as $sub) {
                $mark = $studentMarks->where('subject_id', $sub->id)->first();
                $row['subjects'][$sub->id] = $mark ? $mark->total_score : '-';
            }
            $data[] = $row;
        }

        return response()->json([
            'subjects' => $subjects,
            'students' => $data
        ]);
    }

    public function BroadsheetSubjectGet(Request $request)
    {
        $year_id = $request->year_id;
        $class_id = $request->class_id;
        $section_id = $request->section_id;
        $subject_id = $request->subject_id;
        $term = $request->term;

        $query = StudentMarks::with(['student'])
            ->where('year_id', $year_id)
            ->where('class_id', $class_id)
            ->where('subject_id', $subject_id)
            ->where('term', $term);

        if ($section_id) {
            $query->where('section_id', $section_id);
        }

        $allMarks = $query->orderBy('total_score', 'desc')->get();

        if ($allMarks->isEmpty()) {
            return response()->json(['message' => 'No data found for the selected criteria.'], 404);
        }

        // Calculate Stats
        $totalScores = $allMarks->pluck('total_score')->filter(fn($v) => is_numeric($v));
        $stats = [
            'average' => $totalScores->avg() ?? 0,
            'highest' => $totalScores->max() ?? 0,
            'lowest' => $totalScores->min() ?? 0,
            'count' => $totalScores->count(),
        ];

        // Assign Position within subject
        $rankedMarks = $allMarks->map(function ($mark, $index) {
            $mark->position = $index + 1;
            return $mark;
        });

        return response()->json([
            'marks' => $rankedMarks,
            'stats' => $stats
        ]);
    }

    public function BroadsheetCompareGet(Request $request)
    {
        $year_id = $request->year_id;
        $class_id = $request->class_id;
        $student_id = $request->student_id; // Optional: if provided, compare for single student
        $terms = $request->terms; // Array of terms

        if (!$terms || count($terms) < 2) {
            return response()->json(['message' => 'Please select at least two terms to compare.'], 400);
        }

        $query = StudentMarks::where('year_id', $year_id)
            ->where('class_id', $class_id)
            ->whereIn('term', $terms);

        if ($student_id) {
            $query->where('student_id', $student_id);
        }

        $marks = $query->get();

        // Group by term and subject
        $comparison = [];
        $subjects = $marks->pluck('subject_id')->unique();
        $subjectNames = SchoolSubject::whereIn('id', $subjects)->pluck('name', 'id');

        foreach ($subjects as $subId) {
            $subData = [
                'subject' => $subjectNames[$subId] ?? 'Unknown',
                'terms' => []
            ];
            foreach ($terms as $term) {
                $termMark = $marks->where('subject_id', $subId)->where('term', $term);
                $avg = $termMark->avg('total_score') ?? 0;
                $subData['terms'][$term] = round($avg, 2);
            }
            $comparison[] = $subData;
        }

        // Overall performance
        $overall = [];
        foreach ($terms as $term) {
            $termMarks = $marks->where('term', $term);
            $overall[$term] = round($termMarks->avg('total_score') ?? 0, 2);
        }

        return response()->json([
            'comparison' => $comparison,
            'overall' => $overall,
            'terms' => $terms
        ]);
    }

    public function BroadsheetExportCSV(Request $request)
    {
        $type = $request->type; // full, subject, comparison
        $year_id = $request->year_id;
        $class_id = $request->class_id;
        $section_id = $request->section_id;
        $term = $request->term;
        $subject_id = $request->subject_id;

        $filename = "broadsheet_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type, $year_id, $class_id, $section_id, $term, $subject_id, $request) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type == 'subject') {
                fputcsv($file, ['Student Name', 'Admission No', 'Class', 'Term', 'Subject', 'CA Score', 'Exam Score', 'Total Score', 'Grade', 'Position']);
                
                $query = StudentMarks::with(['student', 'student_class', 'subject'])
                    ->where('year_id', $year_id)
                    ->where('class_id', $class_id)
                    ->where('subject_id', $subject_id)
                    ->where('term', $term);

                if ($section_id) {
                    $query->where('section_id', $section_id);
                }

                $marks = $query->orderBy('total_score', 'desc')->get();

                foreach ($marks as $index => $m) {
                    fputcsv($file, [
                        $m->student->name ?? 'N/A',
                        $m->id_no,
                        $m->student_class->name ?? 'N/A',
                        $m->term,
                        $m->subject->name ?? 'N/A',
                        $m->ca_score,
                        $m->exam_score,
                        $m->total_score,
                        $m->grade,
                        $index + 1
                    ]);
                }
            } elseif ($type == 'comparison') {
                $terms = explode(',', $request->terms);
                $header = array_merge(['Subject'], $terms);
                fputcsv($file, $header);

                $query = StudentMarks::where('year_id', $year_id)
                    ->where('class_id', $class_id)
                    ->whereIn('term', $terms);
                
                if ($section_id) {
                    $query->where('section_id', $section_id);
                }

                $marks = $query->get();

                $subjects = $marks->pluck('subject_id')->unique();
                $subjectNames = SchoolSubject::whereIn('id', $subjects)->pluck('name', 'id');

                foreach ($subjects as $subId) {
                    $row = [$subjectNames[$subId] ?? 'Unknown'];
                    foreach ($terms as $t) {
                        $avg = $marks->where('subject_id', $subId)->where('term', $t)->avg('total_score') ?? 0;
                        $row[] = round($avg, 2);
                    }
                    fputcsv($file, $row);
                }
            } else {
                // Default: Full Broadsheet 
                $query = StudentMarks::with(['student', 'student_class', 'subject'])
                    ->where('year_id', $year_id)
                    ->where('class_id', $class_id)
                    ->where('term', $term);

                if ($section_id) {
                    $query->where('section_id', $section_id);
                }

                $marks = $query->get();

                $subjects = $marks->pluck('subject')->unique('id');
                $students = $marks->groupBy('student_id');

                $header = array_merge(['Student Name', 'ID Number'], $subjects->pluck('name')->toArray());
                fputcsv($file, $header);

                foreach ($students as $studentId => $studentMarks) {
                    $row = [
                        $studentMarks->first()->student->name ?? 'N/A',
                        $studentMarks->first()->id_no
                    ];
                    foreach ($subjects as $sub) {
                        $mark = $studentMarks->where('subject_id', $sub->id)->first();
                        $row[] = $mark ? $mark->total_score : '-';
                    }
                    fputcsv($file, $row);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
