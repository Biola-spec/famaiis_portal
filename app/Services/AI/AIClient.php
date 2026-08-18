<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIClient
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
    }

    public function complete(string $systemPrompt, string $userPrompt, float $temperature = 0.4, int $maxTokens = 1024): string
    {
        return $this->extractText($this->call($systemPrompt, $userPrompt, $temperature, $maxTokens, false));
    }

    public function completeJson(string $systemPrompt, string $userPrompt, float $temperature = 0.3, int $maxTokens = 1500): array
    {
        $text = $this->extractText($this->call($systemPrompt, $userPrompt, $temperature, $maxTokens, true));
        $decoded = json_decode($text, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        Log::warning('AIClient received invalid JSON; retrying once.');
        $retry = $this->call($systemPrompt, $userPrompt . "\n\nReturn ONLY valid JSON. No markdown fences or commentary.", $temperature, $maxTokens, true);
        $decoded = json_decode($this->extractText($retry), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            throw new \RuntimeException('The writing service returned invalid JSON.');
        }

        return $decoded;
    }

    protected function call(string $systemPrompt, string $userPrompt, float $temperature, int $maxTokens, bool $jsonMode): array
    {
        if ($this->apiKey === '') {
            throw new \RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $generationConfig = [
            'temperature' => $temperature,
            'maxOutputTokens' => $maxTokens,
        ];

        if ($jsonMode) {
            $generationConfig['responseMimeType'] = 'application/json';
        }

        $response = Http::timeout(60)->retry(2, 500)->post(
            "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $userPrompt]]]],
                'generationConfig' => $generationConfig,
            ]
        );

        if ($response->failed()) {
            Log::error('AI request failed', ['status' => $response->status()]);
            throw new \RuntimeException('The writing service request failed.');
        }

        return $response->json();
    }

    protected function extractText(array $response): string
    {
        $text = data_get($response, 'candidates.0.content.parts.0.text');
        if ($text === null) {
            throw new \RuntimeException('The writing service returned no usable content.');
        }

        return trim($text);
    }
}
