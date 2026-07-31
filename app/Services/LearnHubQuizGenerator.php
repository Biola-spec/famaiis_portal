<?php

namespace App\Services;

class LearnHubQuizGenerator
{
    public function generate(string $content, string $title, int $count = 5): array
    {
        $openaiKey = config('services.openai.key');

        if ($openaiKey) {
            $questions = $this->generateWithOpenAi($content, $title, $count, $openaiKey);
            if (! empty($questions)) {
                return $questions;
            }
        }

        return $this->generateFallback($content, $title, $count);
    }

    private function generateWithOpenAi(string $content, string $title, int $count, string $apiKey): array
    {
        $systemPrompt = 'You are an expert curriculum designer. Generate multiple-choice CBT questions based STRICTLY on the lesson content. Return ONLY a JSON array with objects containing: question_number, question, option_a, option_b, option_c, option_d, correct_answer (A-D), explanation.';

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => "Lesson: {$title}\n\nContent:\n{$content}\n\nGenerate exactly {$count} questions."],
                    ],
                    'max_tokens' => 2000,
                    'temperature' => 0.7,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $text = $response->json('choices.0.message.content', '');
            if (preg_match('/\[[\s\S]*\]/', $text, $matches)) {
                $parsed = json_decode($matches[0], true);

                return is_array($parsed) ? $this->normalizeQuestions($parsed) : [];
            }
        } catch (\Throwable) {
            return [];
        }

        return [];
    }

    public function generateFallback(string $content, string $title, int $count): array
    {
        // Strip HTML tags and normalize whitespace
        $plainText = trim(preg_replace('/\s+/', ' ', strip_tags($content)));

        // Extract meaningful sentences
        $sentences = array_values(array_filter(
            array_map('trim', preg_split('/(?<=[.!?])\s+/', $plainText)),
            fn ($s) => strlen($s) > 25 && strlen($s) < 500
        ));

        if (empty($sentences)) {
            $sentences = array_values(array_filter(
                array_map('trim', preg_split('/[.!?]+/', $plainText)),
                fn ($s) => strlen($s) > 15
            ));
        }

        if (empty($sentences)) {
            $sentences = ["The lesson on {$title} covers important concepts."];
        }

        // Extract key terms: capitalized words and longer domain words
        $keyTerms = $this->extractKeyTerms($plainText);

        $questions = [];
        $usedSentences = [];
        $limit = min($count, max(count($sentences), 1));

        for ($i = 0; $i < $limit; $i++) {
            // Pick a sentence not yet used (cycle if needed)
            $idx = $i % count($sentences);
            if (count($usedSentences) < count($sentences)) {
                while (in_array($idx, $usedSentences) && count($usedSentences) < count($sentences)) {
                    $idx = ($idx + 1) % count($sentences);
                }
            }
            $usedSentences[] = $idx;
            $sentence = $sentences[$idx];

            $question = $this->buildQuestion($sentence, $keyTerms, $sentences, $title, $i);
            if ($question) {
                $question['question_number'] = $i + 1;
                $questions[] = $question;
            }
        }

        return $questions;
    }

    private function extractKeyTerms(string $text): array
    {
        // Capitalized multi-word terms (e.g. "Photosynthesis", "Newton's Law")
        preg_match_all('/\b[A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)*\b/', $text, $capMatches);
        $capitalized = array_unique(array_map('trim', $capMatches[0] ?? []));

        // Longer domain-specific words (6+ chars, alphabetic)
        preg_match_all('/\b[a-zA-Z]{6,}\b/', $text, $longMatches);
        $longWords = array_unique(array_map('trim', $longMatches[0] ?? []));

        // Merge and deduplicate (case-insensitive)
        $terms = [];
        $seen = [];
        foreach (array_merge($capitalized, $longWords) as $term) {
            $key = strtolower($term);
            if (! isset($seen[$key]) && strlen($term) > 4) {
                $seen[$key] = true;
                $terms[] = $term;
            }
        }

        return array_slice($terms, 0, 30); // Limit to top 30
    }

    private function buildQuestion(string $sentence, array $keyTerms, array $allSentences, string $title, int $index): ?array
    {
        // Find key terms that appear in this sentence
        $sentenceTerms = array_values(array_filter($keyTerms, fn ($t) => stripos($sentence, $t) !== false));

        // Choose question type based on index rotation
        $type = $index % 3;

        if ($type === 0 && ! empty($sentenceTerms)) {
            // Type 0: Fill-in-the-blank with a key term
            $term = $sentenceTerms[array_rand($sentenceTerms)];
            $blanked = preg_replace('/\b' . preg_quote($term, '/') . '\b/i', '_____', $sentence, 1);
            if ($blanked === $sentence) {
                $type = 1; // Fall through to next type
            } else {
                $distractors = $this->getDistractors($term, $keyTerms, 3);
                $options = $this->shuffleCorrectFirst($term, $distractors);

                return [
                    'question' => "Fill in the blank: \"{$blanked}\"",
                    'option_a' => $options[0],
                    'option_b' => $options[1],
                    'option_c' => $options[2],
                    'option_d' => $options[3],
                    'correct_answer' => $this->findCorrectLetter($term, $options),
                    'explanation' => "The correct answer is \"{$term}\". {$sentence}",
                ];
            }
        }

        if ($type === 1 || ($type === 0 && empty($sentenceTerms))) {
            // Type 1: "Which statement is correct?" — correct sentence vs altered ones
            $correct = trim($sentence, '. ');
            $wrongSentences = array_values(array_filter($allSentences, fn ($s) => $s !== $sentence && strlen($s) > 25));
            shuffle($wrongSentences);
            $wrongs = array_slice($wrongSentences, 0, 3);

            // If not enough wrong sentences, create variations
            while (count($wrongs) < 3) {
                $wrongs[] = 'This statement does not appear in the lesson.';
            }

            $wrongs = array_map(fn ($w) => trim(substr($w, 0, 150), '. '), $wrongs);
            $correctShort = trim(substr($correct, 0, 150), '. ');

            $options = $this->shuffleCorrectFirst($correctShort, $wrongs);

            return [
                'question' => "Based on the lesson \"{$title}\", which of the following statements is correct?",
                'option_a' => $options[0],
                'option_b' => $options[1],
                'option_c' => $options[2],
                'option_d' => $options[3],
                'correct_answer' => $this->findCorrectLetter($correctShort, $options),
                'explanation' => "The correct statement is: \"{$correct}.\"",
            ];
        }

        // Type 2: Key-term identification — "Which term relates to...?"
        if (! empty($sentenceTerms)) {
            $term = $sentenceTerms[0];
            $context = trim(substr($sentence, 0, 200), '. ');
            $distractors = $this->getDistractors($term, $keyTerms, 3);
            $options = $this->shuffleCorrectFirst($term, $distractors);

            return [
                'question' => "Which concept is described in: \"{$context}...\"?",
                'option_a' => $options[0],
                'option_b' => $options[1],
                'option_c' => $options[2],
                'option_d' => $options[3],
                'correct_answer' => $this->findCorrectLetter($term, $options),
                'explanation' => "\"{$term}\" is the concept described. The full statement is: {$sentence}",
            ];
        }

        // Last resort: simple fill-in-blank on any significant word
        $words = preg_split('/\s+/', $sentence);
        $candidates = array_values(array_filter($words, fn ($w) => strlen($w) > 5 && preg_match('/^[a-zA-Z]+$/', $w)));
        $answer = $candidates ? $candidates[array_rand($candidates)] : ($words[count($words) - 1] ?? 'answer');
        $blanked = preg_replace('/\b' . preg_quote($answer, '/') . '\b/', '_____', $sentence, 1);
        $distractors = $this->getDistractors($answer, $words, 3);
        $options = $this->shuffleCorrectFirst($answer, $distractors);

        return [
            'question' => "Complete: \"{$blanked}\"",
            'option_a' => $options[0],
            'option_b' => $options[1],
            'option_c' => $options[2],
            'option_d' => $options[3],
            'correct_answer' => $this->findCorrectLetter($answer, $options),
            'explanation' => "The correct answer is \"{$answer}\". {$sentence}",
        ];
    }

    private function getDistractors(string $correct, array $pool, int $count): array
    {
        $candidates = array_values(array_filter($pool, fn ($t) => strtolower($t) !== strtolower($correct)));
        shuffle($candidates);
        $selected = array_slice($candidates, 0, $count);

        // Pad with generic terms if not enough
        $generics = ['concept', 'process', 'method', 'principle', 'element', 'factor', 'system', 'structure'];
        shuffle($generics);
        while (count($selected) < $count) {
            $g = array_pop($generics) ?? 'option';
            if (! in_array($g, $selected)) {
                $selected[] = $g;
            }
        }

        return $selected;
    }

    private function shuffleCorrectFirst(string $correct, array $wrongs): array
    {
        $options = array_merge([$correct], $wrongs);
        shuffle($options);

        return $options;
    }

    private function findCorrectLetter(string $correct, array $options): string
    {
        $letters = ['A', 'B', 'C', 'D'];
        foreach ($options as $i => $opt) {
            if (strtolower($opt) === strtolower($correct)) {
                return $letters[$i] ?? 'A';
            }
        }

        return 'A';
    }

    private function normalizeQuestions(array $questions): array
    {
        $normalized = [];
        foreach ($questions as $i => $q) {
            $normalized[] = [
                'question_number' => $q['question_number'] ?? ($i + 1),
                'question' => $q['question'] ?? $q['question_text'] ?? '',
                'option_a' => $q['option_a'] ?? ($q['options']['A'] ?? ''),
                'option_b' => $q['option_b'] ?? ($q['options']['B'] ?? ''),
                'option_c' => $q['option_c'] ?? ($q['options']['C'] ?? ''),
                'option_d' => $q['option_d'] ?? ($q['options']['D'] ?? ''),
                'correct_answer' => strtoupper($q['correct_answer'] ?? 'A'),
                'explanation' => $q['explanation'] ?? '',
            ];
        }

        return $normalized;
    }
}
