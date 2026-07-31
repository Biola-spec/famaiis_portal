/*
# School Learning Platform - Core Schema (Part 1: Base tables)

1. New Tables
- `profiles`: User profiles with role (teacher/student), name, avatar
- `subjects`: School subjects with teacher ownership
- `weeks`: Weeks within a term, linked to subjects
- `lessons`: Lesson content for each week
- `cbt_questions`: Multiple-choice quiz questions
- `student_progress`: Tracks lesson read/completion by students
- `cbt_attempts`: Records quiz attempts with scores

2. Security
- RLS enabled on all tables
- Owner-scoped CRUD policies for all tables
- Cross-reference policies deferred to part 2

3. Notes
- All owner columns default to auth.uid()
- Foreign keys use CASCADE deletes
*/

-- Profiles table
CREATE TABLE IF NOT EXISTS profiles (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid NOT NULL DEFAULT auth.uid() REFERENCES auth.users(id) ON DELETE CASCADE UNIQUE,
  full_name text NOT NULL,
  role text NOT NULL CHECK (role IN ('teacher', 'student')),
  avatar_url text,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE profiles ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "select_own_profile" ON profiles;
CREATE POLICY "select_own_profile" ON profiles FOR SELECT
  TO authenticated USING (auth.uid() = user_id);

DROP POLICY IF EXISTS "insert_own_profile" ON profiles;
CREATE POLICY "insert_own_profile" ON profiles FOR INSERT
  TO authenticated WITH CHECK (auth.uid() = user_id);

DROP POLICY IF EXISTS "update_own_profile" ON profiles;
CREATE POLICY "update_own_profile" ON profiles FOR UPDATE
  TO authenticated USING (auth.uid() = user_id) WITH CHECK (auth.uid() = user_id);

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  description text,
  teacher_id uuid NOT NULL DEFAULT auth.uid() REFERENCES auth.users(id) ON DELETE CASCADE,
  total_weeks integer NOT NULL DEFAULT 12,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE subjects ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "select_subjects" ON subjects;
CREATE POLICY "select_subjects" ON subjects FOR SELECT
  TO authenticated USING (true);

DROP POLICY IF EXISTS "insert_own_subjects" ON subjects;
CREATE POLICY "insert_own_subjects" ON subjects FOR INSERT
  TO authenticated WITH CHECK (auth.uid() = teacher_id);

DROP POLICY IF EXISTS "update_own_subjects" ON subjects;
CREATE POLICY "update_own_subjects" ON subjects FOR UPDATE
  TO authenticated USING (auth.uid() = teacher_id) WITH CHECK (auth.uid() = teacher_id);

DROP POLICY IF EXISTS "delete_own_subjects" ON subjects;
CREATE POLICY "delete_own_subjects" ON subjects FOR DELETE
  TO authenticated USING (auth.uid() = teacher_id);

-- Weeks table
CREATE TABLE IF NOT EXISTS weeks (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  subject_id uuid NOT NULL REFERENCES subjects(id) ON DELETE CASCADE,
  week_number integer NOT NULL,
  title text NOT NULL,
  created_at timestamptz DEFAULT now(),
  UNIQUE(subject_id, week_number)
);

ALTER TABLE weeks ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "select_weeks" ON weeks;
CREATE POLICY "select_weeks" ON weeks FOR SELECT
  TO authenticated USING (true);

DROP POLICY IF EXISTS "insert_own_weeks" ON weeks;
CREATE POLICY "insert_own_weeks" ON weeks FOR INSERT
  TO authenticated WITH CHECK (
    EXISTS (SELECT 1 FROM subjects WHERE id = weeks.subject_id AND teacher_id = auth.uid())
  );

DROP POLICY IF EXISTS "update_own_weeks" ON weeks;
CREATE POLICY "update_own_weeks" ON weeks FOR UPDATE
  TO authenticated USING (
    EXISTS (SELECT 1 FROM subjects WHERE id = weeks.subject_id AND teacher_id = auth.uid())
  ) WITH CHECK (
    EXISTS (SELECT 1 FROM subjects WHERE id = weeks.subject_id AND teacher_id = auth.uid())
  );

DROP POLICY IF EXISTS "delete_own_weeks" ON weeks;
CREATE POLICY "delete_own_weeks" ON weeks FOR DELETE
  TO authenticated USING (
    EXISTS (SELECT 1 FROM subjects WHERE id = weeks.subject_id AND teacher_id = auth.uid())
  );

-- Lessons table
CREATE TABLE IF NOT EXISTS lessons (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  week_id uuid NOT NULL REFERENCES weeks(id) ON DELETE CASCADE,
  title text NOT NULL,
  content text NOT NULL,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now(),
  UNIQUE(week_id)
);

ALTER TABLE lessons ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "select_lessons" ON lessons;
CREATE POLICY "select_lessons" ON lessons FOR SELECT
  TO authenticated USING (true);

DROP POLICY IF EXISTS "insert_own_lessons" ON lessons;
CREATE POLICY "insert_own_lessons" ON lessons FOR INSERT
  TO authenticated WITH CHECK (
    EXISTS (
      SELECT 1 FROM weeks w JOIN subjects s ON s.id = w.subject_id
      WHERE w.id = lessons.week_id AND s.teacher_id = auth.uid()
    )
  );

DROP POLICY IF EXISTS "update_own_lessons" ON lessons;
CREATE POLICY "update_own_lessons" ON lessons FOR UPDATE
  TO authenticated USING (
    EXISTS (
      SELECT 1 FROM weeks w JOIN subjects s ON s.id = w.subject_id
      WHERE w.id = lessons.week_id AND s.teacher_id = auth.uid()
    )
  ) WITH CHECK (
    EXISTS (
      SELECT 1 FROM weeks w JOIN subjects s ON s.id = w.subject_id
      WHERE w.id = lessons.week_id AND s.teacher_id = auth.uid()
    )
  );

DROP POLICY IF EXISTS "delete_own_lessons" ON lessons;
CREATE POLICY "delete_own_lessons" ON lessons FOR DELETE
  TO authenticated USING (
    EXISTS (
      SELECT 1 FROM weeks w JOIN subjects s ON s.id = w.subject_id
      WHERE w.id = lessons.week_id AND s.teacher_id = auth.uid()
    )
  );

-- CBT Questions table
CREATE TABLE IF NOT EXISTS cbt_questions (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  lesson_id uuid NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
  question_number integer NOT NULL,
  question text NOT NULL,
  option_a text NOT NULL,
  option_b text NOT NULL,
  option_c text NOT NULL,
  option_d text NOT NULL,
  correct_answer text NOT NULL CHECK (correct_answer IN ('A', 'B', 'C', 'D')),
  explanation text NOT NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE cbt_questions ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "select_cbt_questions" ON cbt_questions;
CREATE POLICY "select_cbt_questions" ON cbt_questions FOR SELECT
  TO authenticated USING (true);

DROP POLICY IF EXISTS "insert_own_cbt_questions" ON cbt_questions;
CREATE POLICY "insert_own_cbt_questions" ON cbt_questions FOR INSERT
  TO authenticated WITH CHECK (
    EXISTS (
      SELECT 1 FROM lessons l JOIN weeks w ON w.id = l.week_id
      JOIN subjects s ON s.id = w.subject_id
      WHERE l.id = cbt_questions.lesson_id AND s.teacher_id = auth.uid()
    )
  );

DROP POLICY IF EXISTS "update_own_cbt_questions" ON cbt_questions;
CREATE POLICY "update_own_cbt_questions" ON cbt_questions FOR UPDATE
  TO authenticated USING (
    EXISTS (
      SELECT 1 FROM lessons l JOIN weeks w ON w.id = l.week_id
      JOIN subjects s ON s.id = w.subject_id
      WHERE l.id = cbt_questions.lesson_id AND s.teacher_id = auth.uid()
    )
  ) WITH CHECK (
    EXISTS (
      SELECT 1 FROM lessons l JOIN weeks w ON w.id = l.week_id
      JOIN subjects s ON s.id = w.subject_id
      WHERE l.id = cbt_questions.lesson_id AND s.teacher_id = auth.uid()
    )
  );

DROP POLICY IF EXISTS "delete_own_cbt_questions" ON cbt_questions;
CREATE POLICY "delete_own_cbt_questions" ON cbt_questions FOR DELETE
  TO authenticated USING (
    EXISTS (
      SELECT 1 FROM lessons l JOIN weeks w ON w.id = l.week_id
      JOIN subjects s ON s.id = w.subject_id
      WHERE l.id = cbt_questions.lesson_id AND s.teacher_id = auth.uid()
    )
  );

-- Student Progress table
CREATE TABLE IF NOT EXISTS student_progress (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  student_id uuid NOT NULL DEFAULT auth.uid() REFERENCES auth.users(id) ON DELETE CASCADE,
  lesson_id uuid NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
  read_at timestamptz DEFAULT now(),
  UNIQUE(student_id, lesson_id)
);

ALTER TABLE student_progress ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "select_own_progress" ON student_progress;
CREATE POLICY "select_own_progress" ON student_progress FOR SELECT
  TO authenticated USING (auth.uid() = student_id);

DROP POLICY IF EXISTS "teachers_read_progress" ON student_progress;
CREATE POLICY "teachers_read_progress" ON student_progress FOR SELECT
  TO authenticated USING (
    EXISTS (
      SELECT 1 FROM lessons l JOIN weeks w ON w.id = l.week_id
      JOIN subjects s ON s.id = w.subject_id
      WHERE l.id = student_progress.lesson_id AND s.teacher_id = auth.uid()
    )
  );

DROP POLICY IF EXISTS "insert_own_progress" ON student_progress;
CREATE POLICY "insert_own_progress" ON student_progress FOR INSERT
  TO authenticated WITH CHECK (auth.uid() = student_id);

DROP POLICY IF EXISTS "delete_own_progress" ON student_progress;
CREATE POLICY "delete_own_progress" ON student_progress FOR DELETE
  TO authenticated USING (auth.uid() = student_id);

-- CBT Attempts table
CREATE TABLE IF NOT EXISTS cbt_attempts (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  student_id uuid NOT NULL DEFAULT auth.uid() REFERENCES auth.users(id) ON DELETE CASCADE,
  lesson_id uuid NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
  answers jsonb NOT NULL DEFAULT '{}',
  score integer NOT NULL DEFAULT 0,
  total_questions integer NOT NULL DEFAULT 0,
  passed boolean NOT NULL DEFAULT false,
  attempted_at timestamptz DEFAULT now()
);

ALTER TABLE cbt_attempts ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "select_own_attempts" ON cbt_attempts;
CREATE POLICY "select_own_attempts" ON cbt_attempts FOR SELECT
  TO authenticated USING (auth.uid() = student_id);

DROP POLICY IF EXISTS "teachers_read_attempts" ON cbt_attempts;
CREATE POLICY "teachers_read_attempts" ON cbt_attempts FOR SELECT
  TO authenticated USING (
    EXISTS (
      SELECT 1 FROM lessons l JOIN weeks w ON w.id = l.week_id
      JOIN subjects s ON s.id = w.subject_id
      WHERE l.id = cbt_attempts.lesson_id AND s.teacher_id = auth.uid()
    )
  );

DROP POLICY IF EXISTS "insert_own_attempts" ON cbt_attempts;
CREATE POLICY "insert_own_attempts" ON cbt_attempts FOR INSERT
  TO authenticated WITH CHECK (auth.uid() = student_id);

-- Indexes
CREATE INDEX IF NOT EXISTS idx_profiles_user_id ON profiles(user_id);
CREATE INDEX IF NOT EXISTS idx_subjects_teacher_id ON subjects(teacher_id);
CREATE INDEX IF NOT EXISTS idx_weeks_subject_id ON weeks(subject_id);
CREATE INDEX IF NOT EXISTS idx_lessons_week_id ON lessons(week_id);
CREATE INDEX IF NOT EXISTS idx_cbt_questions_lesson_id ON cbt_questions(lesson_id);
CREATE INDEX IF NOT EXISTS idx_student_progress_student_id ON student_progress(student_id);
CREATE INDEX IF NOT EXISTS idx_student_progress_lesson_id ON student_progress(lesson_id);
CREATE INDEX IF NOT EXISTS idx_cbt_attempts_student_id ON cbt_attempts(student_id);
CREATE INDEX IF NOT EXISTS idx_cbt_attempts_lesson_id ON cbt_attempts(lesson_id);
