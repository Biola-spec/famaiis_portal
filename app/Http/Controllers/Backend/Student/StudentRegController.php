<?php

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignStudent;
use App\Models\User;
use App\Models\DiscountStudent;
use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\StudentGroup;
use App\Models\Term;
use App\Models\SchoolSection;
use DB;
use PDF;
use Illuminate\Support\Facades\Auth;

class StudentRegController extends Controller
{
    public function StudentRegView(Request $request){
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized access to Student Management');
        }
    	$data['years']    = StudentYear::all();
    	$data['classes']  = StudentClass::all();
        $data['groups']   = StudentGroup::all();
        $data['sections'] = SchoolSection::all();
        $data['year_id'] = $request->year_id;
        $data['class_id'] = $request->class_id;
        $data['search_query'] = $request->search_query;

    	$data['allData']  = AssignStudent::with(['student', 'student_year', 'student_class', 'discount'])
            ->whereHas('student')
            ->when($request->year_id, function($query) use ($request) {
                return $query->where('year_id', $request->year_id);
            })
            ->when($request->class_id, function($query) use ($request) {
                return $query->where('class_id', $request->class_id);
            })
            ->when($request->search_query, function($query) use ($request) {
                return $query->whereHas('student', function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('first_name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('surname', 'like', '%'.$request->search_query.'%')
                      ->orWhere('middle_name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('id_no', 'like', '%'.$request->search_query.'%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    	return view('backend.student.student_reg.student_view', $data);
    }

    public function StudentClassYearWise(Request $request){
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized access to Student Management');
        }
    	$data['years']   = StudentYear::all();
    	$data['classes'] = StudentClass::all();
    	$data['groups']  = StudentGroup::all();
    	$data['sections']= SchoolSection::all();

    	$data['year_id']  = $request->year_id;
    	$data['class_id'] = $request->class_id;
        $data['search_query'] = $request->search_query;

    	$data['allData'] = AssignStudent::with(['student', 'student_year', 'student_class', 'discount'])
            ->whereHas('student')
            ->where('year_id', $request->year_id)
            ->where('class_id', $request->class_id)
            ->when($request->search_query, function($query) use ($request) {
                return $query->whereHas('student', function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('first_name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('surname', 'like', '%'.$request->search_query.'%')
                      ->orWhere('middle_name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('id_no', 'like', '%'.$request->search_query.'%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

    	$data['search'] = true;
    	return view('backend.student.student_reg.student_view', $data);
    }

    public function StudentRegLiveSearch(Request $request)
    {
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized access to Student Management');
        }

        $allData = $this->studentRegistrationQuery($request)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'rows' => view('backend.student.student_reg.partials.student_rows', compact('allData'))->render(),
            'pagination' => $allData->links('pagination::bootstrap-4')->render(),
            'showing' => $allData->total() > 0
                ? 'Showing '.$allData->firstItem().' to '.$allData->lastItem().' of '.$allData->total().' students'
                : 'No students found',
        ]);
    }

    private function studentRegistrationQuery(Request $request)
    {
        return AssignStudent::with(['student', 'student_year', 'student_class', 'discount'])
            ->whereHas('student')
            ->when($request->year_id, function($query) use ($request) {
                return $query->where('year_id', $request->year_id);
            })
            ->when($request->class_id, function($query) use ($request) {
                return $query->where('class_id', $request->class_id);
            })
            ->when($request->search_query, function($query) use ($request) {
                return $query->whereHas('student', function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('first_name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('surname', 'like', '%'.$request->search_query.'%')
                      ->orWhere('middle_name', 'like', '%'.$request->search_query.'%')
                      ->orWhere('id_no', 'like', '%'.$request->search_query.'%');
                });
            });
    }

    public function StudentRegAdd(){
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized access to Student Management');
        }
    	$data['years']   = StudentYear::all();
    	$data['classes'] = StudentClass::all();
    	$data['groups']  = StudentGroup::all();
    	$data['terms']   = Term::all();
        $data['parents'] = User::select('id', 'name', 'email')
            ->where('role', 'Parent')
            ->orWhere('usertype', 'Parent')
            ->orWhereHas('roles', function($q){
                $q->where('name', 'Parent');
            })->get();
        $data['sections'] = SchoolSection::all();
    	return view('backend.student.student_reg.student_add', $data);
    }

    public function StudentRegStore(Request $request){
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized action');
        }
    	DB::transaction(function() use($request){
    	    $user = new User();
    	    $user->id_no     = $request->id_no;
    	    $user->password  = bcrypt($request->password);
    	    $user->email     = $request->email;
    	    $user->usertype  = 'Student';
    	    $user->code      = $request->password;
    	    $user->name      = $request->first_name . ' ' . $request->surname . ' ' . $request->middle_name;
            $user->first_name = $request->first_name;
            $user->surname    = $request->surname;
            $user->middle_name= $request->middle_name;
    	    $user->mobile    = $request->mobile;
    	    $user->address   = $request->address;
    	    $user->nin       = $request->nin;
    	    $user->state     = $request->state;
    	    $user->lga       = $request->lga;
            $user->country  = $request->country;
    	    $user->gender    = $request->gender;
    	    $user->section_id= $request->section_id ?? ($request->section_ids[0] ?? null); // Primary section
    	    $user->dob       = $request->dob ? date('Y-m-d', strtotime($request->dob)) : null;

    	    if ($request->file('image')) {
    	    	$file     = $request->file('image');
    	    	$filename = date('YmdHi').$file->getClientOriginalName();
    	    	$file->move(public_path('upload/student_images'), $filename);
    	    	$user['image'] = $filename;
    	    }
  	        $user->save();

            // Link to Parent if selected
            if ($request->parent_id) {
                $user->parents()->sync($request->parent_id);
            }

            // Primary class assignment (AssignStudent)
            $assign_student = new AssignStudent();
            $assign_student->student_id = $user->id;
            $assign_student->year_id    = $request->year_id;
            $assign_student->class_id   = $request->class_id;
            $assign_student->group_id   = $request->group_id;
            $assign_student->save();

            // Enroll in student_section pivot (multi-section)
            if ($request->section_ids) {
                foreach ($request->section_ids as $section_id) {
                    DB::table('student_section')->insert([
                        'student_id'      => $user->id,
                        'section_id'      => $section_id,
                        'class_id'        => $request->class_id,
                        'year_id'         => $request->year_id,
                        'is_active'       => true,
                        'enrollment_date' => now()->toDateString(),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }

            $discount_student = new DiscountStudent();
            $discount_student->assign_student_id = $assign_student->id;
            $discount_student->fee_category_id   = '1';
            $discount_student->discount           = $request->discount ?? 0;
            $discount_student->save();
    	});

    	$notification = [
    	    'message'    => 'Student Registration Inserted Successfully',
    	    'alert-type' => 'success',
    	];
    	return redirect()->route('student.registration.view')->with($notification);
    }

    public function StudentRegEdit($student_id){
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized access');
        }
    	$data['years']   = StudentYear::all();
    	$data['classes'] = StudentClass::all();
    	$data['groups']  = StudentGroup::all();
        $data['parents'] = User::select('id', 'name', 'email')
            ->where('role', 'Parent')
            ->orWhere('usertype', 'Parent')
            ->get();
        $data['sections']= SchoolSection::all();
        // Enrolled sections via pivot
        $data['enrolledSections'] = DB::table('student_section')->where('student_id', $student_id)->get();

    	$data['editData'] = AssignStudent::with(['student','discount'])->where('student_id', $student_id)->first();
    	return view('backend.student.student_reg.student_edit', $data);
    }

    public function StudentRegUpdate(Request $request, $student_id){
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized action');
        }
    	DB::transaction(function() use($request, $student_id){
    	    $user = User::where('id', $student_id)->first();
    	    $user->id_no      = $request->id_no;
    	    $user->name       = $request->first_name . ' ' . $request->surname . ' ' . $request->middle_name;
            $user->first_name  = $request->first_name;
            $user->surname     = $request->surname;
            $user->middle_name = $request->middle_name;
    	    $user->mobile     = $request->mobile;
    	    $user->address    = $request->address;
    	    $user->email      = $request->email;
            if ($request->password) {
                $user->password = bcrypt($request->password);
                $user->code     = $request->password;
            }
    	    $user->nin       = $request->nin;
    	    $user->state     = $request->state;
    	    $user->lga       = $request->lga;
            $user->country  = $request->country;
    	    $user->gender    = $request->gender;
    	    $user->section_id= $request->section_id ?? ($request->section_ids[0] ?? null); // Primary section
    	    $user->dob       = $request->dob ? date('Y-m-d', strtotime($request->dob)) : null;

    	    if ($request->file('image')) {
    	    	$file = $request->file('image');
    	    	@unlink(public_path('upload/student_images/'.$user->image));
    	    	$filename = date('YmdHi').$file->getClientOriginalName();
    	    	$file->move(public_path('upload/student_images'), $filename);
    	    	$user['image'] = $filename;
    	    }
  	        $user->save();

            if ($request->parent_id) {
                $user->parents()->sync($request->parent_id);
            }

            $assign_student = AssignStudent::where('id', $request->id)->where('student_id', $student_id)->first();
            $assign_student->year_id  = $request->year_id;
            $assign_student->class_id = $request->class_id;
            $assign_student->group_id = $request->group_id;
            $assign_student->save();

            $discount_student = DiscountStudent::where('assign_student_id', $request->id)->first();
            $discount_student->discount = $request->discount ?? 0;
            $discount_student->save();

            // Sync sections
            if ($request->section_ids) {
                // For simplicity, we clear and re-insert for the current year/class
                DB::table('student_section')
                    ->where('student_id', $student_id)
                    ->where('year_id', $request->year_id)
                    ->where('class_id', $request->class_id)
                    ->delete();

                foreach ($request->section_ids as $section_id) {
                    DB::table('student_section')->insert([
                        'student_id'      => $user->id,
                        'section_id'      => $section_id,
                        'class_id'        => $request->class_id,
                        'year_id'         => $request->year_id,
                        'is_active'       => true,
                        'enrollment_date' => now()->toDateString(),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }
    	});

    	$notification = [
    	    'message'    => 'Student Registration Updated Successfully',
    	    'alert-type' => 'success',
    	];
    	return redirect()->route('student.registration.view')->with($notification);
    }

    public function StudentRegPromotion($student_id){
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized action');
        }
    	$data['years']   = StudentYear::all();
    	$data['classes'] = StudentClass::all();
    	$data['groups']  = StudentGroup::all();
    	$data['sections']= SchoolSection::all();

    	$data['editData'] = AssignStudent::with(['student','discount'])->where('student_id', $student_id)->first();
    	return view('backend.student.student_reg.student_promotion', $data);
    }

    public function StudentUpdatePromotion(Request $request, $student_id){
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Unauthorized action');
        }

        // Check if promoting to same year and same class
        $current = AssignStudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        if ($current && $current->year_id == $request->year_id && $current->class_id == $request->class_id) {
            $notification = [
                'message'    => 'You cannot promote a student to the same Class in the same Session.',
                'alert-type' => 'error',
            ];
            return redirect()->back()->with($notification);
        }

    	DB::transaction(function() use($request, $student_id){
    	    $user = User::where('id', $student_id)->first();
    	    $user->name       = $request->first_name . ' ' . $request->surname . ' ' . $request->middle_name;
            $user->first_name  = $request->first_name;
            $user->surname     = $request->surname;
            $user->middle_name = $request->middle_name;
    	    $user->mobile    = $request->mobile;
    	    $user->address   = $request->address;
    	    $user->nin       = $request->nin;
    	    $user->state     = $request->state;
    	    $user->lga       = $request->lga;
            $user->country  = $request->country;
    	    $user->gender    = $request->gender;
    	    $user->section_id= $request->section_id ?? ($request->section_ids[0] ?? null);
    	    $user->dob       = $request->dob ? date('Y-m-d', strtotime($request->dob)) : null;

    	    if ($request->file('image')) {
    	    	$file = $request->file('image');
    	    	@unlink(public_path('upload/student_images/'.$user->image));
    	    	$filename = date('YmdHi').$file->getClientOriginalName();
    	    	$file->move(public_path('upload/student_images'), $filename);
    	    	$user['image'] = $filename;
    	    }
  	        $user->save();

            $assign_student = new AssignStudent();
            $assign_student->student_id = $student_id;
            $assign_student->year_id    = $request->year_id;
            $assign_student->class_id   = $request->class_id;
            $assign_student->group_id   = $request->group_id;
            $assign_student->save();

            // Update pivot to new sections/class/year
            if ($request->section_ids) {
                // Remove old active enrollments for this student (they are being promoted)
                DB::table('student_section')
                    ->where('student_id', $student_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                foreach ($request->section_ids as $section_id) {
                    DB::table('student_section')->insert([
                        'student_id'      => $user->id,
                        'section_id'      => $section_id,
                        'class_id'        => $request->class_id,
                        'year_id'         => $request->year_id,
                        'is_active'       => true,
                        'enrollment_date' => now()->toDateString(),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }

            $discount_student = new DiscountStudent();
            $discount_student->assign_student_id = $assign_student->id;
            $discount_student->fee_category_id   = '1';
            $discount_student->discount           = $request->discount ?? 0;
            $discount_student->save();
    	});

    	$notification = [
    	    'message'    => 'Student Promotion Updated Successfully',
    	    'alert-type' => 'success',
    	];
    	return redirect()->route('student.registration.view')->with($notification);
    }

    public function StudentRegDetails($student_id){
        $data['details']  = AssignStudent::with(['student.sections', 'student.parents', 'discount', 'student_class', 'student_year', 'group'])
            ->where('student_id', $student_id)->first();
        $data['setting']  = \App\Models\SiteSetting::first();

        $pdf = PDF::loadView('backend.student.student_reg.student_details_pdf', $data);
        return $pdf->stream('document.pdf');
    }

    public function StudentExport(Request $request) {
        if (Auth::user()->hasRole('Parent')) abort(403);
        $students = AssignStudent::with(['student'])
            ->where('year_id', $request->year_id)
            ->where('class_id', $request->class_id)
            ->get();

        $filename = "students_export_" . date('Y-m-d') . ".csv";
        $headers  = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $callback = function() use($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID No', 'Name', 'First Name', 'Surname', 'Middle Name', 'Email', 'Mobile', 'Gender', 'Address']);
            foreach ($students as $row) {
                $s = $row->student;
                fputcsv($file, [$s->id_no, $s->name, $s->first_name, $s->surname, $s->middle_name, $s->email, $s->mobile, $s->gender, $s->address]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function StudentImport(Request $request) {
        if (Auth::user()->hasRole('Parent')) abort(403);
        $file = $request->file('import_file');
        if (!$file) {
            return redirect()->back()->with(['message' => 'Please upload a CSV file', 'alert-type' => 'error']);
        }

        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // Skip header

        DB::transaction(function() use($handle, $request) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) < 2) continue;
                $code = rand(1000, 9999);
                $user = new User();
                $user->id_no      = $data[0];
                $user->name       = $data[1];
                $user->first_name = $data[2] ?? null;
                $user->surname    = $data[3] ?? null;
                $user->middle_name= $data[4] ?? null;
                $user->email      = $data[5] ?? null;
                $user->mobile     = $data[6] ?? null;
                $user->gender     = $data[7] ?? null;
                $user->address    = $data[8] ?? null;
                $user->password   = bcrypt($code);
                $user->usertype   = 'student';
                $user->code       = $code;
                $user->status     = 1;
                $user->section_id = $request->section_id;
                $user->save();

                $assign = new AssignStudent();
                $assign->student_id = $user->id;
                $assign->year_id    = $request->year_id;
                $assign->class_id   = $request->class_id;
                $assign->group_id   = $request->group_id;
                $assign->save();

                // Enroll in pivot
                if ($request->section_id) {
                    DB::table('student_section')->insert([
                        'student_id'      => $user->id,
                        'section_id'      => $request->section_id,
                        'class_id'        => $request->class_id,
                        'year_id'         => $request->year_id,
                        'is_active'       => true,
                        'enrollment_date' => now()->toDateString(),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }

                $discount = new DiscountStudent();
                $discount->assign_student_id = $assign->id;
                $discount->fee_category_id   = '1';
                $discount->discount          = 0;
                $discount->save();
            }
        });

        fclose($handle);
        return redirect()->back()->with(['message' => 'Students Imported Successfully', 'alert-type' => 'success']);
    }

    public function GroupPromotionView(){
        if (Auth::user()->hasRole('Parent')) abort(403);
    	$data['years']   = StudentYear::all();
    	$data['classes'] = StudentClass::all();
    	return view('backend.student.student_reg.student_group_promotion', $data);
    }

    public function GroupPromotionGetStudents(Request $request){
    	$allData = AssignStudent::with(['student'])->where('year_id', $request->year_id)->where('class_id', $request->class_id)->get();
    	return response()->json($allData);
    }

    public function GroupPromotionStore(Request $request){
        $student_ids = $request->student_ids;
        if (!$student_ids) {
            return redirect()->back()->with(['message' => 'Please select at least one student', 'alert-type' => 'error']);
        }

        // Check if target is same as source
        if ($request->year_id == $request->target_year_id && $request->class_id == $request->target_class_id) {
            return redirect()->back()->with(['message' => 'Target Class and Session must be different from the Source.', 'alert-type' => 'error']);
        }

    	DB::transaction(function() use($request, $student_ids){
            foreach ($student_ids as $student_id) {
                $existing = AssignStudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();

                $assign_student = new AssignStudent();
                $assign_student->student_id = $student_id;
                $assign_student->year_id    = $request->target_year_id;
                $assign_student->class_id   = $request->target_class_id;
                $assign_student->group_id   = optional($existing)->group_id;
                $assign_student->save();

                $discount = new DiscountStudent();
                $discount->assign_student_id = $assign_student->id;
                $discount->fee_category_id   = '1';
                $discount->discount          = 0;
                $discount->save();
            }
    	});

    	$notification = ['message' => 'Students Promoted Successfully', 'alert-type' => 'success'];
    	return redirect()->route('student.registration.view')->with($notification);
    }

    public function StudentRegDelete($assign_id){
        $assign_student = AssignStudent::find($assign_id);
        if ($assign_student) {
            DB::transaction(function() use($assign_student){
                // Delete related discount
                DiscountStudent::where('assign_student_id', $assign_student->id)->delete();
                
                // Delete Section enrollment for this specific registration context
                DB::table('student_section')
                    ->where('student_id', $assign_student->student_id)
                    ->where('class_id', $assign_student->class_id)
                    ->where('year_id', $assign_student->year_id)
                    ->delete();
                
                // Delete the assignment itself
                $assign_student->delete();
            });

            $notification = [
                'message'    => 'Student Registration Removed Successfully',
                'alert-type' => 'success',
            ];
            return redirect()->back()->with($notification);
        }
        return redirect()->back();
    }
}
