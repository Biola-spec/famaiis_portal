import { useState, useRef, useEffect } from 'react';
import { Lesson, ChatMessage } from '../lib/supabase';
import { supabase } from '../lib/supabase';
import { ArrowLeft, Send, Bot, User, Loader2 } from 'lucide-react';

type Props = {
  lesson: Lesson;
  onBack: () => void;
};

export function AiChat({ lesson, onBack }: Props) {
  const [messages, setMessages] = useState<ChatMessage[]>([
    { role: 'assistant', content: `Hi! I'm your AI tutor for this lesson. Ask me anything about "${lesson.title}" and I'll help you understand it better!` },
  ]);
  const [input, setInput] = useState('');
  const [loading, setLoading] = useState(false);
  const chatEndRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  async function sendMessage(e: React.FormEvent) {
    e.preventDefault();
    if (!input.trim() || loading) return;

    const userMsg: ChatMessage = { role: 'user', content: input.trim() };
    setMessages(prev => [...prev, userMsg]);
    setInput('');
    setLoading(true);

    try {
      const { data: { session } } = await supabase.auth.getSession();
      const response = await fetch(`${import.meta.env.VITE_SUPABASE_URL}/functions/v1/ai-tutor`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${session?.access_token ?? ''}`,
        },
        body: JSON.stringify({
          lesson_id: lesson.id,
          message: userMsg.content,
          conversation_history: messages,
        }),
      });

      if (!response.ok) throw new Error('Failed to get response');

      const data = await response.json();
      const assistantMsg: ChatMessage = { role: 'assistant', content: data.reply || "I'm having trouble right now. Try again!" };
      setMessages(prev => [...prev, assistantMsg]);
    } catch {
      const errorMsg: ChatMessage = {
        role: 'assistant',
        content: "Sorry, I couldn't process that. Please try again!",
      };
      setMessages(prev => [...prev, errorMsg]);
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="min-h-screen bg-slate-950 flex flex-col">
      {/* Header */}
      <header className="bg-slate-900/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-40">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 h-16 flex items-center gap-4">
          <button
            onClick={onBack}
            className="flex items-center gap-2 text-white/60 hover:text-white transition-colors"
          >
            <ArrowLeft className="w-5 h-5" /> Back
          </button>
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-lg flex items-center justify-center">
              <Bot className="w-4 h-4 text-white" />
            </div>
            <div>
              <p className="text-white font-medium text-sm">AI Tutor</p>
              <p className="text-white/40 text-xs">{lesson.title}</p>
            </div>
          </div>
        </div>
      </header>

      {/* Chat messages */}
      <div className="flex-1 overflow-y-auto max-w-3xl mx-auto w-full px-4 sm:px-6 py-6 space-y-4">
        {messages.map((msg, i) => (
          <div key={i} className={`flex gap-3 ${msg.role === 'user' ? 'flex-row-reverse' : ''}`}>
            <div className={`w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 ${
              msg.role === 'assistant'
                ? 'bg-gradient-to-br from-blue-500/20 to-cyan-400/20'
                : 'bg-white/10'
            }`}>
              {msg.role === 'assistant'
                ? <Bot className="w-4 h-4 text-blue-400" />
                : <User className="w-4 h-4 text-white/60" />
              }
            </div>
            <div className={`max-w-[80%] rounded-2xl px-4 py-3 ${
              msg.role === 'assistant'
                ? 'bg-white/[0.05] border border-white/[0.06] text-white/80'
                : 'bg-blue-500/20 border border-blue-500/20 text-blue-100'
            }`}>
              <p className="text-sm leading-relaxed whitespace-pre-wrap">{msg.content}</p>
            </div>
          </div>
        ))}

        {loading && (
          <div className="flex gap-3">
            <div className="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-cyan-400/20 rounded-lg flex items-center justify-center">
              <Bot className="w-4 h-4 text-blue-400" />
            </div>
            <div className="bg-white/[0.05] border border-white/[0.06] rounded-2xl px-4 py-3">
              <Loader2 className="w-5 h-5 text-blue-400 animate-spin" />
            </div>
          </div>
        )}
        <div ref={chatEndRef} />
      </div>

      {/* Input */}
      <div className="border-t border-white/[0.06] bg-slate-900/60 backdrop-blur-xl">
        <form onSubmit={sendMessage} className="max-w-3xl mx-auto px-4 sm:px-6 py-4 flex gap-3">
          <input
            type="text"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            placeholder="Ask about this lesson..."
            className="flex-1 px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-sm"
          />
          <button
            type="submit"
            disabled={loading || !input.trim()}
            className="px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Send className="w-5 h-5" />
          </button>
        </form>
      </div>
    </div>
  );
}
