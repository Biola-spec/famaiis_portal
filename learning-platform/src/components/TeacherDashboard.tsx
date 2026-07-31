import { useState, useEffect } from 'react';
import { useAuth } from '../lib/auth';
import { supabase, Subject, Week, Lesson } from '../lib/supabase';
import {
  BookOpen, Plus, ChevronRight, ArrowLeft, Users,
  BarChart3, Trash2, Edit3, Save, X, Loader2, GraduationCap,
  CheckCircle2, AlertTriangle, Lightbulb, FileText, Sparkles,
} from 'lucide-react';

type Tab = 'subjects' | 'lessons' | 'progress';

export function TeacherDashboard() {
  const { profile, signOut } = useAuth();
  const [tab, setTab] = useState<Tab>('subjects');
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [selectedSubject, setSelectedSubject] = useState<Subject | null>(null);
  const [weeks, setWeeks] = useState<(Week & { lessons: Lesson[] })[]>([]);
  const [loading, setLoading] = useState(true);
  const [insights, setInsights] = useState<Record<string, unknown> | null>(null);
  const [insightsLoading, setInsightsLoading] = useState(false);

  // Subject form
  const [showSubjectForm, setShowSubjectForm] = useState(false);
  const [subjectName, setSubjectName] = useState('');
  const [subjectDesc, setSubjectDesc] = useState('');
  const [subjectWeeks, setSubjectWeeks] = useState(12);

  // Lesson form
  const [editingLesson, setEditingLesson] = useState<string | null>(null);
  const [lessonTitle, setLessonTitle] = useState('');
  const [lessonContent, setLessonContent] = useState('');
  const [showWeekForm, setShowWeekForm] = useState(false);
  const [weekNumber, setWeekNumber] = useState(1);
  const [weekTitle, setWeekTitle] = useState('');
  const [generatingQuiz, setGeneratingQuiz] = useState<string | null>(null);

  useEffect(() => {
    fetchSubjects();
  }, []);

  async function fetchSubjects() {
    const { data } = await supabase.from('subjects').select('*').eq('teacher_id', profile?.user_id).order('name');
    setSubjects(data ?? []);
    setLoading(false);
  }

  async function createSubject(e: React.FormEvent) {
    e.preventDefault();
    const { data } = await supabase.from('subjects').insert({
      name: subjectName,
      description: subjectDesc || null,
      total_weeks: subjectWeeks,
    }).select().maybeSingle();
    if (data) {
      setSubjects(prev => [...prev, data]);
      setShowSubjectForm(false);
      setSubjectName('');
      setSubjectDesc('');
      setSubjectWeeks(12);
    }
  }

  async function deleteSubject(id: string) {
    await supabase.from('subjects').delete().eq('id', id);
    setSubjects(prev => prev.filter(s => s.id !== id));
    if (selectedSubject?.id === id) {
      setSelectedSubject(null);
      setWeeks([]);
    }
  }

  async function selectSubject(subject: Subject) {
    setSelectedSubject(subject);
    setTab('lessons');
    await fetchWeeks(subject.id);
  }

  async function fetchWeeks(subjectId: string) {
    const { data: weeksData } = await supabase
      .from('weeks')
      .select('*')
      .eq('subject_id', subjectId)
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
  }

  async function createWeek(e: React.FormEvent) {
    e.preventDefault();
    if (!selectedSubject) return;
    const { data } = await supabase.from('weeks').insert({
      subject_id: selectedSubject.id,
      week_number: weekNumber,
      title: weekTitle,
    }).select().maybeSingle();
    if (data) {
      setWeeks(prev => [...prev, { ...data, lessons: [] }].sort((a, b) => a.week_number - b.week_number));
      setShowWeekForm(false);
      setWeekNumber(prev => prev + 1);
      setWeekTitle('');
    }
  }

  async function saveLesson(weekId: string, lessonId: string | null) {
    if (lessonId) {
      await supabase.from('lessons').update({
        title: lessonTitle,
        content: lessonContent,
        updated_at: new Date().toISOString(),
      }).eq('id', lessonId);
    } else {
      const { data } = await supabase.from('lessons').insert({
        week_id: weekId,
        title: lessonTitle,
        content: lessonContent,
      }).select().maybeSingle();
      if (data) {
        setWeeks(prev => prev.map(w =>
          w.id === weekId ? { ...w, lessons: [...w.lessons, data] } : w
        ));
      }
    }
    setEditingLesson(null);
    setLessonTitle('');
    setLessonContent('');
    await fetchWeeks(selectedSubject!.id);
  }

  function startEditLesson(weekId: string, lesson?: Lesson) {
    if (lesson) {
      setEditingLesson(weekId);
      setLessonTitle(lesson.title);
      setLessonContent(lesson.content);
    } else {
      setEditingLesson(weekId);
      setLessonTitle('');
      setLessonContent('');
    }
  }

  async function generateQuiz(lessonId: string) {
    setGeneratingQuiz(lessonId);
    try {
      const { data: { session } } = await supabase.auth.getSession();
      const response = await fetch(`${import.meta.env.VITE_SUPABASE_URL}/functions/v1/cbt-generator`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${session?.access_token ?? ''}`,
        },
        body: JSON.stringify({ lesson_id: lessonId, num_questions: 5 }),
      });

      if (!response.ok) {
        const errData = await response.json().catch(() => ({}));
        throw new Error(errData.error || 'Failed to generate quiz');
      }

      await fetchWeeks(selectedSubject!.id);
    } catch (err: unknown) {
      const message = err instanceof Error ? err.message : 'Failed to generate quiz';
      alert(message);
    } finally {
      setGeneratingQuiz(null);
    }
  }

  async function fetchInsights() {
    if (!selectedSubject) return;
    setInsightsLoading(true);
    try {
      const { data: { session } } = await supabase.auth.getSession();
      const response = await fetch(`${import.meta.env.VITE_SUPABASE_URL}/functions/v1/progress-insights`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${session?.access_token ?? ''}`,
        },
        body: JSON.stringify({ subject_id: selectedSubject.id }),
      });

      if (!response.ok) throw new Error('Failed to fetch insights');
      const data = await response.json();
      setInsights(data);
    } catch {
      setInsights(null);
    } finally {
      setInsightsLoading(false);
    }
  }

  return (
    <div className="min-h-screen bg-slate-950">
      {/* Header */}
      <header className="bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-40">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-400 rounded-lg flex items-center justify-center">
              <BookOpen className="w-5 h-5 text-white" />
            </div>
            <span className="text-white font-bold text-lg tracking-tight">LearnHub</span>
            <span className="text-white/30 text-sm ml-2">Teacher</span>
          </div>
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2">
              <GraduationCap className="w-4 h-4 text-emerald-400" />
              <span className="text-white/70 text-sm">{profile?.full_name}</span>
            </div>
            <button onClick={signOut} className="text-white/40 hover:text-white/70 text-sm transition-colors">
              Sign out
            </button>
          </div>
        </div>
      </header>

      <main className="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {/* Tabs */}
        {selectedSubject && (
          <div className="flex items-center gap-2 mb-6">
            <button
              onClick={() => { setSelectedSubject(null); setWeeks([]); setInsights(null); }}
              className="flex items-center gap-1 text-white/40 hover:text-white/70 text-sm transition-colors mr-2"
            >
              <ArrowLeft className="w-4 h-4" /> Subjects
            </button>
            <span className="text-white/20">/</span>
            <span className="text-white/60 text-sm ml-2">{selectedSubject.name}</span>
          </div>
        )}

        {!selectedSubject ? (
          <>
            <div className="flex items-center justify-between mb-6">
              <h1 className="text-2xl font-bold text-white">My Subjects</h1>
              <button
                onClick={() => setShowSubjectForm(true)}
                className="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl text-sm font-medium hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/20"
              >
                <Plus className="w-4 h-4" /> New Subject
              </button>
            </div>

            {loading ? (
              <div className="flex items-center justify-center py-20">
                <div className="w-8 h-8 border-2 border-emerald-500/30 border-t-emerald-500 rounded-full animate-spin" />
              </div>
            ) : (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {subjects.map(subject => (
                  <div key={subject.id} className="group bg-white/[0.04] border border-white/[0.06] rounded-2xl p-6 hover:bg-white/[0.06] hover:border-emerald-500/20 transition-all">
                    <div className="flex items-start justify-between mb-3">
                      <div className="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-teal-400/20 rounded-xl flex items-center justify-center">
                        <BookOpen className="w-6 h-6 text-emerald-400" />
                      </div>
                      <button
                        onClick={(e) => { e.stopPropagation(); if (confirm('Delete this subject and all its content?')) deleteSubject(subject.id); }}
                        className="text-white/20 hover:text-red-400 transition-colors p-1"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                    <h3 className="text-white font-semibold text-lg mb-1">{subject.name}</h3>
                    <p className="text-white/40 text-sm mb-4">{subject.description || `${subject.total_weeks} weeks`}</p>
                    <button
                      onClick={() => selectSubject(subject)}
                      className="flex items-center gap-1 text-emerald-400 text-sm font-medium group-hover:gap-2 transition-all"
                    >
                      Manage <ChevronRight className="w-4 h-4" />
                    </button>
                  </div>
                ))}
                {subjects.length === 0 && (
                  <div className="col-span-full text-center py-16">
                    <BookOpen className="w-12 h-12 text-white/15 mx-auto mb-4" />
                    <p className="text-white/30 mb-4">Create your first subject to get started</p>
                    <button
                      onClick={() => setShowSubjectForm(true)}
                      className="text-emerald-400 text-sm font-medium hover:text-emerald-300"
                    >
                      + New Subject
                    </button>
                  </div>
                )}
              </div>
            )}
          </>
        ) : (
          <>
            {/* Sub-tabs */}
            <div className="flex gap-1 mb-6 bg-white/[0.03] border border-white/[0.06] rounded-xl p-1 w-fit">
              {([
                { key: 'lessons' as Tab, label: 'Lessons', icon: FileText },
                { key: 'progress' as Tab, label: 'Progress', icon: BarChart3 },
              ]).map(({ key, label, icon: Icon }) => (
                <button
                  key={key}
                  onClick={() => { setTab(key); if (key === 'progress') fetchInsights(); }}
                  className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                    tab === key
                      ? 'bg-white/10 text-white'
                      : 'text-white/40 hover:text-white/60'
                  }`}
                >
                  <Icon className="w-4 h-4" /> {label}
                </button>
              ))}
            </div>

            {tab === 'lessons' && (
              <div className="space-y-6">
                <div className="flex justify-end">
                  <button
                    onClick={() => { setShowWeekForm(true); setWeekNumber(weeks.length + 1); }}
                    className="flex items-center gap-2 px-4 py-2.5 bg-white/[0.06] border border-white/[0.08] text-white/70 rounded-xl text-sm font-medium hover:bg-white/[0.1] hover:text-white transition-all"
                  >
                    <Plus className="w-4 h-4" /> Add Week
                  </button>
                </div>

                {weeks.map(week => (
                  <div key={week.id} className="bg-white/[0.03] border border-white/[0.06] rounded-2xl overflow-hidden">
                    <div className="px-6 py-4 border-b border-white/[0.04] flex items-center gap-3">
                      <span className="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full">
                        Week {week.week_number}
                      </span>
                      <h3 className="text-white font-semibold">{week.title}</h3>
                    </div>

                    <div className="p-6">
                      {week.lessons.length > 0 && week.lessons.map(lesson => (
                        <div key={lesson.id} className="mb-4 last:mb-0">
                          {editingLesson === week.id ? (
                            <div className="space-y-4">
                              <input
                                type="text"
                                value={lessonTitle}
                                onChange={(e) => setLessonTitle(e.target.value)}
                                placeholder="Lesson title"
                                className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                              />
                              <textarea
                                value={lessonContent}
                                onChange={(e) => setLessonContent(e.target.value)}
                                placeholder="Lesson content (supports plain text with ## for headings, - for bullet points)"
                                rows={10}
                                className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 resize-y text-sm"
                              />
                              <div className="flex gap-3">
                                <button
                                  onClick={() => saveLesson(week.id, lesson.id)}
                                  className="flex items-center gap-2 px-4 py-2 bg-emerald-500/20 text-emerald-300 rounded-xl text-sm font-medium hover:bg-emerald-500/30 transition-all"
                                >
                                  <Save className="w-4 h-4" /> Save
                                </button>
                                <button
                                  onClick={() => { setEditingLesson(null); setLessonTitle(''); setLessonContent(''); }}
                                  className="flex items-center gap-2 px-4 py-2 bg-white/5 text-white/40 rounded-xl text-sm hover:text-white/60 transition-all"
                                >
                                  <X className="w-4 h-4" /> Cancel
                                </button>
                              </div>
                            </div>
                          ) : (
                            <div className="flex items-center justify-between p-4 bg-white/[0.02] border border-white/[0.04] rounded-xl">
                              <div>
                                <h4 className="text-white/80 font-medium">{lesson.title}</h4>
                                <p className="text-white/30 text-xs mt-1">{lesson.content.slice(0, 100)}...</p>
                              </div>
                              <div className="flex items-center gap-2">
                                <button
                                  onClick={() => startEditLesson(week.id, lesson)}
                                  className="p-2 text-white/30 hover:text-blue-400 transition-colors"
                                >
                                  <Edit3 className="w-4 h-4" />
                                </button>
                                <button
                                  onClick={() => generateQuiz(lesson.id)}
                                  disabled={generatingQuiz === lesson.id}
                                  className="flex items-center gap-1.5 px-3 py-1.5 bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 rounded-lg text-xs font-medium hover:bg-cyan-500/20 transition-all disabled:opacity-50"
                                >
                                  {generatingQuiz === lesson.id ? (
                                    <Loader2 className="w-3 h-3 animate-spin" />
                                  ) : (
                                    <Sparkles className="w-3 h-3" />
                                  )}
                                  Generate Quiz
                                </button>
                              </div>
                            </div>
                          )}
                        </div>
                      ))}

                      {week.lessons.length === 0 && editingLesson !== week.id && (
                        <div className="text-center py-4">
                          <button
                            onClick={() => startEditLesson(week.id)}
                            className="flex items-center gap-2 text-white/30 hover:text-white/60 text-sm transition-colors mx-auto"
                          >
                            <Plus className="w-4 h-4" /> Add lesson content
                          </button>
                        </div>
                      )}

                      {week.lessons.length === 0 && editingLesson === week.id && (
                        <div className="space-y-4">
                          <input
                            type="text"
                            value={lessonTitle}
                            onChange={(e) => setLessonTitle(e.target.value)}
                            placeholder="Lesson title"
                            className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                          />
                          <textarea
                            value={lessonContent}
                            onChange={(e) => setLessonContent(e.target.value)}
                            placeholder="Lesson content..."
                            rows={10}
                            className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 resize-y text-sm"
                          />
                          <div className="flex gap-3">
                            <button
                              onClick={() => saveLesson(week.id, null)}
                              className="flex items-center gap-2 px-4 py-2 bg-emerald-500/20 text-emerald-300 rounded-xl text-sm font-medium hover:bg-emerald-500/30 transition-all"
                            >
                              <Save className="w-4 h-4" /> Save
                            </button>
                            <button
                              onClick={() => { setEditingLesson(null); setLessonTitle(''); setLessonContent(''); }}
                              className="flex items-center gap-2 px-4 py-2 bg-white/5 text-white/40 rounded-xl text-sm hover:text-white/60 transition-all"
                            >
                              <X className="w-4 h-4" /> Cancel
                            </button>
                          </div>
                        </div>
                      )}
                    </div>
                  </div>
                ))}

                {weeks.length === 0 && !showWeekForm && (
                  <div className="text-center py-16">
                    <FileText className="w-12 h-12 text-white/15 mx-auto mb-4" />
                    <p className="text-white/30 mb-4">No weeks yet. Add your first week to start creating lessons.</p>
                    <button
                      onClick={() => { setShowWeekForm(true); setWeekNumber(1); }}
                      className="text-emerald-400 text-sm font-medium"
                    >
                      + Add Week 1
                    </button>
                  </div>
                )}
              </div>
            )}

            {tab === 'progress' && (
              <div>
                {insightsLoading ? (
                  <div className="flex items-center justify-center py-20">
                    <div className="w-8 h-8 border-2 border-emerald-500/30 border-t-emerald-500 rounded-full animate-spin" />
                  </div>
                ) : insights ? (
                  <div className="space-y-6">
                    {/* Class Summary */}
                    <div className="bg-white/[0.04] border border-white/[0.06] rounded-2xl p-6">
                      <h3 className="text-white font-semibold mb-4 flex items-center gap-2">
                        <BarChart3 className="w-5 h-5 text-emerald-400" /> Class Summary
                      </h3>
                      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        {([
                          { label: 'Completion', value: `${(insights.class_summary as Record<string, number>).completion_rate}%`, color: 'text-blue-400' },
                          { label: 'Avg Lessons', value: `${(insights.class_summary as Record<string, number>).average_lessons_read}`, color: 'text-cyan-400' },
                          { label: 'All CBTs Done', value: `${(insights.class_summary as Record<string, number>).all_cbt_attempted_pct}%`, color: 'text-emerald-400' },
                          { label: 'Pass Rate', value: `${(insights.class_summary as Record<string, number>).average_cbt_pass_rate}%`, color: 'text-teal-400' },
                        ]).map(stat => (
                          <div key={stat.label} className="text-center">
                            <p className={`text-2xl font-bold ${stat.color}`}>{stat.value}</p>
                            <p className="text-white/40 text-xs mt-1">{stat.label}</p>
                          </div>
                        ))}
                      </div>
                    </div>

                    {/* On Track */}
                    {(insights.students_on_track as Array<Record<string, unknown>>)?.length > 0 && (
                      <div className="bg-emerald-500/5 border border-emerald-500/15 rounded-2xl p-6">
                        <h3 className="text-emerald-300 font-semibold mb-4 flex items-center gap-2">
                          <CheckCircle2 className="w-5 h-5" /> Students On Track ({(insights.students_on_track as Array<unknown>).length})
                        </h3>
                        <div className="space-y-2">
                          {(insights.students_on_track as Array<Record<string, unknown>>).map((s, i) => (
                            <div key={i} className="flex items-center justify-between py-2 border-b border-emerald-500/10 last:border-0">
                              <span className="text-white/70 text-sm">{s.name as string}</span>
                              <div className="flex gap-3 text-xs">
                                <span className="text-emerald-400">{s.lessons_read as number} lessons</span>
                                <span className="text-teal-400">{s.cbt_passed as number}/{s.cbt_attempted as number} quizzes</span>
                              </div>
                            </div>
                          ))}
                        </div>
                      </div>
                    )}

                    {/* Needing Attention */}
                    {(insights.students_needing_attention as Array<Record<string, unknown>>)?.length > 0 && (
                      <div className="bg-amber-500/5 border border-amber-500/15 rounded-2xl p-6">
                        <h3 className="text-amber-300 font-semibold mb-4 flex items-center gap-2">
                          <AlertTriangle className="w-5 h-5" /> Needing Attention ({(insights.students_needing_attention as Array<unknown>).length})
                        </h3>
                        <div className="space-y-2">
                          {(insights.students_needing_attention as Array<Record<string, unknown>>).map((s, i) => (
                            <div key={i} className="flex items-center justify-between py-2 border-b border-amber-500/10 last:border-0">
                              <div>
                                <span className="text-white/70 text-sm">{s.name as string}</span>
                                <p className="text-amber-400/70 text-xs mt-0.5">{s.missing as string}</p>
                              </div>
                              <div className="flex gap-3 text-xs">
                                <span className="text-white/40">{s.lessons_read as number} lessons</span>
                                <span className="text-white/40">{s.cbt_attempted as number} quizzes</span>
                              </div>
                            </div>
                          ))}
                        </div>
                      </div>
                    )}

                    {/* Insights */}
                    <div className="bg-blue-500/5 border border-blue-500/15 rounded-2xl p-6">
                      <h3 className="text-blue-300 font-semibold mb-4 flex items-center gap-2">
                        <Lightbulb className="w-5 h-5" /> Insights & Suggestions
                      </h3>
                      <ul className="space-y-2">
                        {(insights.insights_and_suggestions as string[]).map((s, i) => (
                          <li key={i} className="text-white/60 text-sm flex items-start gap-2">
                            <span className="text-blue-400 mt-1">-</span> {s}
                          </li>
                        ))}
                      </ul>
                    </div>

                    <div className="text-center">
                      <button
                        onClick={fetchInsights}
                        className="text-white/30 hover:text-white/60 text-sm transition-colors"
                      >
                        Refresh insights
                      </button>
                    </div>
                  </div>
                ) : (
                  <div className="text-center py-16">
                    <Users className="w-12 h-12 text-white/15 mx-auto mb-4" />
                    <p className="text-white/30 mb-4">No student data yet. Insights will appear once students start using the platform.</p>
                    <button
                      onClick={fetchInsights}
                      className="text-emerald-400 text-sm font-medium"
                    >
                      Load Insights
                    </button>
                  </div>
                )}
              </div>
            )}
          </>
        )}
      </main>

      {/* Subject Form Modal */}
      {showSubjectForm && (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl">
            <h2 className="text-lg font-semibold text-white mb-4">New Subject</h2>
            <form onSubmit={createSubject} className="space-y-4">
              <div>
                <label className="block text-sm text-white/50 mb-1.5">Subject Name</label>
                <input
                  type="text"
                  value={subjectName}
                  onChange={(e) => setSubjectName(e.target.value)}
                  className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                  placeholder="e.g. Mathematics"
                  required
                />
              </div>
              <div>
                <label className="block text-sm text-white/50 mb-1.5">Description</label>
                <input
                  type="text"
                  value={subjectDesc}
                  onChange={(e) => setSubjectDesc(e.target.value)}
                  className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                  placeholder="Brief description"
                />
              </div>
              <div>
                <label className="block text-sm text-white/50 mb-1.5">Total Weeks</label>
                <input
                  type="number"
                  value={subjectWeeks}
                  onChange={(e) => setSubjectWeeks(parseInt(e.target.value) || 12)}
                  className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                  min={1}
                  max={52}
                />
              </div>
              <div className="flex gap-3 pt-2">
                <button
                  type="submit"
                  className="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl font-medium hover:from-emerald-600 hover:to-teal-600 transition-all"
                >
                  Create Subject
                </button>
                <button
                  type="button"
                  onClick={() => setShowSubjectForm(false)}
                  className="px-4 py-3 bg-white/5 text-white/40 rounded-xl hover:text-white/60 transition-all"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Week Form Modal */}
      {showWeekForm && (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl">
            <h2 className="text-lg font-semibold text-white mb-4">Add Week</h2>
            <form onSubmit={createWeek} className="space-y-4">
              <div>
                <label className="block text-sm text-white/50 mb-1.5">Week Number</label>
                <input
                  type="number"
                  value={weekNumber}
                  onChange={(e) => setWeekNumber(parseInt(e.target.value) || 1)}
                  className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                  min={1}
                  required
                />
              </div>
              <div>
                <label className="block text-sm text-white/50 mb-1.5">Week Title</label>
                <input
                  type="text"
                  value={weekTitle}
                  onChange={(e) => setWeekTitle(e.target.value)}
                  className="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                  placeholder="e.g. Introduction to Algebra"
                  required
                />
              </div>
              <div className="flex gap-3 pt-2">
                <button
                  type="submit"
                  className="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl font-medium hover:from-emerald-600 hover:to-teal-600 transition-all"
                >
                  Add Week
                </button>
                <button
                  type="button"
                  onClick={() => setShowWeekForm(false)}
                  className="px-4 py-3 bg-white/5 text-white/40 rounded-xl hover:text-white/60 transition-all"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
