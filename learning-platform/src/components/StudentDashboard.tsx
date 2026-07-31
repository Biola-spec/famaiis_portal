import { useState, useEffect } from 'react';
import { useAuth } from '../lib/auth';
import { supabase, Subject, Week, Lesson, StudentProgress } from '../lib/supabase';
import { BookOpen, ChevronRight, CheckCircle2, Clock, ArrowRight, GraduationCap } from 'lucide-react';
import { LessonViewer } from './LessonViewer';
import { CbtQuiz } from './CbtQuiz';
import { AiChat } from './AiChat';

type View = 'dashboard' | 'lesson' | 'quiz' | 'chat';

export function StudentDashboard() {
  const { profile, signOut } = useAuth();
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [selectedSubject, setSelectedSubject] = useState<Subject | null>(null);
  const [weeks, setWeeks] = useState<(Week & { lessons: Lesson[] })[]>([]);
  const [progress, setProgress] = useState<StudentProgress[]>([]);
  const [cbtAttempts, setCbtAttempts] = useState<{ lesson_id: string; passed: boolean }[]>([]);
  const [view, setView] = useState<View>('dashboard');
  const [selectedLesson, setSelectedLesson] = useState<Lesson | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchSubjects();
  }, []);

  async function fetchSubjects() {
    const { data } = await supabase.from('subjects').select('*').order('name');
    setSubjects(data ?? []);
    setLoading(false);
  }

  async function selectSubject(subject: Subject) {
    setSelectedSubject(subject);
    const { data: weeksData } = await supabase
      .from('weeks')
      .select('*')
      .eq('subject_id', subject.id)
      .order('week_number');

    const weeksWithLessons: (Week & { lessons: Lesson[] })[] = [];
    for (const w of weeksData ?? []) {
      const { data: lessons } = await supabase
        .from('lessons')
        .select('*')
        .eq('week_id', w.id);
      weeksWithLessons.push({ ...w, lessons: lessons ?? [] });
    }
    setWeeks(weeksWithLessons);

    // Fetch progress
    const lessonIds = weeksWithLessons.flatMap(w => w.lessons.map(l => l.id));
    if (lessonIds.length > 0) {
      const { data: prog } = await supabase
        .from('student_progress')
        .select('*')
        .in('lesson_id', lessonIds);
      setProgress(prog ?? []);

      const { data: attempts } = await supabase
        .from('cbt_attempts')
        .select('lesson_id, passed')
        .in('lesson_id', lessonIds);
      setCbtAttempts(attempts ?? []);
    }
  }

  async function markLessonRead(lessonId: string) {
    const exists = progress.some(p => p.lesson_id === lessonId);
    if (!exists) {
      await supabase.from('student_progress').insert({ lesson_id: lessonId });
      setProgress(prev => [...prev, { id: '', student_id: '', lesson_id: lessonId, read_at: new Date().toISOString() }]);
    }
  }

  const readLessonIds = new Set(progress.map(p => p.lesson_id));
  const passedLessonIds = new Set(cbtAttempts.filter(a => a.passed).map(a => a.lesson_id));
  const totalLessons = weeks.reduce((s, w) => s + w.lessons.length, 0);
  const completedLessons = readLessonIds.size;

  if (view === 'lesson' && selectedLesson) {
    return (
      <LessonViewer
        lesson={selectedLesson}
        onBack={() => { setView('dashboard'); markLessonRead(selectedLesson.id); }}
        onOpenChat={() => setView('chat')}
        onOpenQuiz={() => setView('quiz')}
        hasQuiz={cbtAttempts.some(a => a.lesson_id === selectedLesson.id) || false}
        quizPassed={passedLessonIds.has(selectedLesson.id)}
      />
    );
  }

  if (view === 'quiz' && selectedLesson) {
    return (
      <CbtQuiz
        lesson={selectedLesson}
        onBack={() => setView('lesson')}
        onComplete={() => { setView('dashboard'); selectSubject(selectedSubject!); }}
      />
    );
  }

  if (view === 'chat' && selectedLesson) {
    return (
      <AiChat
        lesson={selectedLesson}
        onBack={() => setView('lesson')}
      />
    );
  }

  return (
    <div className="min-h-screen bg-slate-950">
      {/* Header */}
      <header className="bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-40">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-lg flex items-center justify-center">
              <BookOpen className="w-5 h-5 text-white" />
            </div>
            <span className="text-white font-bold text-lg tracking-tight">LearnHub</span>
          </div>
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2">
              <GraduationCap className="w-4 h-4 text-blue-400" />
              <span className="text-white/70 text-sm">{profile?.full_name}</span>
            </div>
            <button
              onClick={signOut}
              className="text-white/40 hover:text-white/70 text-sm transition-colors"
            >
              Sign out
            </button>
          </div>
        </div>
      </header>

      <main className="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {!selectedSubject ? (
          <>
            <div className="mb-8">
              <h1 className="text-2xl font-bold text-white mb-2">My Subjects</h1>
              <p className="text-white/40">Choose a subject to start learning</p>
            </div>

            {loading ? (
              <div className="flex items-center justify-center py-20">
                <div className="w-8 h-8 border-2 border-blue-500/30 border-t-blue-500 rounded-full animate-spin" />
              </div>
            ) : subjects.length === 0 ? (
              <div className="text-center py-20">
                <BookOpen className="w-12 h-12 text-white/20 mx-auto mb-4" />
                <p className="text-white/40">No subjects available yet. Check back soon!</p>
              </div>
            ) : (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {subjects.map(subject => (
                  <button
                    key={subject.id}
                    onClick={() => selectSubject(subject)}
                    className="group bg-white/[0.04] border border-white/[0.06] rounded-2xl p-6 text-left hover:bg-white/[0.08] hover:border-blue-500/30 transition-all"
                  >
                    <div className="w-12 h-12 bg-gradient-to-br from-blue-500/20 to-cyan-400/20 rounded-xl flex items-center justify-center mb-4 group-hover:from-blue-500/30 group-hover:to-cyan-400/30 transition-all">
                      <BookOpen className="w-6 h-6 text-blue-400" />
                    </div>
                    <h3 className="text-white font-semibold text-lg mb-1">{subject.name}</h3>
                    <p className="text-white/40 text-sm mb-3">{subject.description || `${subject.total_weeks} weeks of learning`}</p>
                    <div className="flex items-center gap-1 text-blue-400 text-sm font-medium group-hover:gap-2 transition-all">
                      Start learning <ArrowRight className="w-4 h-4" />
                    </div>
                  </button>
                ))}
              </div>
            )}
          </>
        ) : (
          <>
            {/* Back button + subject header */}
            <button
              onClick={() => { setSelectedSubject(null); setWeeks([]); }}
              className="flex items-center gap-2 text-white/40 hover:text-white/70 mb-6 transition-colors text-sm"
            >
              <ChevronRight className="w-4 h-4 rotate-180" /> Back to subjects
            </button>

            <div className="flex items-center justify-between mb-8">
              <div>
                <h1 className="text-2xl font-bold text-white mb-1">{selectedSubject.name}</h1>
                <p className="text-white/40 text-sm">{totalLessons} lessons across {weeks.length} weeks</p>
              </div>
              <div className="flex items-center gap-4">
                <div className="text-right">
                  <p className="text-white/40 text-xs mb-0.5">Progress</p>
                  <p className="text-white font-semibold">{completedLessons}/{totalLessons}</p>
                </div>
                <div className="w-12 h-12 relative">
                  <svg className="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.5" fill="none" stroke="rgba(255,255,255,0.06)" strokeWidth="3" />
                    <circle cx="18" cy="18" r="15.5" fill="none" stroke="url(#progress-gradient)" strokeWidth="3"
                      strokeDasharray={`${totalLessons > 0 ? (completedLessons / totalLessons) * 97.4 : 0} 97.4`}
                      strokeLinecap="round" />
                    <defs>
                      <linearGradient id="progress-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stopColor="#3b82f6" />
                        <stop offset="100%" stopColor="#22d3ee" />
                      </linearGradient>
                    </defs>
                  </svg>
                </div>
              </div>
            </div>

            {/* Weeks */}
            <div className="space-y-6">
              {weeks.map(week => (
                <div key={week.id} className="bg-white/[0.03] border border-white/[0.06] rounded-2xl overflow-hidden">
                  <div className="px-6 py-4 border-b border-white/[0.04] flex items-center gap-3">
                    <span className="text-xs font-bold text-blue-400 bg-blue-500/10 px-2.5 py-1 rounded-full">
                      Week {week.week_number}
                    </span>
                    <h3 className="text-white font-semibold">{week.title}</h3>
                  </div>

                  <div className="divide-y divide-white/[0.04]">
                    {week.lessons.map(lesson => {
                      const isRead = readLessonIds.has(lesson.id);
                      const isPassed = passedLessonIds.has(lesson.id);

                      return (
                        <button
                          key={lesson.id}
                          onClick={() => { setSelectedLesson(lesson); setView('lesson'); }}
                          className="w-full flex items-center justify-between px-6 py-4 hover:bg-white/[0.03] transition-all group"
                        >
                          <div className="flex items-center gap-4">
                            <div className={`w-8 h-8 rounded-lg flex items-center justify-center ${
                              isPassed ? 'bg-emerald-500/20' : isRead ? 'bg-blue-500/20' : 'bg-white/[0.06]'
                            }`}>
                              {isPassed ? (
                                <CheckCircle2 className="w-4 h-4 text-emerald-400" />
                              ) : isRead ? (
                                <CheckCircle2 className="w-4 h-4 text-blue-400" />
                              ) : (
                                <Clock className="w-4 h-4 text-white/30" />
                              )}
                            </div>
                            <span className={`font-medium ${isPassed ? 'text-emerald-300' : isRead ? 'text-blue-300' : 'text-white/70'}`}>
                              {lesson.title}
                            </span>
                          </div>
                          <div className="flex items-center gap-2">
                            {isPassed && (
                              <span className="text-xs text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">Passed</span>
                            )}
                            <ArrowRight className="w-4 h-4 text-white/20 group-hover:text-white/50 transition-colors" />
                          </div>
                        </button>
                      );
                    })}

                    {week.lessons.length === 0 && (
                      <div className="px-6 py-4 text-white/30 text-sm">No lesson content yet</div>
                    )}
                  </div>
                </div>
              ))}

              {weeks.length === 0 && (
                <div className="text-center py-12">
                  <Clock className="w-10 h-10 text-white/15 mx-auto mb-3" />
                  <p className="text-white/30">No weeks published yet</p>
                </div>
              )}
            </div>
          </>
        )}
      </main>
    </div>
  );
}
