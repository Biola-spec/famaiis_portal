<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SchoolSection;
use App\Models\User;

class SchoolSectionController extends Controller
{
    public function SectionView()
    {
        $data['allData'] = SchoolSection::with('headTeacher')->get();
        return view('backend.setup.section.view_section', $data);
    }

    public function SectionAdd()
    {
        $data['teachers'] = User::whereNotIn('usertype', ['Student', 'Parent'])
            ->where(function($q){
                $q->whereNotIn('role', ['Student', 'Parent'])->orWhereNull('role');
            })->get();
        return view('backend.setup.section.add_section', $data);
    }

    public function SectionStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:school_sections,name',
        ]);

        $data = new SchoolSection();
        $data->name = $request->name;
        $data->code = $request->code;
        $data->description = $request->description;
        $data->head_teacher_id = $request->head_teacher_id;
        $data->head_title = $request->head_title;
        $data->save();

        $notification = array(
            'message' => 'School Section Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('school.section.view')->with($notification);
    }

    public function SectionEdit($id)
    {
        $data['editData'] = SchoolSection::find($id);
        $data['teachers'] = User::whereNotIn('usertype', ['Student', 'Parent'])
            ->where(function($q){
                $q->whereNotIn('role', ['Student', 'Parent'])->orWhereNull('role');
            })->get();
        return view('backend.setup.section.edit_section', $data);
    }

    public function SectionUpdate(Request $request, $id)
    {
        $data = SchoolSection::find($id);
        $data->name = $request->name;
        $data->code = $request->code;
        $data->description = $request->description;
        $data->head_teacher_id = $request->head_teacher_id;
        $data->head_title = $request->head_title;
        $data->save();

        $notification = array(
            'message' => 'School Section Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('school.section.view')->with($notification);
    }

    public function SectionDelete($id)
    {
        $section = SchoolSection::find($id);
        $section->delete();

        $notification = array(
            'message' => 'School Section Deleted Successfully',
            'alert-type' => 'info'
        );

        return redirect()->route('school.section.view')->with($notification);
    }
}
