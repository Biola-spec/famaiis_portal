import { Lesson } from '../lib/supabase';
import { ArrowLeft, MessageCircle, Brain, CheckCircle2 } from 'lucide-react';

type Props = {
  lesson: Lesson;
  onBack: () => void;
  onOpenChat: () => void;
  onOpenQuiz: () => void;
  hasQuiz: boolean;
  quizPassed: boolean;
};

export function LessonViewer({ lesson, onBack, onOpenChat, onOpenQuiz, quizPassed }: Props) {
  return (
    <div className="min-h-screen bg-slate-950">
      <header className="bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-40">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
          <button
            onClick={onBack}
            className="flex items-center gap-2 text-white/60 hover:text-white transition-colors"
          >
            <ArrowLeft className="w-5 h-5" /> Back
          </button>
          <div className="flex items-center gap-3">
            <button
              onClick={onOpenChat}
              className="flex items-center gap-2 px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-xl text-blue-300 text-sm font-medium hover:bg-blue-500/20 transition-all"
            >
              <MessageCircle className="w-4 h-4" /> Ask Tutor
            </button>
            <button
              onClick={onOpenQuiz}
              className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all ${
                quizPassed
                  ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-300'
                  : 'bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 hover:bg-cyan-500/20'
              }`}
            >
              {quizPassed ? <CheckCircle2 className="w-4 h-4" /> : <Brain className="w-4 h-4" />}
              {quizPassed ? 'Passed' : 'Take Quiz'}
            </button>
          </div>
        </div>
      </header>

      <article className="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        <h1 className="text-3xl font-bold text-white mb-8">{lesson.title}</h1>
        <div className="prose prose-invert prose-blue max-w-none">
          {lesson.content.split('\n').map((paragraph, i) => {
            if (paragraph.startsWith('## ')) {
              return <h2 key={i} className="text-xl font-bold text-white mt-8 mb-4">{paragraph.replace('## ', '')}</h2>;
            }
            if (paragraph.startsWith('### ')) {
              return <h3 key={i} className="text-lg font-semibold text-blue-200 mt-6 mb-3">{paragraph.replace('### ', '')}</h3>;
            }
            if (paragraph.startsWith('- ') || paragraph.startsWith('* ')) {
              return (
                <li key={i} className="text-white/70 ml-4 mb-1 list-disc">
                  {paragraph.replace(/^[-*]\s/, '')}
                </li>
              );
            }
            if (paragraph.trim() === '') {
              return <div key={i} className="h-2" />;
            }
            return <p key={i} className="text-white/70 leading-relaxed mb-3">{paragraph}</p>;
          })}
        </div>
      </article>
    </div>
  );
}
