<?php

namespace App\Services;

use App\Models\LearnhubLesson;

class LearnHubAiTutor
{
    public function reply(LearnhubLesson $lesson, string $message, array $history = []): string
    {
        $lesson->load('week.subject');
        $weekTitle = $lesson->week->title ?? 'this week';
        $subjectName = $lesson->week->subject->name ?? 'the subject';
        $content = $lesson->content;

        $openaiKey = config('services.openai.key');
        if ($openaiKey) {
            $reply = $this->replyWithOpenAi($lesson, $message, $history, $openaiKey, $weekTitle, $subjectName);
            if ($reply) {
                return $reply;
            }
        }

        return $this->fallbackReply($message, $content, $weekTitle, $subjectName);
    }

    private function replyWithOpenAi(LearnhubLesson $lesson, string $message, array $history, string $apiKey, string $weekTitle, string $subjectName): ?string
    {
        $system = "You are an academic tutor for {$subjectName}, Week: {$weekTitle}. Only answer from this lesson content:\n\n{$lesson->content}";

        $messages = [['role' => 'system', 'content' => $system]];
        foreach ($history as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(45)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    private function fallbackReply(string $message, string $content, string $weekTitle, string $subjectName): string
    {
        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags($content)));
        $lowerMsg = strtolower($message);

        // Handle greetings and casual chat
        if (preg_match('/^(hi|hello|hey|good\s+(morning|afternoon|evening)|howdy|greetings)/i', $lowerMsg)) {
            return "Hello! I'm your {$subjectName} tutor for the lesson \"{$weekTitle}\". Ask me anything about this lesson and I'll help you understand it better!";
        }

        if (preg_match('/(thank|thanks|appreciate)/i', $lowerMsg)) {
            return "You're welcome! Feel free to ask more questions about {$weekTitle}. I'm here to help!";
        }

        // Split content into sentences for retrieval
        $sentences = array_values(array_filter(
            array_map('trim', preg_split('/(?<=[.!?])\s+/', $plainContent)),
            fn ($s) => strlen($s) > 20
        ));

        if (empty($sentences)) {
            $sentences = array_values(array_filter(
                array_map('trim', preg_split('/[.!?]+/', $plainContent)),
                fn ($s) => strlen($s) > 15
            ));
        }

        // Score each sentence by keyword overlap with the question
        $msgWords = array_values(array_filter(
            array_unique(preg_split('/\s+/', preg_replace('/[^a-zA-Z0-9\s]/', '', $lowerMsg))),
            fn ($w) => strlen($w) > 2
        ));

        // Remove stop words
        $stopWords = ['what', 'who', 'where', 'when', 'why', 'how', 'does', 'do', 'did', 'can', 'could', 'would', 'will', 'shall', 'the', 'this', 'that', 'these', 'those', 'about', 'from', 'with', 'have', 'has', 'been', 'being', 'are', 'was', 'were', 'there', 'their', 'they', 'which', 'explain', 'tell', 'give', 'mean', 'means'];
        $queryWords = array_values(array_filter($msgWords, fn ($w) => ! in_array($w, $stopWords)));

        if (empty($queryWords)) {
            // Very short question — return a lesson summary
            return $this->buildSummary($sentences, $weekTitle, $subjectName);
        }

        // Score sentences by relevance
        $scored = [];
        foreach ($sentences as $i => $sentence) {
            $lowerSentence = strtolower($sentence);
            $score = 0;
            foreach ($queryWords as $word) {
                if (str_contains($lowerSentence, $word)) {
                    // Boost exact word matches
                    $score += (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $sentence)) ? 3 : 1;
                }
            }
            if ($score > 0) {
                $scored[] = ['sentence' => $sentence, 'score' => $score, 'index' => $i];
            }
        }

        // Sort by score descending
        usort($scored, fn ($a, $b) => $b['score'] - $a['score']);

        if (empty($scored)) {
            // No relevant content found
            $summary = $this->extractKeyPoints($sentences, 2);

            return "I couldn't find specific information about that in the lesson \"{$weekTitle}\". Here are some key points from the lesson that might help:\n\n{$summary}\n\nTry rephrasing your question using keywords from the lesson!";
        }

        // Get the top 2-3 most relevant sentences
        $topResults = array_slice($scored, 0, 3);
        // Sort by original position for coherent flow
        usort($topResults, fn ($a, $b) => $a['index'] - $b['index']);

        $relevantContent = implode(' ', array_map(fn ($r) => $r['sentence'], $topResults));

        // Format the response based on question type
        if (preg_match('/(explain|what is|what are|define|definition|describe)/i', $lowerMsg)) {
            return "Based on the lesson \"{$weekTitle}\" in {$subjectName}:\n\n{$relevantContent}\n\nThis is directly from your lesson material. Let me know if you'd like me to find more details!";
        }

        if (preg_match('/(list|enumerate|steps|types|kinds|examples|mention)/i', $lowerMsg)) {
            $points = $this->extractKeyPoints($sentences, 4, $queryWords);

            return "From the lesson \"{$weekTitle}\":\n\n{$points}\n\nThese are the key points I found related to your question. Review the lesson for more details!";
        }

        if (preg_match('/(difference|compare|versus|vs|between)/i', $lowerMsg)) {
            return "Great comparison question! Here's what the lesson says:\n\n{$relevantContent}\n\nReview the full lesson on \"{$weekTitle}\" for a complete understanding of both concepts.";
        }

        if (preg_match('/(how|process|work|function|mechanism)/i', $lowerMsg)) {
            return "Here's what the lesson explains:\n\n{$relevantContent}\n\nThis should help you understand the process. Feel free to ask follow-up questions!";
        }

        // General answer
        return "Here's what I found in the lesson \"{$weekTitle}\":\n\n{$relevantContent}\n\nHope that helps! Ask me more if you need clarification.";
    }

    private function buildSummary(array $sentences, string $weekTitle, string $subjectName): string
    {
        $keyPoints = $this->extractKeyPoints($sentences, 3);

        return "Here's a quick overview of \"{$weekTitle}\" in {$subjectName}:\n\n{$keyPoints}\n\nAsk me about any specific topic from this lesson and I'll find the relevant details for you!";
    }

    private function extractKeyPoints(array $sentences, int $count, array $queryWords = []): string
    {
        if (empty($sentences)) {
            return '• No content available for this lesson.';
        }

        // If query words provided, prioritize relevant sentences
        $selected = [];
        if (! empty($queryWords)) {
            $scored = [];
            foreach ($sentences as $i => $s) {
                $lower = strtolower($s);
                $score = 0;
                foreach ($queryWords as $w) {
                    if (str_contains($lower, $w)) {
                        $score++;
                    }
                }
                $scored[] = ['sentence' => $s, 'score' => $score, 'index' => $i];
            }
            usort($scored, fn ($a, $b) => $b['score'] - $a['score']);
            $topPicks = array_slice($scored, 0, $count);
            usort($topPicks, fn ($a, $b) => $a['index'] - $b['index']);
            $selected = array_map(fn ($p) => $p['sentence'], $topPicks);
        }

        // If not enough selected, pick evenly spaced sentences
        if (count($selected) < $count) {
            $step = max(1, intdiv(count($sentences), $count));
            for ($i = 0; $i < count($sentences) && count($selected) < $count; $i += $step) {
                $s = $sentences[$i];
                if (! in_array($s, $selected)) {
                    $selected[] = $s;
                }
            }
        }

        // Format as bullet points
        return implode("\n", array_map(fn ($s) => '• ' . trim($s), $selected));
    }
}
