<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ParentResultLink;
use App\Models\SchoolSubject;
use App\Models\SiteSetting;
use App\Models\StudentMarks;
use App\Models\StudentYear;
use App\Models\User;
use App\Services\ReportCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentResultLinkController extends Controller
{
    public function store(Request $request, int $parentId)
    {
        $parent = User::query()->where('id', $parentId)->where(function ($q) {
            $q->where('usertype', 'Parent')->orWhere('role', 'Parent');
        })->firstOrFail();

        $validated = $request->validate([
            'student_id' => 'nullable|exists:users,id',
            'year_id' => 'nullable|exists:student_years,id',
            'term' => 'nullable|in:1st Term,2nd Term,3rd Term',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        $childIds = $parent->children()->pluck('id')->all();
        if (empty($childIds)) {
            return back()->with([
                'message' => 'Link this parent to at least one student before creating a results link.',
                'alert-type' => 'error',
            ]);
        }

        if (!empty($validated['student_id']) && !in_array((int) $validated['student_id'], $childIds, true)) {
            return back()->with([
                'message' => 'Selected student is not linked to this parent.',
                'alert-type' => 'error',
            ]);
        }

        $expiresAt = null;
        if (!empty($validated['expires_in_days'])) {
            $expiresAt = now()->addDays((int) $validated['expires_in_days']);
        }

        ParentResultLink::query()
            ->where('parent_id', $parent->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $link = ParentResultLink::create([
            'token' => ParentResultLink::generateUniqueToken(),
            'parent_id' => $parent->id,
            'student_id' => $validated['student_id'] ?? null,
            'year_id' => $validated['year_id'] ?? optional(getCurrentSession())->id,
            'term' => $validated['term'] ?? null,
            'created_by' => Auth::id(),
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        return redirect()
            ->route('parent.edit', $parent->id)
            ->withFragment('results-link')
            ->with([
                'message' => 'Results link created.',
                'alert-type' => 'success',
                'parent_result_link' => $link->shortUrl(),
            ]);
    }

    public function destroy(int $id)
    {
        $link = ParentResultLink::findOrFail($id);
        $link->update(['is_active' => false]);

        return back()->with([
            'message' => 'Results link deactivated.',
            'alert-type' => 'success',
        ]);
    }

    public function show(Request $request, string $token)
    {
        $link = ParentResultLink::findValidByToken($token);

        if (!$link) {
            return view('public.parent_results_invalid');
        }

        $link->recordAccess();

        $validated = $request->validate([
            'student_id' => 'nullable|integer',
            'session_id' => 'nullable|exists:student_years,id',
            'term' => 'nullable|in:1st Term,2nd Term,3rd Term',
            'subject_id' => 'nullable|exists:school_subjects,id',
        ]);

        $children = $link->parent->children()->orderBy('name')->get();
        $studentId = $link->resolveStudentId($validated['student_id'] ?? null);
        $selectedChild = $studentId ? $children->firstWhere('id', $studentId) : null;

        if (!$selectedChild) {
            return view('public.parent_results_invalid', [
                'reason' => 'No student is linked to this parent account.',
            ]);
        }

        $sessionId = $validated['session_id'] ?? $link->year_id ?? optional(getCurrentSession())->id;
        $term = $validated['term'] ?? $link->term;

        $results = StudentMarks::query()
            ->with(['subject', 'student_class', 'exam_type', 'year'])
            ->where('student_id', $selectedChild->id)
            ->where('year_id', $sessionId)
            ->when($term, fn ($q) => $q->where('term', $term))
            ->when(!empty($validated['subject_id']), fn ($q) => $q->where('subject_id', $validated['subject_id']))
            ->orderBy('subject_id')
            ->get();

        $setting = SiteSetting::first();

        return view('public.parent_results', [
            'link' => $link,
            'setting' => $setting,
            'children' => $children,
            'selectedChild' => $selectedChild,
            'results' => $results,
            'sessions' => StudentYear::query()->orderByDesc('id')->get(),
            'subjects' => SchoolSubject::query()->orderBy('name')->get(),
            'filters' => [
                'session_id' => $sessionId,
                'term' => $term,
                'subject_id' => $validated['subject_id'] ?? null,
                'student_id' => $selectedChild->id,
            ],
        ]);
    }

    public function reportCard(Request $request, string $token, ReportCardService $reportCardService)
    {
        $link = ParentResultLink::findValidByToken($token);

        if (!$link) {
            return view('public.parent_results_invalid');
        }

        $validated = $request->validate([
            'student_id' => 'nullable|integer',
            'session_id' => 'nullable|exists:student_years,id',
            'term' => 'required|in:1st Term,2nd Term,3rd Term',
        ]);

        $studentId = $link->resolveStudentId($validated['student_id'] ?? null);
        $student = User::find($studentId);

        if (!$student) {
            return redirect()->route('parent.result.link', $token)->with([
                'message' => 'Student not found for this link.',
                'alert-type' => 'error',
            ]);
        }

        $yearId = (int) ($validated['session_id'] ?? $link->year_id ?? optional(getCurrentSession())->id);
        $term = $validated['term'];

        $sample = StudentMarks::query()
            ->where('student_id', $student->id)
            ->where('year_id', $yearId)
            ->where('term', $term)
            ->first();

        if (!$sample) {
            return redirect()->route('parent.result.link', $token)->with([
                'message' => 'No results found for the selected term. Choose another term or session.',
                'alert-type' => 'error',
            ]);
        }

        $link->recordAccess();

        return $reportCardService->render(
            $yearId,
            (int) $sample->class_id,
            $sample->section_id ? (int) $sample->section_id : null,
            $term,
            (string) $student->id_no
        );
    }
}
