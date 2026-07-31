<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StudentMarks;

use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\MarksGrade;


class MarkSheetController extends Controller
{
    public function MarkSheetView(){

    	$data['years'] = StudentYear::orderBy('id','desc')->get();
    	$data['classes'] = StudentClass::all();
        $data['sections'] = \App\Models\SchoolSection::all();
    	$data['terms'] = ['1st Term', '2nd Term', '3rd Term'];
    	return view('backend.report.marksheet.marksheet_view',$data);

    }


    public function MarkSheetGet(Request $request){

    	$year_id = $request->year_id;
    	$class_id = $request->class_id;
        $section_id = $request->section_id;
    	$term = $request->term;
    	$id_no = $request->id_no;

        // Parent Logic: Restriction
        if (auth()->user()->hasRole('Parent')) {
            $childIds = auth()->user()->children->pluck('id_no')->toArray();
            if (!in_array($id_no, $childIds)) {
                $notification = array(
                    'message' => 'Sorry, you can only view your children\'s results.',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
        }

        // Student Logic: Restriction
        if (auth()->user()->role === 'Student' || auth()->user()->hasRole('Student')) {
            if (auth()->user()->id_no !== $id_no) {
                $notification = array(
                    'message' => 'Sorry, you can only view your own results.',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
        }

        $query = StudentMarks::where('year_id',$year_id)
            ->where('class_id',$class_id)
            ->where('term',$term)
            ->where('id_no',$id_no);

        if ($section_id) {
            $query->where('section_id', $section_id);
        }

        $singleStudent = (clone $query)->first();

        if ($singleStudent) {
            $allMarksQuery = StudentMarks::with(['assign_subject.school_subject', 'subject', 'year', 'student_class', 'student.section.headTeacher'])
                ->where('year_id',$year_id)
                ->where('class_id',$class_id)
                ->where('term',$term)
                ->where('id_no',$id_no)
                ->whereNotNull('marks')
                ->where('marks', '!=', '');

            if ($section_id) {
                $allMarksQuery->where('section_id', $section_id);
            }

            $allMarks = $allMarksQuery->get();
            
            if ($allMarks->isEmpty()) {
                $notification = array(
                    'message' => 'No marks records found for this student in the selected criteria.',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
            
            $count_fail = $allMarks->where('marks', '<', 33)->count();

            $assessmentQuery = \App\Models\StudentAssessment::where('year_id', $year_id)
                ->where('class_id', $class_id)
                ->where('term', $term)
                ->where('student_id', $singleStudent->student_id);

            if ($section_id) {
                $assessmentQuery->where('section_id', $section_id);
            }

            $assessment = $assessmentQuery->first();

            $allGrades = MarksGrade::all();
            
            $markingConfigQuery = \App\Models\ClassMarkingSetting::where('class_id', $class_id)
                ->where(function($query) use ($term) {
                    $query->where('term', $term)
                        ->orWhereNull('term');
                })
                ->where('is_active', 1);

            if ($section_id) {
                $markingConfigQuery->where(function($q) use ($section_id) {
                    $q->where('section_id', $section_id)
                      ->orWhereNull('section_id');
                });
            }

            $markingConfig = $markingConfigQuery->orderBy('term', 'desc')
                ->orderBy('section_id', 'desc')
                ->first();

            $studentInfo = $singleStudent->student;
            $sectionInfo = $singleStudent->section;

            $classTeacher = \App\Models\AssignClassTeacher::with('teacher')
                ->where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->first();

            $academicSetting = \App\Models\AcademicSetting::first();
            $assessmentAreas = $academicSetting->assessment_areas ?? ['Punctuality', 'Attendance', 'Neatness', 'Politeness', 'Honesty', 'Relationship_with_peers'];

            return view('backend.report.marksheet.marksheet_pdf', compact('allMarks','allGrades','count_fail', 'assessment', 'year_id', 'class_id', 'term', 'markingConfig', 'studentInfo', 'sectionInfo', 'section_id', 'classTeacher', 'assessmentAreas'));

        } else {
            $notification = array(
                'message' => 'Criteria mismatch: No records found for Student ID: ' . $id_no . ' in the selected Year/Class/Exam.',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }





}
 