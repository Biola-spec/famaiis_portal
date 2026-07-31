import { createClient } from '@supabase/supabase-js';

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL;
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY;

export const supabase = createClient(supabaseUrl, supabaseAnonKey);

export type Profile = {
  id: string;
  user_id: string;
  full_name: string;
  role: 'teacher' | 'student';
  avatar_url: string | null;
  created_at: string;
};

export type Subject = {
  id: string;
  name: string;
  description: string | null;
  teacher_id: string;
  total_weeks: number;
  created_at: string;
};

export type Week = {
  id: string;
  subject_id: string;
  week_number: number;
  title: string;
  created_at: string;
};

export type Lesson = {
  id: string;
  week_id: string;
  title: string;
  content: string;
  created_at: string;
  updated_at: string;
  weeks?: Week & { subjects?: Subject };
};

export type CbtQuestion = {
  id: string;
  lesson_id: string;
  question_number: number;
  question: string;
  option_a: string;
  option_b: string;
  option_c: string;
  option_d: string;
  correct_answer: 'A' | 'B' | 'C' | 'D';
  explanation: string;
  created_at: string;
};

export type StudentProgress = {
  id: string;
  student_id: string;
  lesson_id: string;
  read_at: string;
};

export type CbtAttempt = {
  id: string;
  student_id: string;
  lesson_id: string;
  answers: Record<string, string>;
  score: number;
  total_questions: number;
  passed: boolean;
  attempted_at: string;
};

export type ChatMessage = {
  role: 'user' | 'assistant';
  content: string;
};
