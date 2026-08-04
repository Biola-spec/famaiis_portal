<?php

namespace App\Http\Controllers\Backend\Marks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignStudent;
use App\Models\User;
use App\Models\DiscountStudent;

use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\StudentGroup;
use App\Models\StudentShift;
use DB;
use PDF;

use App\Models\StudentMarks;
use App\Models\Term;
use App\Models\ExamType;
use App\Models\AssignSubject;
use App\Models\TeacherAssignment;
use Auth;

class MarksController extends Controller
{

    public function MarksAdd(){
        $user = Auth::user();
        if ($user->role == 'Admin' || $user->hasRole('Admin')) {
    	    $data['years'] = StudentYear::all();
    	    $data['classes'] = StudentClass::all();
        } else {
            // Filter by teacher assignment
            $assigned = TeacherAssignment::where('teacher_id', $user->id)->get();
            $data['classes'] = StudentClass::whereIn('id', $assigned->pluck('class_id'))->get();
            $data['years'] = StudentYear::all(); // Teachers typically work across all academic years
        }
    	$data['activeYear'] = StudentYear::where('is_active', true)->first();
    	return view('backend.marks.marks_add',$data);

    }


    public function MarksStore(Request $request){

    	$studentcount = $request->student_id;
    	if ($studentcount) {
            $assign_subject = AssignSubject::find($request->assign_subject_id);
            $subject_id = $assign_subject ? $assign_subject->subject_id : null;

    		for ($i=0; $i <count($request->student_id) ; $i++) { 
                if ($request->marks[$i] !== null && $request->marks[$i] !== '') {
                    $data = New StudentMarks();
                    $data->year_id = $request->year_id;
                    $data->session_id = $request->year_id;
                    $data->class_id = $request->class_id;
                    $data->assign_subject_id = $request->assign_subject_id;
                    $data->subject_id = $subject_id;
                    $data->term = $request->term;
                    $data->student_id = $request->student_id[$i];
                    $data->id_no = $request->id_no[$i];
                    $data->marks = $request->marks[$i];
                    $data->save();
                }
    		} // end for loop
    	}// end if conditon

			$notification = array(
    		'message' => 'Student Marks Inserted Successfully',
    		'alert-type' => 'success'
    	);

    	return redirect()->back()->with($notification);

    }// end method



    public function MarksEdit(){
        $user = Auth::user();
        if ($user->role == 'Admin' || $user->hasRole('Admin')) {
    	    $data['years'] = StudentYear::all();
    	    $data['classes'] = StudentClass::all();
        } else {
            $assigned = TeacherAssignment::where('teacher_id', $user->id)->get();
            $data['classes'] = StudentClass::whereIn('id', $assigned->pluck('class_id'))->get();
            $data['years'] = StudentYear::all();
        }
        $data['activeYear'] = StudentYear::where('is_active', true)->first();

    	return view('backend.marks.marks_edit',$data);
    }


    public function MarksEditGetStudents(Request $request){
    	$year_id = $request->year_id;
    	$class_id = $request->class_id;
    	$assign_subject_id = $request->assign_subject_id;
        $term = $request->term;

        $assign_subject = AssignSubject::find($assign_subject_id);
        $subject_id = $assign_subject ? $assign_subject->subject_id : null;

    	$getStudent = StudentMarks::with(['student'])
            ->where('year_id',$year_id)
            ->where('class_id',$class_id)
            ->where(function($query) use ($assign_subject_id, $subject_id) {
                $query->where('assign_subject_id', $assign_subject_id);
                if ($subject_id) {
                    $query->orWhere('subject_id', $subject_id);
                }
            })
            ->where('term',$term)
            ->get();

    	return response()->json($getStudent);

    }


    public function MarksUpdate(Request $request){

    	$assign_subject = AssignSubject::find($request->assign_subject_id);
    	$subject_id = $assign_subject ? $assign_subject->subject_id : null;

    	StudentMarks::where('year_id',$request->year_id)
            ->where('class_id',$request->class_id)
            ->where(function($q) use ($request, $subject_id) {
                $q->where('assign_subject_id', $request->assign_subject_id);
                if ($subject_id) {
                    $q->orWhere('subject_id', $subject_id);
                }
            })
            ->where('term',$request->term)
            ->delete();

        $studentcount = $request->student_id;
    	if ($studentcount) {
    		for ($i=0; $i <count($request->student_id) ; $i++) { 
                if ($request->marks[$i] !== null && $request->marks[$i] !== '') {
                    $data = New StudentMarks();
                    $data->year_id = $request->year_id;
                    $data->session_id = $request->year_id;
                    $data->class_id = $request->class_id;
                    $data->assign_subject_id = $request->assign_subject_id;
                    $data->subject_id = $subject_id;
                    $data->term = $request->term;
                    $data->student_id = $request->student_id[$i];
                    $data->id_no = $request->id_no[$i];
                    $data->marks = $request->marks[$i];
                    $data->save();
                }
    		} // end for loop
    	}// end if conditon

			$notification = array(
    		'message' => 'Student Marks Updated Successfully',
    		'alert-type' => 'success'
    	);

    	return redirect()->back()->with($notification);


    } // end marks







}
 
