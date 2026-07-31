/*
# Add teacher cross-reference policy for profiles

1. Changes
- Add policy allowing teachers to view profiles of students who have progress in their subjects
*/

DROP POLICY IF EXISTS "teachers_view_student_profiles" ON profiles;
CREATE POLICY "teachers_view_student_profiles" ON profiles FOR SELECT
  TO authenticated USING (
    EXISTS (
      SELECT 1 FROM student_progress sp
      JOIN lessons l ON l.id = sp.lesson_id
      JOIN weeks w ON w.id = l.week_id
      JOIN subjects s ON s.id = w.subject_id
      WHERE s.teacher_id = auth.uid() AND sp.student_id = profiles.user_id
    )
    OR auth.uid() = user_id
  );
