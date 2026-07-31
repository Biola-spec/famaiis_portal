<?php

namespace App\Http\Controllers\Backend;

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

use App\Models\AssignSubject;
use App\Models\StudentMarks;
use App\Models\ExamType;
use App\Models\StudentSection;
use Auth;

class DefaultController extends Controller
{
    public function GetSubject(Request $request){
    	$class_id = $request->class_id;
        $user = Auth::user();
        $query = AssignSubject::with(['school_subject'])->where('class_id',$class_id);

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }
        
        if ($user->role != 'Admin' && !$user->hasRole('Admin')) {
            $query->where('teacher_id', $user->id);
        }
        
    	$allData = $query->get();
    	return response()->json($allData);

    }


    public function GetStudents(Request $request){
    	$year_id = $request->year_id;
    	$class_id = $request->class_id;
        $user = Auth::user();

        $query = AssignStudent::with(['student'])->where('year_id',$year_id)->where('class_id',$class_id);

        if ($request->section_id) {
            $studentIds = StudentSection::where('section_id', $request->section_id)->pluck('student_id');
            $query->whereIn('student_id', $studentIds);
        }

        if ($user->hasRole('Parent')) {
            $childIds = $user->children->pluck('id')->toArray();
            $query->whereIn('student_id', $childIds);
        }

        // Restrict teachers to their assigned classes/sections
        if (!$user->hasRole('Admin') && ($user->usertype == 'Employee' || $user->hasRole('Teacher'))) {
            // Check if they are a subject teacher for this class/section
            $isAssignedSubject = \App\Models\AssignSubject::where('teacher_id', $user->id)
                ->where('class_id', $class_id);
            
            if ($request->section_id) {
                $isAssignedSubject->where(function($q) use ($request) {
                    $q->where('section_id', $request->section_id)->orWhereNull('section_id');
                });
            }
            
            // OR check if they are a class teacher for this class/section
            $isAssignedClass = \App\Models\AssignClassTeacher::where('teacher_id', $user->id)
                ->where('class_id', $class_id);
                
            if ($request->section_id) {
                $isAssignedClass->where('section_id', $request->section_id);
            }

            if (!$isAssignedSubject->exists() && !$isAssignedClass->exists()) {
                return response()->json([], 403);
            }
        }

    	$allData = $query->get();
    	return response()->json($allData);
    }

    public function SwitchSection(Request $request)
    {
        $section_id = $request->section_id;
        if ($section_id == 'all') {
            session()->forget('active_section_id');
        } else {
            session(['active_section_id' => $section_id]);
        }
        
        $notification = [
            'message'    => 'Academic section context updated successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
}
 