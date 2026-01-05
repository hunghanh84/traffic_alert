<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DetectionService;
use Illuminate\Http\Request;

class DetectionController extends Controller
{
    protected $detectionService;

    public function __construct(DetectionService $detectionService)
    {
        $this->detectionService = $detectionService;
    }

    /**
     * Test detection service health
     */
    public function health()
    {
        $health = $this->detectionService->getHealth();
        
        return response()->json([
            'success' => $health !== null,
            'data' => $health
        ]);
    }

    /**
     * Detect image from upload
     */
    public function detect(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120'
        ]);

        $image = $request->file('image');
        $path = $image->store('temp', 'public');
        $fullPath = storage_path('app/public/' . $path);

        $result = $this->detectionService->detectImage($fullPath);

        // Clean up temp file
        @unlink($fullPath);

        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Detection failed'
        ], 500);
    }
}
