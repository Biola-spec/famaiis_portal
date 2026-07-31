<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AssignClassTeacher;
use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\SchoolSection;
use App\Models\User;

class AssignClassTeacherController extends Controller
{
    public function ViewAssignTeacher(){
        $data['allData'] = AssignClassTeacher::all();
    	return view('backend.setup.assign_class_teacher.view_class_teacher',$data);
    }

     public function AddAssignTeacher(){
    	$data['classes'] = StudentClass::all();
        $data['years'] = StudentYear::all();
        $data['sections'] = SchoolSection::all();
        $data['teachers'] = User::where('usertype', 'Teacher')
            ->orWhere('role', 'Teacher')
            ->orWhereHas('roles', function($q){
                $q->where('name', 'Teacher');
            })->get();
    	return view('backend.setup.assign_class_teacher.add_class_teacher',$data);
    }

	public function StoreAssignTeacher(Request $request){
        $validatedData = $request->validate([
    		'class_id' => 'required',
    		'teacher_id' => 'required',
    	]);

        $teacherCount = count($request->teacher_id);
        if ($teacherCount != NULL) {
            for ($i=0; $i < $teacherCount; $i++) { 
                // Check if assignment exists
                $exists = AssignClassTeacher::where('class_id', $request->class_id)
                            ->where('student_year_id', $request->student_year_id)
                            ->where('section_id', $request->section_id)
                            ->where('teacher_id', $request->teacher_id[$i])->first();
                
                if (!$exists) {
                    $assign = new AssignClassTeacher();
                    $assign->class_id = $request->class_id;
                    $assign->section_id = $request->section_id;
                    $assign->teacher_id = $request->teacher_id[$i];
                    $assign->student_year_id = $request->student_year_id;
                    $assign->save();
                }
            }
        }

        $notification = array(
            'message' => 'Class Teacher Assigned Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('assign.class.teacher.view')->with($notification);
    } 

	 public function EditAssignTeacher($id){
	    $data['editData'] = AssignClassTeacher::find($id);
        $data['sections'] = SchoolSection::all();
        $data['years'] = StudentYear::all();
        if ($data['editData']->section_id) {
            $data['classes'] = StudentClass::where('section_id', $data['editData']->section_id)->get();
        } else {
            $data['classes'] = StudentClass::all();
        }
        $data['teachers'] = User::where('usertype', 'Teacher')
            ->orWhere('role', 'Teacher')
            ->orWhereHas('roles', function($q){
                $q->where('name', 'Teacher');
            })->get();
    	return view('backend.setup.assign_class_teacher.edit_class_teacher',$data);
	}

    public function UpdateAssignTeacher(Request $request,$id){
        $validatedData = $request->validate([
    		'class_id' => 'required',
    		'teacher_id' => 'required',
    	]);

        $assign = AssignClassTeacher::find($id);
        $assign->class_id = $request->class_id;
        $assign->section_id = $request->section_id;
        $assign->teacher_id = $request->teacher_id;
        $assign->student_year_id = $request->student_year_id;
        $assign->save();

        $notification = array(
            'message' => 'Class Teacher Assignment Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('assign.class.teacher.view')->with($notification);
    } 

    public function DeleteAssignTeacher($id){
        $assign = AssignClassTeacher::find($id);
        $assign->delete();

        $notification = array(
            'message' => 'Class Teacher Assignment Deleted Successfully',
            'alert-type' => 'info'
        );

        return redirect()->route('assign.class.teacher.view')->with($notification);
    }
}
