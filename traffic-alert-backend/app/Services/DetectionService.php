<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DetectionService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('DETECTION_API_URL', 'http://localhost:8001');
    }

    /**
     * Detect traffic condition from image file
     * 
     * @param string $imagePath Full path to image file
     * @return array|null
     */
    public function detectImage($imagePath)
    {
        try {
            if (!file_exists($imagePath)) {
                Log::error("Detection image not found: {$imagePath}");
                return null;
            }

            $response = Http::attach(
                'file',
                file_get_contents($imagePath),
                basename($imagePath)
            )->post("{$this->apiUrl}/detect");

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    return [
                        'prediction' => $data['prediction'], // 'traffic' | 'flood' | 'normal'
                        'confidence' => $data['confidence'],
                        'probabilities' => $data['all_probabilities'] ?? null
                    ];
                }
            }

            Log::error("Detection API failed", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error("Detection service error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Detect multiple images
     * 
     * @param array $imagePaths Array of image file paths
     * @return array
     */
    public function detectBatch(array $imagePaths)
    {
        try {
            $request = Http::asMultipart();

            foreach ($imagePaths as $path) {
                if (file_exists($path)) {
                    $request->attach(
                        'files',
                        file_get_contents($path),
                        basename($path)
                    );
                }
            }

            $response = $request->post("{$this->apiUrl}/batch-detect");

            if ($response->successful()) {
                return $response->json()['results'] ?? [];
            }

            return [];

        } catch (\Exception $e) {
            Log::error("Batch detection error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if detection service is available
     * 
     * @return bool
     */
    public function isAvailable()
    {
        try {
            $response = Http::timeout(3)->get("{$this->apiUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get service health status
     * 
     * @return array|null
     */
    public function getHealth()
    {
        try {
            $response = Http::get("{$this->apiUrl}/health");
            
            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
