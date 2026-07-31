<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\StudentYear;

class TermController extends Controller
{
    public function ViewTerm()
    {
        $data['allData'] = Term::with('academic_year')->orderBy('student_year_id', 'desc')->get();
        return view('backend.setup.term.view_term', $data);
    }

    public function TermActive($id)
    {
        $term = Term::findOrFail($id);
        
        // Deactivate all terms for the same year
        Term::where('student_year_id', $term->student_year_id)->update(['is_active' => false]);
        
        // Activate this one
        $term->is_active = true;
        $term->save();

        $notification = array(
            'message' => 'Term Activated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('term.view')->with($notification);
    }

    public function GetTermsByYear(Request $request)
    {
        $terms = Term::where('student_year_id', $request->year_id)->get();
        return response()->json($terms);
    }
}
