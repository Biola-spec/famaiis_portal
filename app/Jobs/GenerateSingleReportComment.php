<?php

namespace App\Jobs;

use App\Models\SchoolSetting;
use App\Models\StudentAssessment;
use App\Models\StudentMarks;
use App\Services\AI\AIClient;
use App\Services\AI\PromptBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;

class GenerateSingleReportComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $assessmentId) {}

    public function middleware(): array
    {
        return [new RateLimited('gemini')];
    }

    public function handle(AIClient $ai): void
    {
        $assessment = StudentAssessment::with(['student', 'student_class'])
            ->findOrFail($this->assessmentId);
        $school = SchoolSetting::first() ?: $this->fallbackSchool();

        $marks = StudentMarks::with('subject')
            ->where('student_id', $assessment->student_id)
            ->where('year_id', $assessment->year_id)
            ->where('class_id', $assessment->class_id)
            ->where('term', $assessment->term)
            ->get()
            ->map(fn ($mark) => [
                'subject' => optional($mark->subject)->name,
                'total_score' => $mark->total_score ?? $mark->marks,
                'grade' => $mark->grade,
            ])->values();

        $prompt = view('ai.prompts.report_comment', [
            'student' => $assessment->student,
            'className' => optional($assessment->student_class)->name,
            'marksJson' => $marks->toJson(),
            'term' => $assessment->term,
            'teacherNotes' => $assessment->teacher_comment,
            'previousComment' => $assessment->head_teacher_comment,
            'tone' => $school->report_tone,
        ])->render();

        $assessment->update(['ai_status' => 'processing', 'ai_flag' => null]);
        $comment = $ai->complete(PromptBuilder::coreSystemPrompt($school), $prompt);

        if (str_starts_with($comment, '[FLAG:')) {
            $assessment->update([
                'ai_status' => 'needs_review',
                'ai_flag' => $comment,
                'ai_comment_draft' => null,
            ]);
            return;
        }

        $assessment->update(['ai_status' => 'draft', 'ai_comment_draft' => $comment]);
    }

    protected function fallbackSchool(): SchoolSetting
    {
        $site = \App\Models\SiteSetting::first();
        return new SchoolSetting([
            'school_name' => optional($site)->school_name ?: 'the school',
            'report_tone' => 'encouraging but honest',
        ]);
    }
}
