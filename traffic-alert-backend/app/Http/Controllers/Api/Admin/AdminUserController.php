<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Get all users with filters
     */
    public function index(Request $request)
    {
        $query = NguoiDung::with(['khuVuc', 'phuongXa']);

        // Filter by role
        if ($request->has('vai_tro')) {
            $query->where('vai_tro', $request->vai_tro);
        }

        // Filter by status
        if ($request->has('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ten_dang_nhap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('so_dien_thoai', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'ten_dang_nhap' => $user->ten_dang_nhap,
                        'email' => $user->email,
                        'so_dien_thoai' => $user->so_dien_thoai,
                        'vai_tro' => $user->vai_tro,
                        'trang_thai' => $user->trang_thai,
                        'khu_vuc' => $user->khuVuc ? $user->khuVuc->ten : null,
                        'phuong_xa' => $user->phuongXa ? $user->phuongXa->ten : null,
                        'created_at' => $user->created_at->format('d/m/Y H:i'),
                    ];
                })->values(),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get single user
     */
    public function show($id)
    {
        $user = NguoiDung::with(['khuVuc', 'phuongXa.thanhPho', 'baiDang'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'ten_dang_nhap' => $user->ten_dang_nhap,
                'email' => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'vai_tro' => $user->vai_tro,
                'trang_thai' => $user->trang_thai,
                'khu_vuc' => $user->khuVuc,
                'phuong_xa' => $user->phuongXa ? [
                    'id' => $user->phuongXa->id,
                    'ten' => $user->phuongXa->ten,
                    'thanh_pho' => $user->phuongXa->thanhPho ? [
                        'id' => $user->phuongXa->thanhPho->id,
                        'ten' => $user->phuongXa->thanhPho->ten,
                    ] : null,
                ] : null,
                'bai_dang_count' => $user->baiDang->count(),
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'updated_at' => $user->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Create new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_dang_nhap' => 'required|string|max:100|unique:nguoi_dung',
            'email' => 'required|email|max:255|unique:nguoi_dung',
            'mat_khau' => 'required|string|min:6',
            'so_dien_thoai' => 'nullable|string|max:30',
            'vai_tro' => 'required|in:admin,nguoi_dung,dieu_hanh',
            'trang_thai' => 'required|in:hoat_dong,khoa,tam_dung',
            'khu_vuc_id' => 'nullable|exists:khu_vuc,id',
        ]);

        $validated['mat_khau'] = Hash::make($validated['mat_khau']);

        $user = NguoiDung::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo người dùng thành công',
            'data' => $user
        ], 201);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = NguoiDung::findOrFail($id);

        $validated = $request->validate([
            'ten_dang_nhap' => ['sometimes', 'string', 'max:100', Rule::unique('nguoi_dung')->ignore($user->id)],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('nguoi_dung')->ignore($user->id)],
            'mat_khau' => 'sometimes|string|min:6',
            'so_dien_thoai' => 'nullable|string|max:30',
            'vai_tro' => 'sometimes|in:admin,nguoi_dung,dieu_hanh',
            'trang_thai' => 'sometimes|in:hoat_dong,khoa,tam_dung',
            'khu_vuc_id' => 'nullable|exists:khu_vuc,id',
        ]);

        if (isset($validated['mat_khau'])) {
            $validated['mat_khau'] = Hash::make($validated['mat_khau']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật người dùng thành công',
            'data' => $user
        ]);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = NguoiDung::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa tài khoản của chính mình'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa người dùng thành công'
        ]);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus($id)
    {
        $user = NguoiDung::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thay đổi trạng thái tài khoản của chính mình'
            ], 403);
        }

        $user->trang_thai = $user->trang_thai === 'hoat_dong' ? 'khoa' : 'hoat_dong';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $user
        ]);
    }
}
