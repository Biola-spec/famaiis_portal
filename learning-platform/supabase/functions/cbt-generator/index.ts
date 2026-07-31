import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "npm:@supabase/supabase-js@2.57.4";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
  "Access-Control-Allow-Headers": "Content-Type, Authorization, X-Client-Info, Apikey",
};

const CBT_SYSTEM_PROMPT = `You are an expert curriculum designer and examiner for a school management platform.

A teacher has provided the lesson content for a week.

## Your task
Generate multiple-choice CBT (Computer-Based Test) questions based STRICTLY on the lesson content provided.

## Quality rules
- Every question must be answerable from the lesson content — no outside knowledge needed.
- Vary difficulty: 40% easy, 40% medium, 20% challenging.
- Distractors (wrong options) must be plausible, not obviously silly.
- Avoid trick questions or double negatives.
- Do not repeat the same concept twice.
- Return ONLY a JSON array, no preamble or explanation.

## Format for each question
[
  {
    "question_number": 1,
    "question": "...",
    "option_a": "...",
    "option_b": "...",
    "option_c": "...",
    "option_d": "...",
    "correct_answer": "C",
    "explanation": "Brief explanation of why this is correct, referencing the lesson."
  }
]`;

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

    // Verify teacher role
    const { data: profile } = await supabase
      .from("profiles")
      .select("role")
      .eq("user_id", user.id)
      .maybeSingle();

    if (!profile || profile.role !== "teacher") {
      return new Response(JSON.stringify({ error: "Only teachers can generate quizzes" }), {
        status: 403,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    const { lesson_id, num_questions } = await req.json();

    if (!lesson_id) {
      return new Response(
        JSON.stringify({ error: "lesson_id is required" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    const questionCount = Math.min(Math.max(num_questions || 5, 1), 20);

    // Fetch lesson with week/subject info and verify ownership
    const { data: lesson, error: lessonError } = await supabase
      .from("lessons")
      .select(`
        id, title, content,
        weeks:week_id (
          week_number, title,
          subjects:subject_id (name, id, teacher_id)
        )
      `)
      .eq("id", lesson_id)
      .maybeSingle();

    if (lessonError || !lesson) {
      return new Response(JSON.stringify({ error: "Lesson not found" }), {
        status: 404,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    const week = lesson.weeks as unknown as {
      week_number: number;
      title: string;
      subjects: { name: string; id: string; teacher_id: string };
    };

    if (week.subjects.teacher_id !== user.id) {
      return new Response(JSON.stringify({ error: "You can only generate quizzes for your own lessons" }), {
        status: 403,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    // Delete existing questions for this lesson
    await supabase.from("cbt_questions").delete().eq("lesson_id", lesson_id);

    const lessonContext = `
Subject: ${week.subjects.name}
Week ${week.week_number}: ${week.title}
Lesson: ${lesson.title}

## Lesson content
${lesson.content}

Generate exactly ${questionCount} multiple-choice questions.`;

    const openaiKey = Deno.env.get("OPENAI_API_KEY");

    let questions;

    if (openaiKey) {
      // Use AI to generate questions
      const aiResponse = await fetch("https://api.openai.com/v1/chat/completions", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${openaiKey}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          model: "gpt-4o-mini",
          messages: [
            { role: "system", content: CBT_SYSTEM_PROMPT },
            { role: "user", content: lessonContext },
          ],
          max_tokens: 2000,
          temperature: 0.7,
        }),
      });

      if (!aiResponse.ok) {
        questions = generateFallbackQuestions(lesson.content, lesson.title, questionCount);
      } else {
        const aiData = await aiResponse.json();
        const content = aiData.choices?.[0]?.message?.content ?? "";

        try {
          // Try to parse the JSON from the AI response
          const jsonMatch = content.match(/\[[\s\S]*\]/);
          if (jsonMatch) {
            questions = JSON.parse(jsonMatch[0]);
          } else {
            questions = generateFallbackQuestions(lesson.content, lesson.title, questionCount);
          }
        } catch {
          questions = generateFallbackQuestions(lesson.content, lesson.title, questionCount);
        }
      }
    } else {
      questions = generateFallbackQuestions(lesson.content, lesson.title, questionCount);
    }

    // Insert questions into database
    const insertData = questions.map((q: Record<string, unknown>, i: number) => ({
      lesson_id,
      question_number: q.question_number || i + 1,
      question: q.question || q.question_text || "",
      option_a: q.option_a || q.options?.A || "",
      option_b: q.option_b || q.options?.B || "",
      option_c: q.option_c || q.options?.C || "",
      option_d: q.option_d || q.options?.D || "",
      correct_answer: (q.correct_answer || "A").toUpperCase(),
      explanation: q.explanation || "",
    }));

    const { data: insertedQuestions, error: insertError } = await supabase
      .from("cbt_questions")
      .insert(insertData)
      .select();

    if (insertError) {
      return new Response(JSON.stringify({ error: "Failed to save questions" }), {
        status: 500,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    return new Response(JSON.stringify({ questions: insertedQuestions, count: insertedQuestions.length }), {
      headers: { ...corsHeaders, "Content-Type": "application/json" },
    });
  } catch (err) {
    return new Response(
      JSON.stringify({ error: err.message || "Internal server error" }),
      { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }
});

function generateFallbackQuestions(content: string, title: string, count: number): Record<string, unknown>[] {
  // Simple extraction-based question generation
  const sentences = content.split(/[.!?]+/).map(s => s.trim()).filter(s => s.length > 20);
  const questions: Record<string, unknown>[] = [];

  for (let i = 0; i < Math.min(count, sentences.length); i++) {
    const sentence = sentences[i % sentences.length];
    const words = sentence.split(/\s+/);

    // Pick a key word to be the answer (prefer longer words)
    const candidates = words.filter(w => w.length > 5 && /^[a-zA-Z]+$/.test(w));
    const answerWord = candidates.length > 0
      ? candidates[Math.floor(Math.random() * candidates.length)]
      : words[Math.floor(Math.random() * words.length)];

    const blankSentence = sentence.replace(answerWord, "_____");

    questions.push({
      question_number: i + 1,
      question: `Complete the following: ${blankSentence}`,
      option_a: answerWord,
      option_b: getDistractor(answerWord, words, 0),
      option_c: getDistractor(answerWord, words, 1),
      option_d: getDistractor(answerWord, words, 2),
      correct_answer: "A",
      explanation: `The correct answer is "${answerWord}" as stated in the lesson.`,
    });
  }

  return questions;
}

function getDistractor(answer: string, words: string[], offset: number): string {
  const alternatives = words.filter(w => w.length > 4 && w !== answer && /^[a-zA-Z]+$/.test(w));
  if (alternatives.length > offset) {
    return alternatives[offset];
  }
  const suffixes = ["tion", "ment", "ness", "ity", "ance"];
  return answer + suffixes[offset % suffixes.length];
}
