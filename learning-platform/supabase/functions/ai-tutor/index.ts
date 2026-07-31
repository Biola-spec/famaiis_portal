import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "npm:@supabase/supabase-js@2.57.4";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
  "Access-Control-Allow-Headers": "Content-Type, Authorization, X-Client-Info, Apikey",
};

const SYSTEM_PROMPT = `You are an intelligent academic tutor embedded in a school learning platform. Your role is to help students understand the lesson content for the current week ONLY.

## Rules you must strictly follow
1. ONLY answer questions that are directly related to the lesson content provided. If a student asks about any topic NOT covered in the lesson — no matter how similar or adjacent — respond with: "That's outside this week's lesson. I can only help with the current topic right now. Try asking something from today's content!"
2. NEVER reveal content from future weeks or past weeks unless it was explicitly included in the lesson.
3. Use simple, age-appropriate language suited for a secondary school student.
4. If the student seems confused, break your explanation into numbered steps.
5. Always encourage the student. End each answer with a short motivating line.
6. Never do their CBT quiz for them — if they paste a quiz question, say: "I won't answer quiz questions for you, but I can help you understand the concept behind it!"
7. Do not discuss anything unrelated to academics (e.g. social media, entertainment, personal topics).

## Your personality
Warm, patient, clear, encouraging. Like a knowledgeable tutor who wants every student to succeed.`;

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

    const { lesson_id, message, conversation_history } = await req.json();

    if (!lesson_id || !message) {
      return new Response(
        JSON.stringify({ error: "lesson_id and message are required" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    // Fetch lesson content with subject and week info
    const { data: lesson, error: lessonError } = await supabase
      .from("lessons")
      .select(`
        id, title, content,
        weeks:week_id (
          week_number, title,
          subjects:subject_id (name, id)
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

    const week = lesson.weeks as unknown as { week_number: number; title: string; subjects: { name: string; id: string } };
    const lessonContext = `
Subject: ${week.subjects.name}
Week: ${week.week_number} — ${week.title}

## Lesson content for this week
${lesson.content}`;

    const systemMessage = SYSTEM_PROMPT.replace("the current week ONLY", `Week ${week.week_number}: ${week.title} ONLY`)
      .replace("the current topic", week.title);

    // Build messages array for the AI
    const messages = [
      { role: "system", content: `${systemMessage}\n\n${lessonContext}` },
    ];

    if (conversation_history && Array.isArray(conversation_history)) {
      for (const msg of conversation_history) {
        messages.push({ role: msg.role, content: msg.content });
      }
    }

    messages.push({ role: "user", content: message });

    // Call OpenAI-compatible API (we'll use a simple approach with fetch)
    const openaiKey = Deno.env.get("OPENAI_API_KEY");
    if (!openaiKey) {
      // Fallback: generate a helpful response based on lesson content
      const reply = generateFallbackReply(message, lesson.content, week.title, week.subjects.name);
      return new Response(JSON.stringify({ reply }), {
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    const aiResponse = await fetch("https://api.openai.com/v1/chat/completions", {
      method: "POST",
      headers: {
        Authorization: `Bearer ${openaiKey}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        model: "gpt-4o-mini",
        messages,
        max_tokens: 500,
        temperature: 0.7,
      }),
    });

    if (!aiResponse.ok) {
      const reply = generateFallbackReply(message, lesson.content, week.title, week.subjects.name);
      return new Response(JSON.stringify({ reply }), {
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      });
    }

    const aiData = await aiResponse.json();
    const reply = aiData.choices?.[0]?.message?.content ?? "I'm having trouble right now. Please try again!";

    return new Response(JSON.stringify({ reply }), {
      headers: { ...corsHeaders, "Content-Type": "application/json" },
    });
  } catch (err) {
    return new Response(
      JSON.stringify({ error: err.message || "Internal server error" }),
      { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }
});

function generateFallbackReply(message: string, lessonContent: string, weekTitle: string, subjectName: string): string {
  const lowerMsg = message.toLowerCase();

  // Check if the question seems related to the lesson
  const lessonWords = lessonContent.toLowerCase().split(/\s+/);
  const msgWords = lowerMsg.split(/\s+/).filter(w => w.length > 3);
  const overlap = msgWords.filter(w => lessonWords.some(lw => lw.includes(w)));

  if (overlap.length < 2 && msgWords.length > 2) {
    return `That's outside this week's lesson. I can only help with ${weekTitle} right now. Try asking something from today's content! You're doing great — keep exploring the lesson!`;
  }

  // Simple keyword-based response
  if (lowerMsg.includes("explain") || lowerMsg.includes("what is") || lowerMsg.includes("what are")) {
    return `Great question! Let me break this down for you based on the lesson on ${weekTitle} in ${subjectName}:\n\nThe lesson covers key concepts that are important to understand. Try reading through the content again carefully, focusing on the definitions and examples provided.\n\nYou're asking the right questions — that's the path to understanding!`;
  }

  if (lowerMsg.includes("help") || lowerMsg.includes("don't understand") || lowerMsg.includes("confused")) {
    return `No worries — let's take it step by step! Here's how to approach the lesson on ${weekTitle}:\n\n1. Read through the lesson content slowly\n2. Write down any words or ideas that seem new to you\n3. Try to connect each concept to something you already know\n4. Ask me specific questions about any part that's unclear\n\nYou've got this — every expert was once a beginner!`;
  }

  return `That's a thoughtful question about ${weekTitle}! The lesson content has some great information on this topic. I'd suggest reviewing the relevant section carefully, and if you have a more specific question about a particular concept, I'd be happy to help break it down for you.\n\nKeep up the curiosity — it's your best learning tool!`;
}
