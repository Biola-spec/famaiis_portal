<?php

namespace App\Http\Controllers\Backend\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSetting;
use App\Models\StudentYear;
use App\Models\Term;
use Illuminate\Http\Request;

class AcademicSessionTermController extends Controller
{
    public function edit()
    {
        $data['sessions'] = StudentYear::query()->orderByDesc('id')->get();
        $data['setting'] = AcademicSetting::query()->first();

        return view('backend.academic.settings.session_term', $data);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_session_id' => 'required|exists:student_years,id',
        ]);

        AcademicSetting::query()->updateOrCreate(['id' => 1], [
            'current_session_id' => $validated['current_session_id'],
        ]);

        // Maintain 'is_active' flag on models for backward compatibility
        StudentYear::query()->update(['is_active' => false]);
        StudentYear::query()->where('id', $validated['current_session_id'])->update(['is_active' => true]);

        return redirect()->back()->with([
            'message' => 'Active session updated successfully.',
            'alert-type' => 'success',
        ]);
    }
}
