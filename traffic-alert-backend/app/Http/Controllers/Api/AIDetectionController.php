<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KetQuaAI;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AIDetectionController extends Controller
{
    /**
     * Nhận kết quả detection từ Python service
     */
    public function detect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nhan' => 'required|string',
            'do_tin_cay' => 'required|numeric|min:0|max:1',
            'timestamp' => 'sometimes|string',
            'source' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Tạo media record (nếu cần)
            $media = Media::create([
                'loai' => 'stream',
                'duong_dan' => $request->source ?? 'youtube_livestream',
            ]);

            // Lưu kết quả AI
            $result = KetQuaAI::create([
                'media_id' => $media->id,
                'nhan' => $request->nhan,
                'do_tin_cay' => $request->do_tin_cay,
                'raw_json' => $request->all(),
                'da_xac_minh' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detection saved successfully',
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving detection',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
