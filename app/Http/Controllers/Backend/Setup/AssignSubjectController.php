<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSubject;
use App\Models\StudentClass; 
use App\Models\SchoolSection;
use App\Models\AssignSubject;
use App\Models\TeacherAssignment;
use App\Models\User;

class AssignSubjectController extends Controller
{
    public function ViewAssignSubject(){
        $data['allData'] = AssignSubject::with(['student_class', 'section'])->select('class_id', 'section_id')->groupBy('class_id', 'section_id')->get();
        $data['allAssignments'] = AssignSubject::with(['student_class', 'section', 'school_subject', 'teacher'])
            ->orderBy('class_id')
            ->orderBy('subject_id')
            ->get();
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
                    $this->saveAssignmentRow($request, $i);

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
	
    		for ($i=0; $i <$countClass ; $i++) { 
                $this->saveAssignmentRow($request, $i, $request->class_id);

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

    private function saveAssignmentRow(Request $request, int $index, $defaultClassId = null): void
    {
        $classId = is_array($request->class_id) ? $request->class_id[$index] : ($defaultClassId ?? $request->class_id);
        $subjectId = $request->subject_id[$index] ?? null;
        $teacherId = $request->teacher_id[$index] ?? null;

        if (!$classId || !$subjectId || !$teacherId) {
            return;
        }

        $existingAssignment = AssignSubject::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->when($request->section_id, function ($query) use ($request) {
                $query->where('section_id', $request->section_id);
            }, function ($query) {
                $query->whereNull('section_id');
            })
            ->first();

        if ($existingAssignment && $existingAssignment->teacher_id && (int) $existingAssignment->teacher_id !== (int) $teacherId) {
            TeacherAssignment::where('teacher_id', $existingAssignment->teacher_id)
                ->where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->delete();
        }

        AssignSubject::updateOrCreate(
            [
                'class_id' => $classId,
                'section_id' => $request->section_id,
                'subject_id' => $subjectId,
            ],
            [
                'teacher_id' => $teacherId,
                'full_mark' => $request->full_mark[$index] ?? 0,
                'pass_mark' => $request->pass_mark[$index] ?? 0,
                'subjective_mark' => $request->subjective_mark[$index] ?? 0,
            ]
        );

        TeacherAssignment::updateOrCreate(
            [
                'teacher_id' => $teacherId,
                'class_id' => $classId,
                'subject_id' => $subjectId,
            ],
            []
        );
    }




}
 
