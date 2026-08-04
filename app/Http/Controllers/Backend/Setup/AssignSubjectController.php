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
use PDF;
use Auth;

class AssignSubjectController extends Controller
{
    public function ViewAssignSubject(){
        $data['allData'] = AssignSubject::with(['student_class', 'section'])->select('class_id', 'section_id')->groupBy('class_id', 'section_id')->get();
        $allAssignments = AssignSubject::with(['student_class', 'section', 'school_subject', 'teacher'])
            ->orderBy('class_id')
            ->orderBy('subject_id')
            ->get();
        $this->attachAssignedTeachers($allAssignments);
        $data['allAssignments'] = $allAssignments;
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

	    	$subjectIndexes = array_keys($request->subject_id ?? []);
	    	if (!empty($subjectIndexes)) {
	    		foreach ($subjectIndexes as $i) {
                    $this->saveAssignmentRow($request, (int) $i);

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
                $editData = AssignSubject::where('class_id',$class_id)->where('section_id', $section_id)->orderBy('subject_id','asc')->get();
            } else {
                $editData = AssignSubject::where('class_id',$class_id)->whereNull('section_id')->orderBy('subject_id','asc')->get();
            }
        $this->attachAssignedTeachers($editData);
        $data['editData'] = $editData;
	    	
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
    		 
    $subjectIndexes = array_keys($request->subject_id ?? []);
	
    		foreach ($subjectIndexes as $i) {
                $this->saveAssignmentRow($request, (int) $i, $request->class_id);

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
            $detailsData = AssignSubject::where('class_id',$class_id)->where('section_id', $section_id)->orderBy('subject_id','asc')->get();
        } else {
            $detailsData = AssignSubject::where('class_id',$class_id)->whereNull('section_id')->orderBy('subject_id','asc')->get();
        }
        $this->attachAssignedTeachers($detailsData);
        $data['detailsData'] = $detailsData;

   return view('backend.setup.assign_subject.details_assign_subject',$data);


 	}

    public function DeleteAssignSubject($id){
        $assignment = AssignSubject::findOrFail($id);

        TeacherAssignment::where('class_id', $assignment->class_id)
            ->where('subject_id', $assignment->subject_id)
            ->when($assignment->section_id, function ($query) use ($assignment) {
                $query->where('section_id', $assignment->section_id);
            }, function ($query) {
                $query->whereNull('section_id');
            })
            ->delete();

        $assignment->delete();

        $notification = array(
            'message' => 'Assignment deleted successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('assign.subject.view')->with($notification);
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
        $teacherIds = $request->input("teacher_id.$index", []);
        if (!is_array($teacherIds)) {
            $teacherIds = [$teacherIds];
        }
        $teacherIds = array_values(array_filter($teacherIds));
        $teacherId = $teacherIds[0] ?? null;

        if (!$classId || !$subjectId || !$teacherId) {
            return;
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

        $assignmentQuery = TeacherAssignment::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->when($request->section_id, function ($query) use ($request) {
                $query->where('section_id', $request->section_id);
            }, function ($query) {
                $query->whereNull('section_id');
            });

        $assignmentQuery->whereNotIn('teacher_id', $teacherIds)->delete();

        foreach ($teacherIds as $selectedTeacherId) {
            TeacherAssignment::updateOrCreate(
                [
                    'teacher_id' => $selectedTeacherId,
                    'class_id' => $classId,
                    'subject_id' => $subjectId,
                    'section_id' => $request->section_id,
                ],
                []
            );
        }
    }

    private function attachAssignedTeachers($assignments): void
    {
        if ($assignments->isEmpty()) {
            return;
        }

        $classIds = $assignments->pluck('class_id')->unique();
        $subjectIds = $assignments->pluck('subject_id')->unique();
        $sectionIds = $assignments->pluck('section_id')->filter()->unique();

        $teacherAssignments = TeacherAssignment::with('teacher')
            ->whereIn('class_id', $classIds)
            ->whereIn('subject_id', $subjectIds)
            ->where(function ($query) use ($sectionIds) {
                $query->whereNull('section_id');
                if ($sectionIds->isNotEmpty()) {
                    $query->orWhereIn('section_id', $sectionIds);
                }
            })
            ->get()
            ->groupBy(function ($assignment) {
                return $this->assignmentKey($assignment->class_id, $assignment->subject_id, $assignment->section_id);
            });

        foreach ($assignments as $assignment) {
            $key = $this->assignmentKey($assignment->class_id, $assignment->subject_id, $assignment->section_id);
            $selectedAssignments = $teacherAssignments->get($key, collect());
            $assignment->setRelation('assignedTeachers', $selectedAssignments->pluck('teacher')->filter()->values());
            $assignment->assigned_teacher_ids = $selectedAssignments->pluck('teacher_id')->values()->all();
        }
    }

    private function assignmentKey($classId, $subjectId, $sectionId): string
    {
        return $classId . '|' . $subjectId . '|' . ($sectionId ?: 'null');
    }

    public function TeacherAssignmentPdf($teacher_id = null){
        $teacherId = $teacher_id ?: Auth::id();
        $teacher = User::with('designation')->findOrFail($teacherId);

        $teacherAssignments = TeacherAssignment::with(['studentClass', 'subject', 'section'])
            ->where('teacher_id', $teacherId)
            ->get();

        $assignSubjects = AssignSubject::with(['student_class', 'school_subject', 'section'])
            ->where('teacher_id', $teacherId)
            ->get();

        $subjectsMap = collect();
        $classesMap = collect();
        $assignmentsList = collect();

        foreach ($teacherAssignments as $ta) {
            $className = $ta->studentClass->name ?? 'N/A';
            $sectionName = $ta->section->name ?? 'All Sections';
            $subjectName = $ta->subject->name ?? 'N/A';

            if ($ta->studentClass) {
                $classesMap->put($ta->class_id, $className);
            }
            if ($ta->subject) {
                $subjectsMap->put($ta->subject_id, $subjectName);
            }

            $assignmentsList->push([
                'class_name' => $className,
                'section_name' => $sectionName,
                'subject_name' => $subjectName,
            ]);
        }

        foreach ($assignSubjects as $as) {
            $className = $as->student_class->name ?? 'N/A';
            $sectionName = $as->section->name ?? 'All Sections';
            $subjectName = $as->school_subject->name ?? 'N/A';

            if ($as->student_class) {
                $classesMap->put($as->class_id, $className);
            }
            if ($as->school_subject) {
                $subjectsMap->put($as->subject_id, $subjectName);
            }

            $exists = $assignmentsList->contains(function ($item) use ($className, $sectionName, $subjectName) {
                return $item['class_name'] === $className && $item['section_name'] === $sectionName && $item['subject_name'] === $subjectName;
            });

            if (!$exists) {
                $assignmentsList->push([
                    'class_name' => $className,
                    'section_name' => $sectionName,
                    'subject_name' => $subjectName,
                ]);
            }
        }

        $data['teacher'] = $teacher;
        $data['assignments'] = $assignmentsList;
        $data['unique_subjects'] = $subjectsMap;
        $data['unique_classes'] = $classesMap;
        $data['total_subjects'] = $subjectsMap->count();
        $data['total_classes'] = $classesMap->count();

        $setting = \DB::table('primary_settings')->first() ?? \DB::table('site_settings')->first();
        if (!$setting) {
            $setting = (object)[
                'school_name' => config('app.name', 'School Management System'),
                'school_email' => 'info@school.com',
                'logo' => null,
            ];
        }
        $data['setting'] = $setting;

        $pdf = PDF::loadView('backend.setup.assign_subject.teacher_assignment_pdf', $data);
        $pdf->setEncryption('', 'pass', ['copy', 'print']);
        return $pdf->stream('Teacher_Assignment_' . $teacher->name . '.pdf');
    }

}
 
