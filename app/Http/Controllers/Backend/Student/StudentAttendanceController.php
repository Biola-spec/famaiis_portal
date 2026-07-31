<?php

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentAttendance;
use App\Models\User;
use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\SchoolSection;
use App\Models\AssignStudent;
use App\Models\AssignClassTeacher;
use Auth;
use DB;

class StudentAttendanceController extends Controller
{
    public function AttendanceView()
    {
        $teacher = Auth::user();
        $query = StudentAttendance::select('date', 'class_id', 'section_id')
            ->groupBy('date', 'class_id', 'section_id')
            ->orderBy('date', 'desc');

        if (!$teacher->hasRole('Admin')) {
            // Filter by classes assigned to the teacher
            $assignedClasses = AssignClassTeacher::where('teacher_id', $teacher->id)->get();
            $classIds = $assignedClasses->pluck('class_id')->toArray();
            $sectionIds = $assignedClasses->pluck('section_id')->toArray();
            
            $query->whereIn('class_id', $classIds);
            // Optional: further filter by section if applicable
        }

        $data['allData'] = $query->get();
        return view('backend.student.attendance.attendance_view', $data);
    }

    public function AttendanceAdd()
    {
        $teacher = Auth::user();
        if ($teacher->hasRole('Admin')) {
            $data['classes'] = StudentClass::all();
        } else {
            $assigned = AssignClassTeacher::where('teacher_id', $teacher->id)->with('studentClass')->get();
            $data['classes'] = $assigned->pluck('studentClass')->unique('id');
        }
        
        $data['years'] = StudentYear::where('is_active', 1)->get();
        return view('backend.student.attendance.attendance_add', $data);
    }

    public function GetStudents(Request $request)
    {
        $year_id = $request->year_id;
        $class_id = $request->class_id;
        $section_id = $request->section_id;

        $students = AssignStudent::with(['student'])
            ->where('year_id', $year_id)
            ->where('class_id', $class_id);
        
        if ($section_id) {
            $students->where('section_id', $section_id);
        }

        $allData = $students->get();
        return response()->json($allData);
    }

    public function AttendanceStore(Request $request)
    {
        StudentAttendance::where('date', date('Y-m-d', strtotime($request->date)))
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->delete();

        $countstudent = count($request->student_id);
        for ($i = 0; $i < $countstudent; $i++) {
            $attend_status = 'attend_status' . $i;
            $attend = new StudentAttendance();
            $attend->date = date('Y-m-d', strtotime($request->date));
            $attend->student_id = $request->student_id[$i];
            $attend->year_id = $request->year_id;
            $attend->class_id = $request->class_id;
            $attend->section_id = $request->section_id;
            $attend->attend_status = $request->$attend_status;
            $attend->save();
        }

        $notification = array(
            'message' => 'Student Attendance Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('student.attendance.view')->with($notification);
    }

    public function AttendanceEdit($date, $class_id, $section_id = null)
    {
        $data['editData'] = StudentAttendance::where('date', $date)
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->get();
        
        $data['classes'] = StudentClass::all();
        $data['years'] = StudentYear::all();
        $data['sections'] = SchoolSection::all();
        
        return view('backend.student.attendance.attendance_edit', $data);
    }

    public function AttendanceDetails($date, $class_id, $section_id = null)
    {
        $data['details'] = StudentAttendance::where('date', $date)
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->get();
            
        return view('backend.student.attendance.attendance_details', $data);
    }
}
