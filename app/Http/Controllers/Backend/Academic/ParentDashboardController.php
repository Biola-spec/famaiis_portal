<?php

namespace App\Http\Controllers\Backend\Academic;

use App\Http\Controllers\Controller;
use App\Models\AccountStudentFee;
use App\Models\FeeCategoryAmount;
use App\Models\SchoolSubject;
use App\Models\StudentMarks;
use App\Models\StudentYear;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SchoolScheduleService;

class ParentDashboardController extends Controller
{
    public function index(Request $request)
    {
        return $this->buildDashboard($request, 'dashboard');
    }

    public function results(Request $request)
    {
        return $this->buildDashboard($request, 'results');
    }

    public function fees(Request $request)
    {
        return $this->buildDashboard($request, 'fees');
    }

    public function shop(Request $request)
    {
        return $this->buildDashboard($request, 'shop');
    }

    private function buildDashboard(Request $request, string $activeTab)
    {
        $session = getCurrentSession();
        $parent = Auth::user();

        $filters = $request->validate([
            'child_id' => 'nullable|exists:users,id',
            'session_id' => 'nullable|exists:student_years,id',
            'subject_id' => 'nullable|exists:school_subjects,id',
            'term' => 'nullable|string',
        ]);

        $children = $parent->children()->orderBy('name')->get();
        $childId = $filters['child_id'] ?? $children->first()?->id;
        $selectedChild = $childId ? User::query()->find($childId) : null;

        abort_if($selectedChild && !$children->contains('id', $selectedChild->id), 403);

        $sessionId = $filters['session_id'] ?? optional($session)->id;

        $results = collect();
        $feeSummary = ['total_fees' => 0, 'paid' => 0, 'balance' => 0];
        $paymentHistory = collect();

        if ($selectedChild) {
            $results = StudentMarks::query()
                ->with(['subject', 'student_class', 'exam_type', 'year'])
                ->where('student_id', $selectedChild->id)
                ->where('year_id', $sessionId)
                ->when(!empty($filters['term']), function ($query) use ($filters) {
                    $query->where('term', $filters['term']);
                })
                ->when(!empty($filters['subject_id']), function ($query) use ($filters) {
                    $query->where('subject_id', $filters['subject_id']);
                })
                ->orderBy('subject_id')
                ->get();

            $classId = $results->first()?->class_id;
            $configuredTotal = 0;

            if ($classId && $sessionId) {
                $configuredTotal = (float) FeeCategoryAmount::query()
                    ->where('class_id', $classId)
                    ->sum('amount');
            }

            $paid = (float) AccountStudentFee::query()
                ->where('student_id', $selectedChild->id)
                ->where('year_id', $sessionId)
                ->sum('amount');

            $paymentHistory = AccountStudentFee::query()
                ->with('fee_category')
                ->where('student_id', $selectedChild->id)
                ->where('year_id', $sessionId)
                ->latest('date')
                ->get();

            $feeSummary = [
                'total_fees' => $configuredTotal,
                'paid' => $paid,
                'balance' => max(0, $configuredTotal - $paid),
            ];
        }

        return view('backend.academic.parent.dashboard', [
            'children' => $children,
            'selectedChild' => $selectedChild,
            'results' => $results,
            'currentSession' => $session,
            'feeSummary' => $feeSummary,
            'sessions' => StudentYear::query()->orderByDesc('id')->get(),
            'subjects' => SchoolSubject::query()->orderBy('name')->get(),
            'filters' => $filters,
            'paymentHistory' => $paymentHistory,
            'activeTab' => $activeTab,
            ...app(SchoolScheduleService::class)->dashboardData($parent),
        ]);
    }
}
