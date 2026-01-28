<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\KetQuaAI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAIResultController extends Controller
{
    /**
     * Lấy danh sách kết quả AI
     */
    public function index(Request $request)
    {
        $query = KetQuaAI::with(['media', 'nguoiXacMinh']);

        // Filter by verification status
        if ($request->has('da_xac_minh')) {
            $query->where('da_xac_minh', $request->da_xac_minh === 'true');
        }

        // Filter by label
        if ($request->has('nhan')) {
            $query->where('nhan', $request->nhan);
        }

        // Filter by confidence
        if ($request->has('min_confidence')) {
            $query->where('do_tin_cay', '>=', $request->min_confidence);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nhan', 'like', "%{$search}%");
        }

        $results = $query->orderBy('created_at', 'desc')
                        ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Lấy chi tiết kết quả AI
     */
    public function show($id)
    {
        $result = KetQuaAI::with(['media', 'nguoiXacMinh'])->find($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kết quả AI'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Xác minh kết quả AI
     */
    public function verify(Request $request, $id)
    {
        $result = KetQuaAI::find($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kết quả AI'
            ], 404);
        }

        if ($result->da_xac_minh) {
            return response()->json([
                'success' => false,
                'message' => 'Kết quả đã được xác minh'
            ], 400);
        }

        $result->update([
            'da_xac_minh' => true,
            'xac_minh_boi' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Xác minh thành công',
            'data' => $result->load(['media', 'nguoiXacMinh'])
        ]);
    }

    /**
     * Hủy xác minh kết quả AI
     */
    public function unverify($id)
    {
        $result = KetQuaAI::find($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kết quả AI'
            ], 404);
        }

        $result->update([
            'da_xac_minh' => false,
            'xac_minh_boi' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy xác minh',
            'data' => $result
        ]);
    }

    /**
     * Xóa kết quả AI
     */
    public function destroy($id)
    {
        $result = KetQuaAI::find($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kết quả AI'
            ], 404);
        }

        $result->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa kết quả AI thành công'
        ]);
    }

    /**
     * Lấy thống kê kết quả AI
     */
    public function statistics()
    {
        $total = KetQuaAI::count();
        $verified = KetQuaAI::where('da_xac_minh', true)->count();
        $unverified = KetQuaAI::where('da_xac_minh', false)->count();
        
        $byLabel = KetQuaAI::selectRaw('nhan, COUNT(*) as count')
            ->groupBy('nhan')
            ->get();

        $avgConfidence = KetQuaAI::avg('do_tin_cay');

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'verified' => $verified,
                'unverified' => $unverified,
                'by_label' => $byLabel,
                'avg_confidence' => round($avgConfidence, 2)
            ]
        ]);
    }
}
