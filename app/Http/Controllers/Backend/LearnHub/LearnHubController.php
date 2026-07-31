<?php

namespace App\Http\Controllers\Backend\LearnHub;

use App\Http\Controllers\Controller;
use App\Models\LearnhubCbtAttempt;
use App\Models\LearnhubCbtQuestion;
use App\Models\LearnhubLesson;
use App\Models\LearnhubLiveSession;
use App\Models\LearnhubStudentProgress;
use App\Models\LearnhubSubject;
use App\Models\LearnhubWeek;
use App\Models\User;
use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\Term;
use App\Services\LearnHubAiTutor;
use App\Services\LearnHubQuizGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearnHubController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($this->isTeacher($user)) {
            $subjects = LearnhubSubject::where('teacher_id', $user->id)
                ->with(['studentClass', 'year', 'term'])
                ->orderBy('name')->get();
            $teacherId = $user->id;
            $classIds = DB::table('assign_class_teachers')
                ->where('teacher_id', $teacherId)
                ->pluck('class_id')
                ->unique();
            $classes = StudentClass::whereIn('id', $classIds)->orderBy('name')->get();
            $years = StudentYear::orderBy('name', 'desc')->get();
            $terms = Term::orderBy('student_year_id')->orderBy('name')->get();

            return view('backend.learnhub.teacher.index', compact('subjects', 'classes', 'years', 'terms'));
        }

        if ($this->isStudent($user)) {
            // Get the student's class IDs and year IDs from active enrollments
            $enrollments = DB::table('student_section')
                ->where('student_id', $user->id)
                ->where('is_active', true)
                ->get();
            $studentClassIds = $enrollments->pluck('class_id')->unique()->filter();
            $studentYearIds = $enrollments->pluck('year_id')->unique()->filter();

            $subjects = LearnhubSubject::with(['studentClass', 'year', 'term'])
                ->where(function ($q) use ($studentClassIds, $studentYearIds) {
                    $q->where(function ($q2) use ($studentClassIds) {
                        $q2->whereNull('class_id');
                        if ($studentClassIds->isNotEmpty()) {
                            $q2->orWhereIn('class_id', $studentClassIds);
                        }
                    });
                    $q->where(function ($q2) use ($studentYearIds) {
                        $q2->whereNull('year_id');
                        if ($studentYearIds->isNotEmpty()) {
                            $q2->orWhereIn('year_id', $studentYearIds);
                        }
                    });
                })
                ->orderBy('name')->get();

            return view('backend.learnhub.student.index', compact('subjects'));
        }

        abort(403, 'FamaiisStudyHub is available for teachers and students only.');
    }

    public function storeSubject(Request $request)
    {
        $this->authorizeTeacher();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'class_id' => 'required|exists:student_classes,id',
            'year_id' => 'required|exists:student_years,id',
            'term_id' => 'required|exists:terms,id',
            'total_weeks' => 'required|integer|min:1|max:52',
        ]);

        LearnhubSubject::create([
            ...$validated,
            'teacher_id' => Auth::id(),
        ]);

        return redirect()->route('learnhub.index')->with('message', 'Subject created successfully.');
    }

    public function destroySubject($id)
    {
        $this->authorizeTeacher();
        $subject = LearnhubSubject::where('teacher_id', Auth::id())->findOrFail($id);
        $subject->delete();

        return redirect()->route('learnhub.index')->with('message', 'Subject deleted.');
    }

    public function manageSubject($id, Request $request)
    {
        $this->authorizeTeacher();
        $subject = LearnhubSubject::where('teacher_id', Auth::id())->findOrFail($id);
        $subject->load(['weeks.lessons.cbtQuestions', 'liveSessions.lesson', 'liveSessions.teacher']);

        $tab = $request->get('tab', 'lessons');
        $insights = null;

        if ($tab === 'progress') {
            $insights = $this->buildInsights($subject);
        }

        $lessonsForLive = $subject->weeks->flatMap(fn ($w) => $w->lessons);

        return view('backend.learnhub.teacher.manage', compact('subject', 'tab', 'insights', 'lessonsForLive'));
    }

    public function storeWeek(Request $request, $subjectId)
    {
        $this->authorizeTeacher();
        $subject = LearnhubSubject::where('teacher_id', Auth::id())->findOrFail($subjectId);

        $validated = $request->validate([
            'week_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
        ]);

        LearnhubWeek::create([
            'subject_id' => $subject->id,
            ...$validated,
        ]);

        return redirect()->route('learnhub.manage', $subject->id)->with('message', 'Week added.');
    }

    public function storeLesson(Request $request, $subjectId, $weekId)
    {
        $this->authorizeTeacher();
        $subject = LearnhubSubject::where('teacher_id', Auth::id())->findOrFail($subjectId);
        $week = LearnhubWeek::where('subject_id', $subject->id)->findOrFail($weekId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $lesson = LearnhubLesson::updateOrCreate(
            ['week_id' => $week->id],
            $validated
        );

        return redirect()->route('learnhub.manage', $subject->id)->with('message', 'Lesson saved.');
    }

    public function generateQuiz($subjectId, $lessonId, LearnHubQuizGenerator $generator)
    {
        $this->authorizeTeacher();
        $subject = LearnhubSubject::where('teacher_id', Auth::id())->findOrFail($subjectId);
        $lesson = LearnhubLesson::whereHas('week', fn ($q) => $q->where('subject_id', $subject->id))->findOrFail($lessonId);

        LearnhubCbtQuestion::where('lesson_id', $lesson->id)->delete();

        $questions = $generator->generate($lesson->content, $lesson->title, 5);

        foreach ($questions as $q) {
            LearnhubCbtQuestion::create([
                'lesson_id' => $lesson->id,
                'question_number' => $q['question_number'],
                'question' => $q['question'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_answer' => in_array($q['correct_answer'], ['A', 'B', 'C', 'D']) ? $q['correct_answer'] : 'A',
                'explanation' => $q['explanation'],
            ]);
        }

        return redirect()->route('learnhub.manage', $subject->id)->with('message', count($questions).' quiz questions generated.');
    }

    public function studentSubject($id)
    {
        $this->authorizeStudent();
        $subject = LearnhubSubject::with(['weeks.lessons'])->findOrFail($id);

        $lessonIds = $subject->weeks->flatMap(fn ($w) => $w->lessons->pluck('id'));
        $progress = LearnhubStudentProgress::where('student_id', Auth::id())
            ->whereIn('lesson_id', $lessonIds)
            ->pluck('lesson_id')
            ->flip();
        $attempts = LearnhubCbtAttempt::where('student_id', Auth::id())
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');

        $liveSessions = LearnhubLiveSession::where('subject_id', $subject->id)
            ->whereIn('status', ['scheduled', 'live'])
            ->with('lesson')
            ->orderByDesc('started_at')
            ->orderBy('scheduled_at')
            ->get();

        return view('backend.learnhub.student.subject', compact('subject', 'progress', 'attempts', 'liveSessions'));
    }

    public function showLesson($id)
    {
        $this->authorizeStudent();
        $lesson = LearnhubLesson::with('week.subject')->findOrFail($id);

        LearnhubStudentProgress::firstOrCreate([
            'student_id' => Auth::id(),
            'lesson_id' => $lesson->id,
        ], ['read_at' => now()]);

        $passed = LearnhubCbtAttempt::where('student_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->where('passed', true)
            ->exists();

        $hasQuiz = LearnhubCbtQuestion::where('lesson_id', $lesson->id)->exists();

        $liveSessions = LearnhubLiveSession::where('subject_id', $lesson->week->subject_id)
            ->where(function ($q) use ($lesson) {
                $q->where('lesson_id', $lesson->id)->orWhereNull('lesson_id');
            })
            ->whereIn('status', ['scheduled', 'live'])
            ->orderByDesc('started_at')
            ->get();

        $bestAttempt = LearnhubCbtAttempt::where('student_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->orderByDesc('game_points')
            ->first();

        return view('backend.learnhub.student.lesson', compact('lesson', 'passed', 'hasQuiz', 'liveSessions', 'bestAttempt'));
    }

    public function showQuiz($id)
    {
        $this->authorizeStudent();
        $lesson = LearnhubLesson::with('week.subject')->findOrFail($id);
        $questions = LearnhubCbtQuestion::where('lesson_id', $lesson->id)->orderBy('question_number')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('learnhub.lesson', $lesson->id)
                ->with('error', 'No quiz questions available for this lesson yet.');
        }

        return view('backend.learnhub.student.quiz', compact('lesson', 'questions'));
    }

    public function showQuizGame($id)
    {
        $this->authorizeStudent();
        $lesson = LearnhubLesson::with('week.subject')->findOrFail($id);
        $questions = LearnhubCbtQuestion::where('lesson_id', $lesson->id)->orderBy('question_number')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('learnhub.lesson', $lesson->id)
                ->with('error', 'No quiz game available yet. Ask your teacher to generate questions from this note.');
        }

        return view('backend.learnhub.student.quiz_game', compact('lesson', 'questions'));
    }

    public function submitQuiz(Request $request, $id)
    {
        $this->authorizeStudent();
        $lesson = LearnhubLesson::with('week.subject')->findOrFail($id);
        $questions = LearnhubCbtQuestion::where('lesson_id', $lesson->id)->orderBy('question_number')->get();

        $answers = $request->input('answers', []);
        $score = 0;

        foreach ($questions as $q) {
            $key = 'q'.$q->question_number;
            if (($answers[$key] ?? null) === $q->correct_answer) {
                $score++;
            }
        }

        $total = $questions->count();
        $passed = $total > 0 && ($score / $total) >= 0.5;

        $gamePoints = (int) $request->input('game_points', 0);
        $maxStreak = (int) $request->input('max_streak', 0);
        $timeSeconds = $request->filled('time_seconds') ? (int) $request->input('time_seconds') : null;

        $attempt = LearnhubCbtAttempt::create([
            'student_id' => Auth::id(),
            'lesson_id' => $lesson->id,
            'answers' => $answers,
            'score' => $score,
            'total_questions' => $total,
            'passed' => $passed,
            'attempted_at' => now(),
            'game_points' => $gamePoints,
            'max_streak' => $maxStreak,
            'time_seconds' => $timeSeconds,
        ]);

        $isGameMode = $request->boolean('game_mode');

        return view('backend.learnhub.student.quiz_result', compact('lesson', 'questions', 'answers', 'attempt', 'passed', 'score', 'total', 'isGameMode'));
    }

    public function storeLiveSession(Request $request, $subjectId)
    {
        $this->authorizeTeacher();
        $subject = LearnhubSubject::where('teacher_id', Auth::id())->findOrFail($subjectId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'lesson_id' => 'nullable|exists:learnhub_lessons,id',
            'scheduled_at' => 'nullable|date',
            'start_now' => 'nullable|boolean',
        ]);

        $startNow = $request->boolean('start_now');

        LearnhubLiveSession::create([
            'subject_id' => $subject->id,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'teacher_id' => Auth::id(),
            'title' => $validated['title'],
            'room_name' => LearnhubLiveSession::generateRoomName($subject->id),
            'scheduled_at' => $startNow ? null : ($validated['scheduled_at'] ?? null),
            'started_at' => $startNow ? now() : null,
            'status' => $startNow ? 'live' : 'scheduled',
        ]);

        return redirect()->route('learnhub.manage', ['id' => $subject->id, 'tab' => 'live'])
            ->with('message', $startNow ? 'Live session started. Students can join now.' : 'Live session scheduled.');
    }

    public function startLiveSession($subjectId, $sessionId)
    {
        $this->authorizeTeacher();
        $subject = LearnhubSubject::where('teacher_id', Auth::id())->findOrFail($subjectId);
        $session = LearnhubLiveSession::where('subject_id', $subject->id)->findOrFail($sessionId);

        $session->update([
            'status' => 'live',
            'started_at' => now(),
        ]);

        return redirect()->route('learnhub.live.join', $session->id);
    }

    public function endLiveSession($subjectId, $sessionId)
    {
        $this->authorizeTeacher();
        $subject = LearnhubSubject::where('teacher_id', Auth::id())->findOrFail($subjectId);
        $session = LearnhubLiveSession::where('subject_id', $subject->id)->findOrFail($sessionId);

        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return redirect()->route('learnhub.manage', ['id' => $subject->id, 'tab' => 'live'])
            ->with('message', 'Live session ended.');
    }

    public function joinLiveSession($sessionId)
    {
        $user = Auth::user();
        if (! $this->isTeacher($user) && ! $this->isStudent($user)) {
            abort(403);
        }

        $session = LearnhubLiveSession::with(['subject', 'lesson', 'teacher'])->findOrFail($sessionId);

        if ($session->status === 'ended') {
            return redirect()->back()->with('error', 'This live session has already ended.');
        }

        if ($this->isStudent($user)) {
            $this->authorizeStudentAccessToSubject($session->subject);
        } elseif ($this->isTeacher($user) && $session->teacher_id !== $user->id && ! $user->hasRole('Admin', 'Super Admin')) {
            abort(403, 'You can only join your own live sessions.');
        }

        if ($session->status === 'scheduled' && $this->isTeacher($user) && $session->teacher_id === $user->id) {
            $session->update(['status' => 'live', 'started_at' => now()]);
        }

        return view('backend.learnhub.live_room', compact('session'));
    }

    public function showChat($id)
    {
        $this->authorizeStudent();
        $lesson = LearnhubLesson::with('week.subject')->findOrFail($id);

        return view('backend.learnhub.student.chat', compact('lesson'));
    }

    public function sendChat(Request $request, $id, LearnHubAiTutor $tutor)
    {
        $this->authorizeStudent();
        $lesson = LearnhubLesson::with('week.subject')->findOrFail($id);

        $request->validate(['message' => 'required|string|max:1000']);

        $history = session("famaiis_studyhub_chat_{$lesson->id}", []);
        $reply = $tutor->reply($lesson, $request->message, $history);

        $history[] = ['role' => 'user', 'content' => $request->message];
        $history[] = ['role' => 'assistant', 'content' => $reply];
        session(["famaiis_studyhub_chat_{$lesson->id}" => array_slice($history, -20)]);

        if ($request->expectsJson()) {
            return response()->json(['reply' => $reply]);
        }

        return redirect()->route('learnhub.chat', $lesson->id);
    }

    private function buildInsights(LearnhubSubject $subject): array
    {
        $lessons = $subject->lessons()->with('week')->get();
        $lessonIds = $lessons->pluck('id');
        $totalLessons = $lessonIds->count();

        if ($totalLessons === 0) {
            return [
                'class_summary' => ['completion_rate' => 0, 'average_lessons_read' => 0, 'all_cbt_attempted_pct' => 0, 'average_cbt_pass_rate' => 0],
                'students_on_track' => [],
                'students_needing_attention' => [],
                'insights_and_suggestions' => ['No lessons published yet. Add weeks and lesson content to start tracking progress.'],
                'student_details' => [],
                'lessons' => [],
            ];
        }

        $progressRecords = LearnhubStudentProgress::whereIn('lesson_id', $lessonIds)->get();
        $attemptRecords = LearnhubCbtAttempt::whereIn('lesson_id', $lessonIds)->get();

        $studentIds = $progressRecords->pluck('student_id')
            ->merge($attemptRecords->pluck('student_id'))
            ->unique()
            ->values();

        $names = User::whereIn('id', $studentIds)->pluck('name', 'id');
        $students = [];
        $studentDetails = [];

        foreach ($studentIds as $sid) {
            $students[$sid] = [
                'name' => $names[$sid] ?? 'Unknown Student',
                'lessons_read' => $progressRecords->where('student_id', $sid)->count(),
                'cbt_attempted' => $attemptRecords->where('student_id', $sid)->count(),
                'cbt_passed' => $attemptRecords->where('student_id', $sid)->where('passed', true)->count(),
            ];

            // Build per-lesson detail for this student
            $lessonBreakdown = [];
            foreach ($lessons as $lesson) {
                $read = $progressRecords->first(fn ($p) => $p->student_id == $sid && $p->lesson_id == $lesson->id);
                $attempts = $attemptRecords->where('student_id', $sid)->where('lesson_id', $lesson->id);
                $bestAttempt = $attempts->sortByDesc('score')->first();

                $lessonBreakdown[$lesson->id] = [
                    'read' => $read ? true : false,
                    'read_at' => $read ? $read->read_at->format('M j') : null,
                    'quiz_attempts' => $attempts->count(),
                    'best_score' => $bestAttempt ? $bestAttempt->score : null,
                    'best_total' => $bestAttempt ? $bestAttempt->total_questions : null,
                    'passed' => $attempts->contains('passed', true),
                ];
            }

            $studentDetails[] = [
                'id' => $sid,
                'name' => $names[$sid] ?? 'Unknown Student',
                'lessons_read' => $students[$sid]['lessons_read'],
                'cbt_attempted' => $students[$sid]['cbt_attempted'],
                'cbt_passed' => $students[$sid]['cbt_passed'],
                'lesson_breakdown' => $lessonBreakdown,
            ];
        }

        $totalStudents = count($students);
        $avgLessonsRead = $totalStudents > 0 ? collect($students)->avg('lessons_read') : 0;
        $completionRate = ($totalLessons > 0 && $totalStudents > 0) ? ($avgLessonsRead / $totalLessons) * 100 : 0;
        $allCbtPct = $totalStudents > 0
            ? (collect($students)->filter(fn ($s) => $s['cbt_attempted'] >= $totalLessons)->count() / $totalStudents) * 100
            : 0;
        $cbtPassRate = $totalStudents > 0
            ? collect($students)->avg(fn ($s) => $s['cbt_attempted'] > 0 ? ($s['cbt_passed'] / $s['cbt_attempted']) * 100 : 0)
            : 0;

        $onTrack = collect($students)->filter(fn ($s) => $s['lessons_read'] >= $totalLessons * 0.75 && $s['cbt_attempted'] >= ceil($totalLessons * 0.75))->values();
        $needingAttention = collect($students)->filter(fn ($s) => $s['lessons_read'] < $totalLessons * 0.5 || $s['cbt_attempted'] < ceil($totalLessons * 0.5))->map(function ($s) use ($totalLessons) {
            $missing = [];
            if ($s['lessons_read'] < $totalLessons * 0.5) {
                $missing[] = 'behind on reading';
            }
            if ($s['cbt_attempted'] < ceil($totalLessons * 0.5)) {
                $missing[] = 'behind on quizzes';
            }
            $s['missing'] = implode(' and ', $missing);

            return $s;
        })->values();

        $insights = [];
        if ($completionRate < 50) {
            $insights[] = 'Overall lesson completion is below 50%. Consider sending reminders or scheduling review sessions.';
        }
        if ($cbtPassRate < 60) {
            $insights[] = 'CBT pass rates are low. Review quiz difficulty and ensure lesson content covers tested concepts.';
        }
        if ($needingAttention->count() > $totalStudents * 0.3 && $totalStudents > 0) {
            $insights[] = 'More than 30% of active students need attention. Consider one-on-one check-ins.';
        }
        if (empty($insights)) {
            $insights[] = 'Class progress looks healthy. Keep encouraging students to complete lessons and quizzes.';
        }

        return [
            'class_summary' => [
                'completion_rate' => round($completionRate),
                'average_lessons_read' => round($avgLessonsRead, 1),
                'all_cbt_attempted_pct' => round($allCbtPct),
                'average_cbt_pass_rate' => round($cbtPassRate),
            ],
            'students_on_track' => $onTrack->all(),
            'students_needing_attention' => $needingAttention->all(),
            'insights_and_suggestions' => $insights,
            'student_details' => $studentDetails,
            'lessons' => $lessons,
        ];
    }

    private function isTeacher($user): bool
    {
        return $user->hasRole('Teacher', 'Staff') || $user->hasRole('Admin', 'Super Admin');
    }

    private function isStudent($user): bool
    {
        return $user->hasRole('Student');
    }

    private function authorizeTeacher(): void
    {
        if (! $this->isTeacher(Auth::user())) {
            abort(403);
        }
    }

    private function authorizeStudent(): void
    {
        if (! $this->isStudent(Auth::user())) {
            abort(403);
        }
    }

    private function authorizeStudentAccessToSubject(LearnhubSubject $subject): void
    {
        $user = Auth::user();
        $enrollments = DB::table('student_section')
            ->where('student_id', $user->id)
            ->where('is_active', true)
            ->get();

        $studentClassIds = $enrollments->pluck('class_id')->unique()->filter();
        $studentYearIds = $enrollments->pluck('year_id')->unique()->filter();

        $allowed = ($subject->class_id === null || $studentClassIds->contains($subject->class_id))
            && ($subject->year_id === null || $studentYearIds->contains($subject->year_id));

        if (! $allowed) {
            abort(403, 'You do not have access to this subject.');
        }
    }
}
