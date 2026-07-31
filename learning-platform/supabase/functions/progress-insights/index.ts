import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "npm:@supabase/supabase-js@2.57.4";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
  "Access-Control-Allow-Headers": "Content-Type, Authorization, X-Client-Info, Apikey",
};

Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, { status: 200, headers: corsHeaders });
  }

  try {
    const authHeader = req.headers.get("Authorization");
    if (!authHeader) {
      return new Response(JSON.stringify({ error: "Missing authorization" }), {
        status: 401,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    const supabase = createClient(
      Deno.env.get("SUPABASE_URL") ?? "",
      Deno.env.get("SUPABASE_ANON_KEY") ?? "",
      {
        global: {
          headers: { Authorization: authHeader },
        },
      }
    );

    const { data: { user }, error: authError } = await supabase.auth.getUser();
    if (authError || !user) {
      return new Response(JSON.stringify({ error: "Invalid token" }), {
        status: 401,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    const { data: profile } = await supabase
      .from("profiles")
      .select("role")
      .eq("user_id", user.id)
      .maybeSingle();

    if (!profile || profile.role !== "teacher") {
      return new Response(JSON.stringify({ error: "Only teachers can view progress insights" }), {
        status: 403,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    const { subject_id } = await req.json();
    if (!subject_id) {
      return new Response(
        JSON.stringify({ error: "subject_id is required" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    // Verify ownership
    const { data: subject } = await supabase
      .from("subjects")
      .select("id, name, total_weeks, teacher_id")
      .eq("id", subject_id)
      .maybeSingle();

    if (!subject || subject.teacher_id !== user.id) {
      return new Response(JSON.stringify({ error: "Subject not found or not owned by you" }), {
        status: 403,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    // Get all lessons for this subject with week info
    const { data: lessons } = await supabase
      .from("lessons")
      .select(`id, title, weeks:week_id (week_number, title)`)
      .in("week_id", (
        await supabase.from("weeks").select("id").eq("subject_id", subject_id)
      ).data?.map((w: { id: string }) => w.id) ?? []);

    const lessonIds = (lessons ?? []).map((l: { id: string }) => l.id);
    const totalLessons = lessonIds.length;

    // Get all student progress for this subject's lessons
    const { data: progressRecords } = await supabase
      .from("student_progress")
      .select("student_id, lesson_id, read_at")
      .in("lesson_id", lessonIds);

    // Get all CBT attempts for this subject's lessons
    const { data: attemptRecords } = await supabase
      .from("cbt_attempts")
      .select("student_id, lesson_id, score, total_questions, passed, attempted_at")
      .in("lesson_id", lessonIds);

    // Get student profiles
    const studentIds = [...new Set([
      ...(progressRecords ?? []).map((p: { student_id: string }) => p.student_id),
      ...(attemptRecords ?? []).map((a: { student_id: string }) => a.student_id),
    ])];

    const { data: studentProfiles } = await supabase
      .from("profiles")
      .select("user_id, full_name")
      .in("user_id", studentIds);

    const profileMap = new Map(
      (studentProfiles ?? []).map((p: { user_id: string; full_name: string }) => [p.user_id, p.full_name])
    );

    // Build per-student summaries
    const studentMap = new Map<string, {
      name: string;
      lessonsRead: number;
      cbtAttempted: number;
      cbtPassed: number;
      lastActive: string | null;
    }>();

    for (const sid of studentIds) {
      studentMap.set(sid, {
        name: profileMap.get(sid) || "Unknown Student",
        lessonsRead: 0,
        cbtAttempted: 0,
        cbtPassed: 0,
        lastActive: null,
      });
    }

    for (const p of progressRecords ?? []) {
      const s = studentMap.get(p.student_id);
      if (s) {
        s.lessonsRead++;
        if (!s.lastActive || p.read_at > s.lastActive) s.lastActive = p.read_at;
      }
    }

    for (const a of attemptRecords ?? []) {
      const s = studentMap.get(a.student_id);
      if (s) {
        s.cbtAttempted++;
        if (a.passed) s.cbtPassed++;
        if (!s.lastActive || a.attempted_at > s.lastActive) s.lastActive = a.attempted_at;
      }
    }

    const students = Array.from(studentMap.entries()).map(([id, data]) => ({
      id,
      ...data,
    }));

    // Compute class summary
    const totalStudents = students.length;
    const avgLessonsRead = totalStudents > 0
      ? students.reduce((sum, s) => sum + s.lessonsRead, 0) / totalStudents
      : 0;
    const completionRate = totalLessons > 0 && totalStudents > 0
      ? (avgLessonsRead / totalLessons) * 100
      : 0;
    const allCbtAttempted = students.filter(s => s.cbtAttempted >= totalLessons).length;
    const cbtPassRate = students.reduce((sum, s) => sum + (s.cbtAttempted > 0 ? (s.cbtPassed / s.cbtAttempted) * 100 : 0), 0) / (totalStudents || 1);

    const onTrack = students.filter(s => s.lessonsRead >= totalLessons * 0.75 && s.cbtAttempted >= Math.ceil(totalLessons * 0.75));
    const needingAttention = students.filter(s => s.lessonsRead < totalLessons * 0.5 || s.cbtAttempted < Math.ceil(totalLessons * 0.5));

    // Generate insights
    const insights: string[] = [];

    if (completionRate < 50) {
      insights.push("Overall lesson completion is below 50%. Consider sending reminders or scheduling review sessions.");
    }
    if (cbtPassRate < 60) {
      insights.push("CBT pass rates are low. Review the quiz difficulty and ensure lesson content covers tested concepts.");
    }
    if (needingAttention.length > totalStudents * 0.3) {
      insights.push("Over 30% of students need attention. Consider targeted intervention or peer study groups.");
    }
    if (insights.length === 0) {
      insights.push("Class performance looks healthy! Keep up the good teaching.");
      insights.push("Consider adding more challenging questions for advanced students.");
    }

    const report = {
      subject_name: subject.name,
      total_weeks: subject.total_weeks,
      total_lessons: totalLessons,
      total_students: totalStudents,
      class_summary: {
        completion_rate: Math.round(completionRate),
        average_lessons_read: Math.round(avgLessonsRead * 10) / 10,
        all_cbt_attempted_count: allCbtAttempted,
        all_cbt_attempted_pct: totalStudents > 0 ? Math.round((allCbtAttempted / totalStudents) * 100) : 0,
        average_cbt_pass_rate: Math.round(cbtPassRate),
      },
      students_on_track: onTrack.map(s => ({ id: s.id, name: s.name, lessons_read: s.lessonsRead, cbt_attempted: s.cbtAttempted, cbt_passed: s.cbtPassed })),
      students_needing_attention: needingAttention.map(s => ({
        id: s.id,
        name: s.name,
        lessons_read: s.lessonsRead,
        cbt_attempted: s.cbtAttempted,
        cbt_passed: s.cbtPassed,
        missing: s.lessonsRead < totalLessons * 0.5 ? `${totalLessons - s.lessonsRead} lessons unread` : `${Math.ceil(totalLessons * 0.75) - s.cbtAttempted} CBTs unattempted`,
      })),
      insights_and_suggestions: insights,
    };

    return new Response(JSON.stringify(report), {
      headers: { ...corsHeaders, "Content-Type": "application/json" },
    });
  } catch (err) {
    return new Response(
      JSON.stringify({ error: err.message || "Internal server error" }),
      { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }
});
