import { useState, useEffect } from 'react';
import { Lesson, CbtQuestion, CbtAttempt } from '../lib/supabase';
import { supabase } from '../lib/supabase';
import { ArrowLeft, Brain, CheckCircle2, XCircle, ChevronRight } from 'lucide-react';

type Props = {
  lesson: Lesson;
  onBack: () => void;
  onComplete: () => void;
};

export function CbtQuiz({ lesson, onBack, onComplete }: Props) {
  const [questions, setQuestions] = useState<CbtQuestion[]>([]);
  const [currentIdx, setCurrentIdx] = useState(0);
  const [answers, setAnswers] = useState<Record<string, string>>({});
  const [showResult, setShowResult] = useState(false);
  const [result, setResult] = useState<CbtAttempt | null>(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    fetchQuestions();
  }, [lesson.id]);

  async function fetchQuestions() {
    const { data } = await supabase
      .from('cbt_questions')
      .select('*')
      .eq('lesson_id', lesson.id)
      .order('question_number');
    setQuestions(data ?? []);
    setLoading(false);
  }

  async function submitQuiz() {
    if (submitting) return;
    setSubmitting(true);

    let score = 0;
    for (const q of questions) {
      const key = `q${q.question_number}`;
      if (answers[key] === q.correct_answer) score++;
    }

    const total = questions.length;
    const passed = total > 0 && (score / total) >= 0.5;

    const { data: attempt } = await supabase
      .from('cbt_attempts')
      .insert({
        lesson_id: lesson.id,
        answers,
        score,
        total_questions: total,
        passed,
      })
      .select()
      .maybeSingle();

    setResult(attempt ?? { id: '', student_id: '', lesson_id: lesson.id, answers, score, total_questions: total, passed, attempted_at: '' });
    setShowResult(true);
    setSubmitting(false);
  }

  if (loading) {
    return (
      <div className="min-h-screen bg-slate-950 flex items-center justify-center">
        <div className="w-8 h-8 border-2 border-blue-500/30 border-t-blue-500 rounded-full animate-spin" />
      </div>
    );
  }

  if (questions.length === 0) {
    return (
      <div className="min-h-screen bg-slate-950">
        <header className="bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-40">
          <div className="max-w-3xl mx-auto px-4 sm:px-6 h-16 flex items-center">
            <button onClick={onBack} className="flex items-center gap-2 text-white/60 hover:text-white transition-colors">
              <ArrowLeft className="w-5 h-5" /> Back
            </button>
          </div>
        </header>
        <div className="max-w-3xl mx-auto px-4 sm:px-6 py-20 text-center">
          <Brain className="w-12 h-12 text-white/15 mx-auto mb-4" />
          <h2 className="text-xl font-semibold text-white mb-2">No quiz questions yet</h2>
          <p className="text-white/40">Your teacher hasn't generated quiz questions for this lesson yet.</p>
        </div>
      </div>
    );
  }

  if (showResult && result) {
    const pct = result.total_questions > 0 ? Math.round((result.score / result.total_questions) * 100) : 0;
    return (
      <div className="min-h-screen bg-slate-950">
        <header className="bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-40">
          <div className="max-w-3xl mx-auto px-4 sm:px-6 h-16 flex items-center">
            <button onClick={onComplete} className="flex items-center gap-2 text-white/60 hover:text-white transition-colors">
              <ArrowLeft className="w-5 h-5" /> Back to lessons
            </button>
          </div>
        </header>

        <div className="max-w-3xl mx-auto px-4 sm:px-6 py-10">
          <div className={`text-center mb-10 p-8 rounded-2xl border ${
            result.passed
              ? 'bg-emerald-500/5 border-emerald-500/20'
              : 'bg-red-500/5 border-red-500/20'
          }`}>
            {result.passed ? (
              <CheckCircle2 className="w-16 h-16 text-emerald-400 mx-auto mb-4" />
            ) : (
              <XCircle className="w-16 h-16 text-red-400 mx-auto mb-4" />
            )}
            <h2 className="text-2xl font-bold text-white mb-2">
              {result.passed ? 'Great job!' : 'Keep practicing!'}
            </h2>
            <p className="text-4xl font-bold text-white mb-1">{pct}%</p>
            <p className="text-white/50">{result.score} of {result.total_questions} correct</p>
          </div>

          {/* Show each question with result */}
          <div className="space-y-4">
            {questions.map((q) => {
              const userAnswer = answers[`q${q.question_number}`];
              const isCorrect = userAnswer === q.correct_answer;

              return (
                <div key={q.id} className={`p-5 rounded-xl border ${
                  isCorrect ? 'bg-emerald-500/5 border-emerald-500/15' : 'bg-red-500/5 border-red-500/15'
                }`}>
                  <div className="flex items-start gap-3 mb-3">
                    {isCorrect
                      ? <CheckCircle2 className="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" />
                      : <XCircle className="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" />
                    }
                    <p className="text-white/80 text-sm font-medium">{q.question}</p>
                  </div>
                  <div className="ml-8 space-y-1">
                    {['A', 'B', 'C', 'D'].map(opt => {
                      const val = opt === 'A' ? q.option_a : opt === 'B' ? q.option_b : opt === 'C' ? q.option_c : q.option_d;
                      const isUserChoice = userAnswer === opt;
                      const isCorrectOpt = q.correct_answer === opt;

                      return (
                        <div key={opt} className={`text-sm px-3 py-1.5 rounded-lg ${
                          isCorrectOpt ? 'text-emerald-300 bg-emerald-500/10' :
                          isUserChoice ? 'text-red-300 bg-red-500/10 line-through' :
                          'text-white/40'
                        }`}>
                          {opt}. {val}
                        </div>
                      );
                    })}
                  </div>
                  {!isCorrect && (
                    <p className="ml-8 mt-3 text-sm text-white/50 italic">{q.explanation}</p>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </div>
    );
  }

  const currentQ = questions[currentIdx];
  const selectedAnswer = answers[`q${currentQ.question_number}`];

  return (
    <div className="min-h-screen bg-slate-950">
      <header className="bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-40">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
          <button onClick={onBack} className="flex items-center gap-2 text-white/60 hover:text-white transition-colors">
            <ArrowLeft className="w-5 h-5" /> Exit
          </button>
          <div className="flex items-center gap-2 text-white/60 text-sm">
            <Brain className="w-4 h-4 text-cyan-400" />
            Question {currentIdx + 1} of {questions.length}
          </div>
        </div>
      </header>

      <div className="max-w-3xl mx-auto px-4 sm:px-6 py-8">
        {/* Progress bar */}
        <div className="h-1.5 bg-white/[0.06] rounded-full mb-8 overflow-hidden">
          <div
            className="h-full bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full transition-all duration-300"
            style={{ width: `${((currentIdx + 1) / questions.length) * 100}%` }}
          />
        </div>

        <h2 className="text-lg font-semibold text-white mb-6">{currentQ.question}</h2>

        <div className="space-y-3">
          {(['A', 'B', 'C', 'D'] as const).map(opt => {
            const val = opt === 'A' ? currentQ.option_a : opt === 'B' ? currentQ.option_b : opt === 'C' ? currentQ.option_c : currentQ.option_d;
            const isSelected = selectedAnswer === opt;

            return (
              <button
                key={opt}
                onClick={() => setAnswers(prev => ({ ...prev, [`q${currentQ.question_number}`]: opt }))}
                className={`w-full text-left px-5 py-4 rounded-xl border transition-all ${
                  isSelected
                    ? 'bg-blue-500/15 border-blue-500/40 text-blue-200'
                    : 'bg-white/[0.03] border-white/[0.06] text-white/70 hover:bg-white/[0.06] hover:border-white/10'
                }`}
              >
                <span className="font-semibold mr-3">{opt}.</span> {val}
              </button>
            );
          })}
        </div>

        <div className="flex justify-end mt-8 gap-3">
          {currentIdx < questions.length - 1 ? (
            <button
              onClick={() => setCurrentIdx(prev => prev + 1)}
              disabled={!selectedAnswer}
              className="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:from-blue-600 hover:to-cyan-600 transition-all"
            >
              Next <ChevronRight className="w-4 h-4" />
            </button>
          ) : (
            <button
              onClick={submitQuiz}
              disabled={!selectedAnswer || submitting}
              className="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-500 text-white rounded-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:from-emerald-600 hover:to-green-600 transition-all"
            >
              {submitting ? (
                <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
              ) : (
                <>Submit Quiz</>
              )}
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
