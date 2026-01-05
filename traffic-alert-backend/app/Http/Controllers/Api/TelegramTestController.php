<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramTestController extends Controller
{
    /**
     * Test sending a simple message to Telegram
     */
    public function testSimpleMessage()
    {
        $telegramService = new TelegramService();
        
        $message = "🚨 <b>CẢNH BÁO GIAO THÔNG MỚI</b>\n\n";
        $message .= "🚗 <b>Loại sự cố:</b> Tắc đường\n";
        $message .= "📍 <b>Địa điểm:</b> Đường Lê Duẩn\n";
        $message .= "🏘 <b>Khu vực:</b> Phường Hải Châu 1, Đà Nẵng\n";
        $message .= "⚠️ <b>Mức độ:</b> Cao\n";
        $message .= "🕐 <b>Thời gian:</b> " . now()->format('H:i, d/m/Y') . "\n\n";
        $message .= "💡 <i>Thông tin được xác nhận bởi hệ thống dựa trên báo cáo từ cộng đồng</i>";
        
        $result = $telegramService->sendMessage($message);
        
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được gửi lên Telegram thành công!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi tin nhắn. Vui lòng kiểm tra cấu hình TELEGRAM_BOT_TOKEN và TELEGRAM_CHANNEL_ID trong file .env',
            ], 500);
        }
    }
}
