<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class YoloVerificationService
{
    private $apiUrl;
    
    public function __construct()
    {
        $this->apiUrl = env('YOLO_API_URL', 'http://localhost:5000');
    }
    
    /**
     * Verify alert với YOLO
     * 
     * @param int $cameraId
     * @param string $expectedLabel (traffic/flood)
     * @param int $duration Thời gian quét (giây)
     * @return array|null
     */
    public function verify($cameraId, $expectedLabel, $duration = 30)
    {
        try {
            // Tìm camera
            $camera = DB::table('camera')->find($cameraId);
            
            if (!$camera) {
                Log::error("Camera not found: {$cameraId}");
                return null;
            }
            
            // Map label từ Laravel sang YOLO format
            $yoloLabel = $this->mapLabelToYolo($expectedLabel);
            
            // SMART STREAM URL HANDLING:
            // 1. Try YouTube stream first (if available)
            // 2. Fallback to local video if YouTube fails
            $streamUrl = $camera->stream_url;
            $isYouTube = strpos($streamUrl, 'youtube.com') !== false || strpos($streamUrl, 'youtu.be') !== false;
            
            // If YouTube, also prepare local fallback
            $localVideoPath = null;
            if ($isYouTube) {
                // Map camera ID to local video file
                $localVideoPath = $this->getLocalVideoPath($cameraId);
                Log::info("YouTube stream detected, local fallback prepared", [
                    'youtube_url' => $streamUrl,
                    'local_fallback' => $localVideoPath
                ]);
            }
            
            // Gọi YOLO API
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(120)->post("{$this->apiUrl}/verify", [
                'stream_url' => $streamUrl,
                'expected_label' => $yoloLabel,
                'duration' => $duration
            ]);
            
            // If YouTube failed and we have local fallback, try again
            if ($response->failed() && $localVideoPath) {
                Log::warning("YouTube stream failed, trying local video", [
                    'local_path' => $localVideoPath
                ]);
                
                $response = Http::timeout(120)->post("{$this->apiUrl}/verify", [
                    'stream_url' => $localVideoPath,
                    'expected_label' => $yoloLabel,
                    'duration' => $duration
                ]);
            }
            
            if ($response->failed()) {
                Log::error("YOLO API error: " . $response->body());
                return null;
            }
            
            $data = $response->json();
            
            if (!isset($data['success']) || !$data['success']) {
                Log::error("YOLO verification failed: " . ($data['error'] ?? 'Unknown error'));
                return null;
            }
            
            return $data['result'] ?? null;
            
        } catch (\Exception $e) {
            Log::error("YOLO verification exception: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get local video path for camera
     */
    private function getLocalVideoPath($cameraId)
    {
        // Map camera ID to local video files
        $videoMap = [
            1 => 'D:/php/DATT_AI/ai-detection/videos/cam001.mp4',
            2 => 'D:/php/DATT_AI/ai-detection/videos/cam002.mp4',
            3 => 'D:/php/DATT_AI/ai-detection/videos/cam003.mp4',
            4 => 'D:/php/DATT_AI/ai-detection/videos/cam004.mp4',
            5 => 'D:/php/DATT_AI/ai-detection/videos/cam005.mp4',
            6 => 'D:/php/DATT_AI/ai-detection/videos/camera6.mp4',
            7 => 'D:/php/DATT_AI/ai-detection/videos/downloaded_video.mp4',
        ];
        
        return $videoMap[$cameraId] ?? null;
    }
    
    /**
     * Map label từ Laravel (traffic/flood) sang YOLO format
     */
    private function mapLabelToYolo($laravelLabel)
    {
        $mapping = [
            'traffic' => 'Heavy Traffic', // hoặc 'Light Traffic' tùy mức độ
            'flood' => 'Flooding'
        ];
        
        return $mapping[$laravelLabel] ?? $laravelLabel;
    }
    
    /**
     * Tìm camera gần nhất theo đường
     */
    public function findNearestCamera($duongId)
    {
        return \DB::table('camera')
            ->where('duong_id', $duongId)
            ->where('trang_thai_ket_noi', 'active')
            ->first();
    }
}
