<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentClass;
use App\Models\SchoolSection;

class StudentClassController extends Controller
{
    public function ViewStudent(){
    	$data['allData'] = StudentClass::with(['section'])->get();
    	return view('backend.setup.student_class.view_class',$data);
    }


    public function StudentClassAdd(){
        $data['sections'] = SchoolSection::all();
    	return view('backend.setup.student_class.add_class', $data);
    }


    public function StudentClassStore(Request $request){

    	$validatedData = $request->validate([
    		'name' => 'required|unique:student_classes,name',
    		'section_id' => 'required'
    	]);

    	$data = new StudentClass();
    	$data->name = $request->name;
        $data->section_id = $request->section_id;
    	$data->save();

    	$notification = array(
    		'message' => 'Student Class Inserted Successfully',
    		'alert-type' => 'success'
    	);

    	return redirect()->route('student.class.view')->with($notification);

    }



    public function StudentClassEdit($id){
    	$data['editData'] = StudentClass::find($id);
        $data['sections'] = SchoolSection::all();
    	return view('backend.setup.student_class.edit_class', $data);
    }


    public function StudentClassUpdate(Request $request,$id){

		$data = StudentClass::find($id);
     
     $validatedData = $request->validate([
    		'name' => 'required|unique:student_classes,name,'.$data->id,
            'section_id' => 'required'
    	]);

    	
    	$data->name = $request->name;
        $data->section_id = $request->section_id;
    	$data->save();

    	$notification = array(
    		'message' => 'Student Class Updated Successfully',
    		'alert-type' => 'success'
    	);

    	return redirect()->route('student.class.view')->with($notification);
    }


    public function StudentClassDelete($id){
    	$user = StudentClass::find($id);
    	$user->delete();

    	$notification = array(
    		'message' => 'Student Class Deleted Successfully',
    		'alert-type' => 'info'
    	);

    	return redirect()->route('student.class.view')->with($notification);

    }



}
