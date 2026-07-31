<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSubject;
use App\Models\StudentClass; 
use App\Models\SchoolSection;
use App\Models\AssignSubject;
use App\Models\User;

class AssignSubjectController extends Controller
{
    public function ViewAssignSubject(){
        $data['allData'] = AssignSubject::with(['student_class', 'section'])->select('class_id', 'section_id')->groupBy('class_id', 'section_id')->get();
    	return view('backend.setup.assign_subject.view_assign_subject',$data);
    }


     public function AddAssignSubject(){
    	$data['subjects'] = SchoolSubject::all();
        $data['sections'] = SchoolSection::all();
    	$data['classes'] = StudentClass::orderBy('name')->get();
        $data['teachers'] = $this->teacherQuery()->orderBy('name')->get();
    	return view('backend.setup.assign_subject.add_assign_subject',$data);
    }

    public function GetTeachersBySection(Request $request)
    {
        $request->validate([
            'section_id' => 'nullable|exists:school_sections,id',
        ]);

        $query = $this->teacherQuery();

        if ($request->section_id) {
            $query->where(function ($teacherQuery) use ($request) {
                $teacherQuery->where('section_id', $request->section_id)
                    ->orWhereHas('teacherSections', function ($q) use ($request) {
                        $q->where('school_sections.id', $request->section_id)
                            ->where('teacher_section.is_active', true);
                    });
            });
        }

        return response()->json(
            $query->orderBy('name')->get(['id', 'name'])
        );
    }


	public function StoreAssignSubject(Request $request){

	    	$subjectCount = count($request->subject_id);
	    	if ($subjectCount !=NULL) {
	    		for ($i=0; $i <$subjectCount ; $i++) { 
	    			$assign_subject = new AssignSubject();
	    			$assign_subject->class_id = is_array($request->class_id) ? $request->class_id[$i] : $request->class_id;
                    $assign_subject->section_id = $request->section_id;
	    			$assign_subject->subject_id = $request->subject_id[$i];
                    $assign_subject->teacher_id = $request->teacher_id[$i];
	    			$assign_subject->full_mark = $request->full_mark[$i] ?? 0;
	    			$assign_subject->pass_mark = $request->pass_mark[$i] ?? 0;
	    			$assign_subject->subjective_mark = $request->subjective_mark[$i] ?? 0;
	    			$assign_subject->save();

	    		} // End For Loop
	    	}// End If Condition

	    	$notification = array(
	    		'message' => 'Subject Assign Inserted Successfully',
	    		'alert-type' => 'success'
	    	);

	    	return redirect()->route('assign.subject.view')->with($notification);

	    }  // End Method 


	 public function EditAssignSubject($class_id, $section_id = null){
            if ($section_id) {
                $data['editData'] = AssignSubject::where('class_id',$class_id)->where('section_id', $section_id)->orderBy('subject_id','asc')->get();
            } else {
                $data['editData'] = AssignSubject::where('class_id',$class_id)->whereNull('section_id')->orderBy('subject_id','asc')->get();
            }
	    	
	    $data['subjects'] = SchoolSubject::all();
        $data['sections'] = SchoolSection::all();
        if ($section_id) {
            $data['classes'] = StudentClass::where('section_id', $section_id)->get();
        } else {
            $data['classes'] = StudentClass::all();
        }
        $data['teachers'] = User::where('usertype', 'Teacher')
            ->orWhere('role', 'Teacher')
            ->orWhereHas('roles', function($q){
                $q->where('name', 'Teacher');
            })->get();
    	return view('backend.setup.assign_subject.edit_assign_subject',$data);

	    }


public function UpdateAssignSubject(Request $request,$class_id){
    	if ($request->subject_id == NULL) {
       
        $notification = array(
    		'message' => 'Sorry You do not select any Subject',
    		'alert-type' => 'error'
    	);

    	return redirect()->back()->with($notification);
    		 
    	}else{
    		 
    $countClass = count($request->subject_id);
    if ($request->section_id) {
        AssignSubject::where('class_id',$class_id)->where('section_id', $request->section_id)->delete(); 
    } else {
        AssignSubject::where('class_id',$class_id)->whereNull('section_id')->delete(); 
    }
	
    		for ($i=0; $i <$countClass ; $i++) { 
    			$assign_subject = new AssignSubject();
	    			$assign_subject->class_id = $request->class_id;
                    $assign_subject->section_id = $request->section_id;
	    			$assign_subject->subject_id = $request->subject_id[$i];
                    $assign_subject->teacher_id = $request->teacher_id[$i];
	    			$assign_subject->full_mark = $request->full_mark[$i];
	    			$assign_subject->pass_mark = $request->pass_mark[$i];
	    			$assign_subject->subjective_mark = $request->subjective_mark[$i];
	    			$assign_subject->save();

    		} // End For Loop	 

    	}// end Else

       $notification = array(
    		'message' => 'Data Updated Successfully',
    		'alert-type' => 'success'
    	);

    	return redirect()->route('assign.subject.view')->with($notification);
    } // end Method 


	public function DetailsAssignSubject($class_id, $section_id = null){
        if ($section_id) {
            $data['detailsData'] = AssignSubject::where('class_id',$class_id)->where('section_id', $section_id)->orderBy('subject_id','asc')->get();
        } else {
            $data['detailsData'] = AssignSubject::where('class_id',$class_id)->whereNull('section_id')->orderBy('subject_id','asc')->get();
        }

   return view('backend.setup.assign_subject.details_assign_subject',$data);


 	}
    private function teacherQuery()
    {
        return User::where(function ($query) {
            $query->where('usertype', 'Teacher')
                ->orWhere('role', 'Teacher')
                ->orWhereHas('roles', function ($q) {
                    $q->where('name', 'Teacher');
                });
        });
    }




}
 
