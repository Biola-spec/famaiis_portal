<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\StudentClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function index(): View
    {
        return view('backend.payment.fees_index', [
            'fees' => Fee::query()->with('studentClass')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('backend.payment.fees_create', [
            'classes' => StudentClass::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:student_classes,id'],
            'term' => ['required', 'string', 'max:100'],
            'session' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        Fee::query()->create($data);

        return redirect()->route('fees.index')->with([
            'message' => 'Fee created successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function edit(Fee $fee): View
    {
        return view('backend.payment.fees_edit', [
            'fee' => $fee,
            'classes' => StudentClass::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Fee $fee): RedirectResponse
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:student_classes,id'],
            'term' => ['required', 'string', 'max:100'],
            'session' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        $fee->update($data);

        return redirect()->route('fees.index')->with([
            'message' => 'Fee updated successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function destroy(Fee $fee): RedirectResponse
    {
        $fee->delete();

        return redirect()->route('fees.index')->with([
            'message' => 'Fee removed successfully.',
            'alert-type' => 'success',
        ]);
    }
}
