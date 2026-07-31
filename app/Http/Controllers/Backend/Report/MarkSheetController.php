<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StudentMarks;

use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Services\ReportCardService;


class MarkSheetController extends Controller
{
    public function MarkSheetView(){

    	$data['years'] = StudentYear::orderBy('id','desc')->get();
    	$data['classes'] = StudentClass::all();
        $data['sections'] = \App\Models\SchoolSection::all();
    	$data['terms'] = ['1st Term', '2nd Term', '3rd Term'];
    	return view('backend.report.marksheet.marksheet_view',$data);

    }


    public function MarkSheetGet(Request $request, ReportCardService $reportCardService){

    	$year_id = $request->year_id;
    	$class_id = $request->class_id;
        $section_id = $request->section_id;
    	$term = $request->term;
    	$id_no = $request->id_no;

        if (auth()->check()) {
            // Parent Logic: Restriction
            if (auth()->user()->hasRole('Parent')) {
                $childIds = auth()->user()->children->pluck('id_no')->toArray();
                if (!in_array($id_no, $childIds)) {
                    $notification = array(
                        'message' => 'Sorry, you can only view your children\'s results.',
                        'alert-type' => 'error'
                    );
                    return redirect()->back()->with($notification);
                }
            }

            // Student Logic: Restriction
            if (auth()->user()->role === 'Student' || auth()->user()->hasRole('Student')) {
                if (auth()->user()->id_no !== $id_no) {
                    $notification = array(
                        'message' => 'Sorry, you can only view your own results.',
                        'alert-type' => 'error'
                    );
                    return redirect()->back()->with($notification);
                }
            }
        }

        return $reportCardService->render(
            (int) $year_id,
            (int) $class_id,
            $section_id ? (int) $section_id : null,
            (string) $term,
            (string) $id_no
        );
    }





}
 