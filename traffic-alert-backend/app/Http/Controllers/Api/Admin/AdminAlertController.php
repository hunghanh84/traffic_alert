<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaiDang;
use App\Models\ThietLapCanhBao;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAlertController extends Controller
{
    /**
     * Get all alerts with filters
     */
    public function index(Request $request)
    {
        $query = BaiDang::with(['nguoiDung', 'duong.phuongXa', 'mucDoSuKien', 'media']);

        // Filter by status
        if ($request->has('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Filter by type
        if ($request->has('loai_canh_bao')) {
            $query->where('loai_canh_bao', $request->loai_canh_bao);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('mo_ta', 'like', "%{$search}%")
                  ->orWhereHas('duong', function($q2) use ($search) {
                      $q2->where('ten', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 20);
        $alerts = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $alerts->map(function($alert) {
                    return [
                        'id' => $alert->id,
                        'nguoi_dung' => $alert->nguoiDung ? [
                            'id' => $alert->nguoiDung->id,
                            'ten' => $alert->nguoiDung->ten,
                            'ho_ten' => $alert->nguoiDung->ho_ten,
                        ] : null,
                        'duong' => $alert->duong ? $alert->duong->ten : null,
                        'phuong_xa' => $alert->duong && $alert->duong->phuongXa ? $alert->duong->phuongXa->ten : null,
                        'loai_canh_bao' => $alert->loai_canh_bao,
                        'muc_do' => $alert->mucDoSuKien ? $alert->mucDoSuKien->ma : null,
                        'trang_thai' => $alert->trang_thai,
                        'mo_ta' => $alert->mo_ta,
                        'media_count' => $alert->media->count(),
                        'created_at' => $alert->created_at->format('d/m/Y H:i'),
                    ];
                })->values(),
                'pagination' => [
                    'total' => $alerts->total(),
                    'per_page' => $alerts->perPage(),
                    'current_page' => $alerts->currentPage(),
                    'last_page' => $alerts->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Approve an alert
     */
    public function approve(Request $request, $id)
    {
        try {
            $alert = BaiDang::findOrFail($id);

            if ($alert->trang_thai !== 'cho_duyet') {
                return response()->json([
                    'success' => false,
                    'message' => 'Alert is not pending approval'
                ], 400);
            }

            $alert->update(['trang_thai' => 'da_duyet']);

            // Create active alert setting
            $duration = $request->get('duration', 60); // Default 60 minutes
            ThietLapCanhBao::create([
                'bai_dang_id' => $alert->id,
                'loai_canh_bao' => $alert->loai_canh_bao,
                'kich_hoat' => true,
                'trang_thai' => 'active',
                'thoi_gian_bat_dau' => Carbon::now(),
                'thoi_gian_ket_thuc' => Carbon::now()->addMinutes($duration),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alert approved successfully',
                'data' => $alert
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving alert: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject an alert
     */
    public function reject(Request $request, $id)
    {
        try {
            $alert = BaiDang::findOrFail($id);

            if ($alert->trang_thai !== 'cho_duyet') {
                return response()->json([
                    'success' => false,
                    'message' => 'Alert is not pending approval'
                ], 400);
            }

            $alert->update(['trang_thai' => 'tu_choi']);

            return response()->json([
                'success' => true,
                'message' => 'Alert rejected successfully',
                'data' => $alert
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting alert: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an alert
     */
    public function destroy($id)
    {
        try {
            $alert = BaiDang::findOrFail($id);
            $alert->delete();

            return response()->json([
                'success' => true,
                'message' => 'Alert deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting alert: ' . $e->getMessage()
            ], 500);
        }
    }
}
