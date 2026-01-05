<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuKienGiaoThong;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    /**
     * Get all events with filters
     */
    public function index(Request $request)
    {
        $query = SuKienGiaoThong::with(['duong.phuongXa', 'mucDoSuKien', 'loaiSuKien', 'trang_thai_su_kien']);

        // Filter by status
        if ($request->has('trang_thai_id')) {
            $query->where('trang_thai_id', $request->trang_thai_id);
        }

        // Filter by severity
        if ($request->has('muc_do_id')) {
            $query->where('muc_do_id', $request->muc_do_id);
        }

        // Filter by type
        if ($request->has('loai_su_kien_id')) {
            $query->where('loai_su_kien_id', $request->loai_su_kien_id);
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
        $events = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $events->map(function($event) {
                    return [
                        'id' => $event->id,
                        'duong' => $event->duong ? $event->duong->ten : null,
                        'phuong_xa' => $event->duong && $event->duong->phuongXa ? $event->duong->phuongXa->ten : null,
                        'loai_su_kien' => $event->loaiSuKien ? $event->loaiSuKien->ten : null,
                        'muc_do' => $event->mucDoSuKien ? $event->mucDoSuKien->ma : null,
                        'muc_do_ten' => $event->mucDoSuKien ? $event->mucDoSuKien->ten : null,
                        'trang_thai' => $event->trang_thai_su_kien ? $event->trang_thai_su_kien->ma : null,
                        'trang_thai_ten' => $event->trang_thai_su_kien ? $event->trang_thai_su_kien->ten : null,
                        'mo_ta' => $event->mo_ta,
                        'thoi_gian_bat_dau' => $event->bat_dau_luc ? $event->bat_dau_luc->format('d/m/Y H:i') : null,
                        'thoi_gian_ket_thuc' => $event->ket_thuc_luc ? $event->ket_thuc_luc->format('d/m/Y H:i') : null,
                        'created_at' => $event->created_at->format('d/m/Y H:i'),
                    ];
                })->values(),
                'pagination' => [
                    'total' => $events->total(),
                    'per_page' => $events->perPage(),
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get single event
     */
    public function show($id)
    {
        $event = SuKienGiaoThong::with(['duong.phuongXa.thanhPho', 'mucDoSuKien', 'loaiSuKien'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $event->id,
                'duong' => $event->duong ? [
                    'id' => $event->duong->id,
                    'ten' => $event->duong->ten,
                    'phuong_xa' => $event->duong->phuongXa ? [
                        'id' => $event->duong->phuongXa->id,
                        'ten' => $event->duong->phuongXa->ten,
                        'thanh_pho' => $event->duong->phuongXa->thanhPho ? [
                            'id' => $event->duong->phuongXa->thanhPho->id,
                            'ten' => $event->duong->phuongXa->thanhPho->ten,
                        ] : null,
                    ] : null,
                ] : null,
                'loai_su_kien' => $event->loaiSuKien,
                'muc_do_su_kien' => $event->mucDoSuKien,
                'trang_thai' => $event->trang_thai,
                'mo_ta' => $event->mo_ta,
                'thoi_gian_bat_dau' => $event->thoi_gian_bat_dau ? $event->thoi_gian_bat_dau->toISOString() : null,
                'thoi_gian_ket_thuc' => $event->thoi_gian_ket_thuc ? $event->thoi_gian_ket_thuc->toISOString() : null,
                'created_at' => $event->created_at->toISOString(),
                'updated_at' => $event->updated_at->toISOString(),
            ]
        ]);
    }

    /**
     * Create new event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'duong_id' => 'required|exists:duong,id',
            'loai_su_kien_id' => 'required|exists:loai_su_kien,id',
            'muc_do' => 'required|exists:muc_do_su_kien,ma',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|in:dang_dien_ra,da_ket_thuc,tam_dung',
            'thoi_gian_bat_dau' => 'nullable|date',
            'thoi_gian_ket_thuc' => 'nullable|date|after:thoi_gian_bat_dau',
        ]);

        $event = SuKienGiaoThong::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo sự kiện thành công',
            'data' => $event
        ], 201);
    }

    /**
     * Update event
     */
    public function update(Request $request, $id)
    {
        $event = SuKienGiaoThong::findOrFail($id);

        $validated = $request->validate([
            'duong_id' => 'sometimes|exists:duong,id',
            'loai_su_kien_id' => 'sometimes|exists:loai_su_kien,id',
            'muc_do' => 'sometimes|exists:muc_do_su_kien,ma',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'sometimes|in:dang_dien_ra,da_ket_thuc,tam_dung',
            'thoi_gian_bat_dau' => 'nullable|date',
            'thoi_gian_ket_thuc' => 'nullable|date|after:thoi_gian_bat_dau',
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sự kiện thành công',
            'data' => $event
        ]);
    }

    /**
     * Delete event
     */
    public function destroy($id)
    {
        $event = SuKienGiaoThong::findOrFail($id);
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa sự kiện thành công'
        ]);
    }

    /**
     * Toggle event status
     */
    public function toggleStatus($id)
    {
        $event = SuKienGiaoThong::findOrFail($id);

        $event->trang_thai = $event->trang_thai === 'dang_dien_ra' ? 'da_ket_thuc' : 'dang_dien_ra';
        $event->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $event
        ]);
    }
}
