<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $channelId;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token');
        $this->channelId = config('telegram.channel_id');
        $this->apiUrl = config('telegram.api_url');
    }

    /**
     * Send a message to Telegram channel
     */
    public function sendMessage($message, $parseMode = 'HTML')
    {
        if (empty($this->botToken) || empty($this->channelId)) {
            Log::warning('Telegram bot token or channel ID not configured');
            return false;
        }

        try {
            $url = $this->apiUrl . $this->botToken . '/sendMessage';
            
            $response = Http::post($url, [
                'chat_id' => $this->channelId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                Log::info('Telegram message sent successfully');
                return true;
            } else {
                Log::error('Failed to send Telegram message: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram API error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format traffic event notification message
     */
    public function formatTrafficEventMessage($suKien, $duong, $loaiSuKien)
    {
        $icon = $this->getEventIcon($loaiSuKien->ma);
        $severity = $this->getSeverityText($suKien);
        
        $message = "🚨 <b>CẢNH BÁO GIAO THÔNG MỚI</b>\n\n";
        $message .= "{$icon} <b>Loại sự cố:</b> {$loaiSuKien->ten}\n";
        $message .= "📍 <b>Địa điểm:</b> Đường {$duong->ten}\n";
        
        if ($duong->phuongXa) {
            $message .= "🏘 <b>Khu vực:</b> Phường {$duong->phuongXa->ten}";
            if ($duong->phuongXa->thanhPho) {
                $message .= ", {$duong->phuongXa->thanhPho->ten}";
            }
            $message .= "\n";
        }
        
        if ($severity) {
            $message .= "⚠️ <b>Mức độ:</b> {$severity}\n";
        }
        
        $message .= "🕐 <b>Thời gian:</b> " . $suKien->bat_dau_luc->format('H:i, d/m/Y') . "\n";
        
        if ($suKien->mo_ta) {
            $message .= "\n📝 <i>{$suKien->mo_ta}</i>\n";
        }
        
        $message .= "\n💡 <i>Thông tin được xác nhận bởi hệ thống dựa trên báo cáo từ cộng đồng</i>";
        
        return $message;
    }

    /**
     * Get icon based on event type
     */
    private function getEventIcon($eventType)
    {
        $icons = [
            'traffic_jam' => '🚗',
            'flood' => '🌊',
            'accident' => '💥',
            'road_work' => '🚧',
            'fire' => '🔥',
            'other' => '⚠️',
        ];

        return $icons[$eventType] ?? '⚠️';
    }

    /**
     * Get severity text
     */
    private function getSeverityText($suKien)
    {
        if ($suKien->muc_do_id) {
            $mucDo = \App\Models\MucDoSuKien::find($suKien->muc_do_id);
            if ($mucDo) {
                return $mucDo->ten;
            }
        }
        return null;
    }

    /**
     * Send traffic event notification
     */
    public function sendTrafficEventNotification($suKienId)
    {
        try {
            $suKien = \App\Models\SuKienGiaoThong::with([
                'duong.phuongXa.thanhPho',
                'loaiSuKien',
                'mucDoSuKien'
            ])->find($suKienId);

            if (!$suKien || !$suKien->duong || !$suKien->loaiSuKien) {
                Log::warning("Traffic event #{$suKienId} not found or incomplete data");
                return false;
            }

            $message = $this->formatTrafficEventMessage(
                $suKien,
                $suKien->duong,
                $suKien->loaiSuKien
            );

            return $this->sendMessage($message);
        } catch (\Exception $e) {
            Log::error('Error sending traffic event notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification based on ThongBao record
     */
    public function sendNotificationFromThongBao($thongBaoId)
    {
        try {
            $thongBao = \App\Models\ThongBao::with([
                'suKien.duong.phuongXa.thanhPho',
                'suKien.loaiSuKien',
                'suKien.mucDoSuKien'
            ])->find($thongBaoId);

            if (!$thongBao) {
                Log::warning("ThongBao #{$thongBaoId} not found");
                return false;
            }

            $suKien = $thongBao->suKien;
            if (!$suKien || !$suKien->duong || !$suKien->loaiSuKien) {
                Log::warning("Related event data incomplete for ThongBao #{$thongBaoId}");
                return false;
            }

            $icon = $this->getEventIcon($suKien->loaiSuKien->ma);
            $severity = $this->getSeverityText($suKien);
            
            // Build message from ThongBao data
            $message = "🚨 <b>CẢNH BÁO GIAO THÔNG MỚI</b>\n\n";
            $message .= "{$icon} <b>Loại sự cố:</b> {$suKien->loaiSuKien->ten}\n";
            $message .= "📍 <b>Địa điểm:</b> Đường {$suKien->duong->ten}\n";
            
            if ($suKien->duong->phuongXa) {
                $message .= "🏘 <b>Khu vực:</b> Phường {$suKien->duong->phuongXa->ten}";
                if ($suKien->duong->phuongXa->thanhPho) {
                    $message .= ", {$suKien->duong->phuongXa->thanhPho->ten}";
                }
                $message .= "\n";
            }
            
            if ($severity) {
                $message .= "⚠️ <b>Mức độ:</b> {$severity}\n";
            }
            
            $message .= "🕐 <b>Thời gian:</b> " . $suKien->bat_dau_luc->format('H:i, d/m/Y') . "\n";
            
            // Use noi_dung from ThongBao
            if ($thongBao->noi_dung) {
                $message .= "\n📝 <i>{$thongBao->noi_dung}</i>";
            }

            return $this->sendMessage($message);
        } catch (\Exception $e) {
            Log::error('Error sending notification from ThongBao: ' . $e->getMessage());
            return false;
        }
    }
}
