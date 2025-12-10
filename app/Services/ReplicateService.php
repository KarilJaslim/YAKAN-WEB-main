<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReplicateService
{
    private $apiKey;
    private $baseUrl = 'https://api.replicate.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.replicate.api_key');
    }

    /**
     * Generate fabric pattern using Replicate API
     */
    public function generateFabricPattern(array $params)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/predictions', [
                'version' => $params['version'] ?? 'stability-ai/stable-diffusion:ac732df83cea7fff18b8472768c88ad041fa750ff7682a21affe81863cbe77e4',
                'input' => [
                    'prompt' => $this->buildPrompt($params),
                    'width' => $params['width'] ?? 512,
                    'height' => $params['height'] ?? 512,
                    'num_outputs' => 1,
                    'num_inference_steps' => 20,
                    'guidance_scale' => 7.5,
                    'negative_prompt' => 'blurry, low quality, distorted, text, watermark, signature',
                ],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Replicate API error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to generate pattern: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Replicate service error', [
                'message' => $e->getMessage(),
                'params' => $params,
            ]);
            throw $e;
        }
    }

    /**
     * Get prediction status
     */
    public function getPredictionStatus(string $predictionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->get($this->baseUrl . '/predictions/' . $predictionId);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Failed to get prediction status', [
                'prediction_id' => $predictionId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Build optimized prompt for fabric pattern generation
     */
    private function buildPrompt(array $params)
    {
        $basePrompt = 'seamless fabric pattern, textile design, repeating pattern';
        
        if (!empty($params['style'])) {
            $basePrompt .= ', ' . $params['style'] . ' style';
        }

        if (!empty($params['description'])) {
            $basePrompt .= ', ' . $params['description'];
        }

        if (!empty($params['colors'])) {
            $basePrompt .= ', ' . implode(', ', $params['colors']);
        }

        // Add quality and style modifiers
        $basePrompt .= ', high quality, detailed, clean, professional fabric design';

        return $basePrompt;
    }

    /**
     * Get available models for pattern generation
     */
    public function getAvailableModels()
    {
        return [
            'stable-diffusion' => [
                'version' => 'stability-ai/stable-diffusion:ac732df83cea7fff18b8472768c88ad041fa750ff7682a21affe81863cbe77e4',
                'name' => 'Stable Diffusion',
                'description' => 'General purpose pattern generation',
            ],
        ];
    }
}
