<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends Controller
{
    /**
     * Gửi mã xác thực qua email
     */
    public function sendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:nguoi_dung,email',
        ], [
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.exists' => 'Email không tồn tại trong hệ thống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = NguoiDung::where('email', $request->email)->first();

        // Kiểm tra email đã xác thực chưa
        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email đã được xác thực',
            ], 400);
        }

        // Tạo mã xác thực 6 số
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu mã và thời gian hết hạn (10 phút)
        $user->verification_code = $code;
        $user->verification_code_expires_at = now()->addMinutes(10);
        $user->save();

        // Gửi email
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->ten_dang_nhap));

            return response()->json([
                'success' => true,
                'message' => 'Mã xác thực đã được gửi đến email của bạn',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi email. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xác thực mã
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:nguoi_dung,email',
            'code' => 'required|string|size:6',
        ], [
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.exists' => 'Email không tồn tại',
            'code.required' => 'Mã xác thực là bắt buộc',
            'code.size' => 'Mã xác thực phải có 6 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = NguoiDung::where('email', $request->email)->first();

        // Kiểm tra email đã xác thực chưa
        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email đã được xác thực',
            ], 400);
        }

        // Kiểm tra mã xác thực
        if ($user->verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác thực không đúng',
            ], 400);
        }

        // Kiểm tra mã đã hết hạn chưa
        if (now()->greaterThan($user->verification_code_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác thực đã hết hạn. Vui lòng yêu cầu mã mới.',
            ], 400);
        }

        // Xác thực thành công
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Xác thực email thành công',
        ]);
    }
}
