<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateSingleReportComment;
use App\Models\SchoolSetting;
use App\Models\StudentAssessment;
use App\Services\AI\AIClient;
use App\Services\AI\PromptBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\User;

class AIController extends Controller
{
    public function tools()
    {
        abort_unless(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'), 403);
        $students = User::query()
            ->where(function ($query) {
                $query->whereIn('usertype', ['Student', 'student'])
                    ->orWhereIn('role', ['Student', 'student']);
            })
            ->orderBy('name')
            ->get(['id', 'id_no', 'name']);

        return view('backend.ai.tools', compact('students'));
    }

    public function settings()
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);
        $school = SchoolSetting::first() ?: $this->schoolFromSiteSetting();
        $geminiConfigured = filled(config('services.gemini.key'));
        return view('backend.ai.settings', compact('school', 'geminiConfigured'));
    }

    public function updateSettings(Request $request)
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'motto' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'report_tone' => ['required', 'string', 'max:255'],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $directory = public_path('upload/school');
            File::ensureDirectoryExists($directory);
            $filename = now()->format('YmdHis') . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $request->file('logo')->getClientOriginalName());
            $request->file('logo')->move($directory, $filename);
            $data['logo_path'] = 'upload/school/' . $filename;
        }
        unset($data['logo']);

        SchoolSetting::updateOrCreate(['id' => 1], $data);
        return back()->with(['message' => 'School AI branding settings saved.', 'alert-type' => 'success']);
    }

    public function generateComment(StudentAssessment $assessment)
    {
        abort_unless(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'), 403);
        GenerateSingleReportComment::dispatch($assessment->id);
        if (request()->expectsJson()) {
            $assessment->refresh();
            return response()->json([
                'message' => 'Report comment generation queued.',
                'status' => $assessment->ai_status,
                'comment' => $assessment->ai_comment_draft,
                'flag' => $assessment->ai_flag,
            ]);
        }
        return back()->with(['message' => 'Report comment generation queued.', 'alert-type' => 'success']);
    }

    public function generateBulkComments(Request $request)
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);
        $data = $request->validate(['assessment_ids' => ['required', 'array'], 'assessment_ids.*' => ['integer', 'exists:student_assessments,id']]);
        foreach ($data['assessment_ids'] as $id) {
            GenerateSingleReportComment::dispatch($id);
        }
        return response()->json(['message' => count($data['assessment_ids']) . ' comment(s) queued.']);
    }

    public function lessonPlan(Request $request, AIClient $ai)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:120'], 'class_level' => ['required', 'string', 'max:120'],
            'week_number' => ['nullable', 'string', 'max:30'], 'scheme_topic' => ['required', 'string', 'max:500'],
            'scheme_objectives' => ['required', 'string', 'max:2000'], 'duration_minutes' => ['required', 'integer', 'min:1', 'max:300'],
            'resources' => ['nullable', 'string', 'max:1000'],
        ]);
        $school = SchoolSetting::first() ?: $this->schoolFromSiteSetting();
        $prompt = view('ai.prompts.lesson_plan', $data)->render();
        return response()->json($ai->completeJson(PromptBuilder::coreSystemPrompt($school), $prompt));
    }

    public function insight(Request $request, AIClient $ai)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'], 'class_name' => ['required', 'string', 'max:120'],
            'term_results' => ['required'], 'attendance_data' => ['nullable', 'string', 'max:2000'], 'conduct_log' => ['nullable', 'string', 'max:4000'],
        ]);
        $student = \App\Models\User::findOrFail($data['student_id']);
        $school = SchoolSetting::first() ?: $this->schoolFromSiteSetting();
        $prompt = view('ai.prompts.student_insight', [
            'student' => $student, 'className' => $data['class_name'],
            'termResultsJson' => is_string($data['term_results']) ? $data['term_results'] : json_encode($data['term_results']),
            'attendanceData' => $data['attendance_data'] ?? 'not available', 'conductLog' => $data['conduct_log'] ?? 'none supplied',
        ])->render();
        return response()->json(['text' => $ai->complete(PromptBuilder::coreSystemPrompt($school), $prompt)]);
    }

    public function expandComment(Request $request, AIClient $ai)
    {
        $data = $request->validate([
            'raw_text' => ['required', 'string', 'max:4000'], 'subject' => ['nullable', 'string', 'max:120'],
            'tone' => ['nullable', 'string', 'max:255'], 'sentence_count' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);
        $school = SchoolSetting::first() ?: $this->schoolFromSiteSetting();
        $prompt = view('ai.prompts.expand_comment', [
            'rawText' => $data['raw_text'], 'subject' => $data['subject'] ?? 'general',
            'tone' => $data['tone'] ?? $school->report_tone, 'sentenceCount' => $data['sentence_count'] ?? 3,
        ])->render();
        return response()->json(['text' => $ai->complete(PromptBuilder::coreSystemPrompt($school), $prompt)]);
    }

    protected function schoolFromSiteSetting(): SchoolSetting
    {
        $site = \App\Models\SiteSetting::first();
        return new SchoolSetting([
            'school_name' => optional($site)->school_name ?: 'the school',
            'address' => optional($site)->school_address,
            'report_tone' => 'encouraging but honest',
            'primary_color' => '#1a56db',
        ]);
    }
}
