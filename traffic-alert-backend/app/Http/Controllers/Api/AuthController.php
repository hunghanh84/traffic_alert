<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới (Register)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_dang_nhap' => 'required|string|max:100|unique:nguoi_dung',
            'email' => 'required|string|email|max:255|unique:nguoi_dung',
            'mat_khau' => 'required|string|min:6',
            'so_dien_thoai' => 'nullable|string|max:30',
            'phuong_xa_id' => 'nullable|exists:phuong_xa,id',
            'khu_vuc_id' => 'nullable|exists:khu_vuc,id',
        ], [
            'ten_dang_nhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'email.unique' => 'Email đã được sử dụng.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'phuong_xa_id.exists' => 'Phường không hợp lệ.',
            'khu_vuc_id.exists' => 'Khu vực không hợp lệ.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Tạo người dùng mới
        // Model NguoiDung đã có Mutator để hash mật khẩu tự động
        $user = NguoiDung::create([
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'email' => $request->email,
            'mat_khau' => $request->mat_khau,
            'so_dien_thoai' => $request->so_dien_thoai,
            'phuong_xa_id' => $request->phuong_xa_id,
            'khu_vuc_id' => $request->khu_vuc_id,
            'vai_tro' => 'nguoi_dung', // Mặc định là người dùng thường
            'trang_thai' => 'hoat_dong',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký tài khoản thành công',
            'data' => [
                'user' => $user->load(['phuongXa.thanhPho', 'khuVuc']),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    /**
     * Đăng nhập (Login)
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Chấp nhận cả email hoặc tên đăng nhập
            'mat_khau' => 'required|string',
        ]);

        // Tìm user theo email hoặc tên đăng nhập
        $user = NguoiDung::where('email', $request->login)
            ->orWhere('ten_dang_nhap', $request->login)
            ->first();

        // Kiểm tra user và mật khẩu
        if (!$user || !Hash::check($request->mat_khau, $user->mat_khau)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác'
            ], 401);
        }

        // Kiểm tra trạng thái tài khoản
        if ($user->trang_thai !== 'hoat_dong') {
            $statusText = $user->trang_thai === 'khoa' ? 'bị khóa' : 'tạm dừng';
            return response()->json([
                'success' => false,
                'message' => "Tài khoản của bạn đã $statusText. Vui lòng liên hệ Admin."
            ], 403);
        }

        // Xóa các token cũ nếu muốn (tùy chọn: chỉ cho phép 1 thiết bị đăng nhập)
        // $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'user' => $user->load(['phuongXa.thanhPho', 'khuVuc']),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load(['phuongXa.thanhPho', 'khuVuc'])
        ]);
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Cập nhật thông tin tài khoản
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'ten_dang_nhap' => 'required|string|max:100|unique:nguoi_dung,ten_dang_nhap,' . $user->id,
            'email' => 'required|string|email|max:255|unique:nguoi_dung,email,' . $user->id,
            'so_dien_thoai' => 'nullable|string|max:30',
            'phuong_xa_id' => 'nullable|exists:phuong_xa,id',
            'khu_vuc_id' => 'nullable|exists:khu_vuc,id',
            'mat_khau_cu' => 'required_with:mat_khau_moi|string',
            'mat_khau_moi' => 'nullable|string|min:6',
        ], [
            'ten_dang_nhap.unique' => 'Tên đăng nhập đã được sử dụng.',
            'email.unique' => 'Email đã được sử dụng.',
            'phuong_xa_id.exists' => 'Phường không hợp lệ.',
            'khu_vuc_id.exists' => 'Khu vực không hợp lệ.',
            'mat_khau_cu.required_with' => 'Vui lòng nhập mật khẩu cũ để đổi mật khẩu.',
            'mat_khau_moi.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->ten_dang_nhap = $request->ten_dang_nhap;
        $user->email = $request->email;
        $user->so_dien_thoai = $request->so_dien_thoai;
        $user->phuong_xa_id = $request->phuong_xa_id;
        $user->khu_vuc_id = $request->khu_vuc_id;

        // Verify old password and update if changing password
        if ($request->filled('mat_khau_moi')) {
            if (!Hash::check($request->mat_khau_cu, $user->mat_khau)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu cũ không chính xác'
                ], 422);
            }
            $user->mat_khau = $request->mat_khau_moi; // Mutator in model handles hashing
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => $user->load(['phuongXa.thanhPho', 'khuVuc'])
        ]);
    }

    /**
     * Vô hiệu hóa tài khoản (Tạm dừng)
     */
    public function deactivate(Request $request)
    {
        $user = $request->user();
        $user->trang_thai = 'tam_dung';
        $user->save();

        // Thu hồi tokens
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tài khoản đã được tạm dừng.'
        ]);
    }
}
