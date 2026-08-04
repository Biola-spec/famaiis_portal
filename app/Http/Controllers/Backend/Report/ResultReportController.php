<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StudentYear;
use App\Models\StudentClass;

use App\Models\StudentMarks;

use PDF;
use App\Models\AssignStudent;
use App\Models\AssignSubject;
use App\Models\TeacherAssignment;
use Auth;


class ResultReportController extends Controller
{
    public function ResultView(){
        $user = Auth::user();
        if ($user->role == 'Admin' || $user->hasRole('Admin')) {
    	    $data['years'] = StudentYear::all();
    	    $data['classes'] = StudentClass::all();
        } else {
            $assigned = TeacherAssignment::where('teacher_id', $user->id)->get();
            $data['years'] = StudentYear::all();
            $data['classes'] = StudentClass::whereIn('id', $assigned->pluck('class_id'))->get();
        }
    	$data['terms'] = ['1st Term', '2nd Term', '3rd Term'];
    	return view('backend.report.student_result.student_result_view',$data);

    }


    public function ResultGet(Request $request){

    	$year_id = $request->year_id;
    	$class_id = $request->class_id;
        $section_id = $request->section_id;
    	$term = $request->term;

        $query = StudentMarks::with(['student', 'subject', 'year', 'student_class'])
            ->where('year_id',$year_id)
            ->where('class_id',$class_id)
            ->where('term',$term);
        
        if ($section_id) {
            $query->where('section_id', $section_id);
        }

        $allMarks = $query->get();

        if ($allMarks->isEmpty()) {
            $notification = array(
                'message' => 'Sorry, no records found for these criteria.',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }

        $data['allMarks'] = $allMarks;
        $data['subjects'] = $allMarks->pluck('subject')->unique('id');
        $data['students'] = $allMarks->groupBy('student_id');
        $data['year'] = StudentYear::find($year_id);
        $data['class'] = StudentClass::find($class_id);
        $data['term'] = $term;

        $pdf = PDF::loadView('backend.report.broadsheet.broadsheet_pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('broadsheet.pdf');
    } // end Method 



    public function IdcardView(){
    	$data['years'] = StudentYear::all();
    	$data['classes'] = StudentClass::all();
    	return view('backend.report.idcard.idcard_view',$data);
    }


    public function IdcardGet(Request $request){
    	$year_id = $request->year_id;
    	$class_id = $request->class_id;

    	$check_data = AssignStudent::where('year_id',$year_id)->where('class_id',$class_id)->first();

    if ($check_data == true) {
    	$data['allData'] = AssignStudent::where('year_id',$year_id)->where('class_id',$class_id)->get();
    	// dd($data['allData']->toArray());

    $pdf = PDF::loadView('backend.report.idcard.idcard_pdf', $data);
	return $pdf->stream('document.pdf');

    }else{

    	$notification = array(
    		'message' => 'Sorry These Criteria Does not match',
    		'alert-type' => 'error'
    	);

    	return redirect()->back()->with($notification);

    }


    }// end method 



}
 
