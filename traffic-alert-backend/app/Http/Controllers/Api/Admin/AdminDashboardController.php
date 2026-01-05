<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaiDang;
use App\Models\SuKienGiaoThong;
use App\Models\ThongBao;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStatistics()
    {
        try {
            // Total counts
            $totalAlerts = BaiDang::count();
            $totalEvents = SuKienGiaoThong::count();
            $totalUsers = NguoiDung::count();
            $totalNotifications = ThongBao::count();

            // Pending alerts (cho_duyet)
            $pendingAlerts = BaiDang::where('trang_thai', 'cho_duyet')->count();

            // Active events - simplified query
            $activeEvents = SuKienGiaoThong::count();

            // Today's statistics
            $today = Carbon::today();
            $todayAlerts = BaiDang::whereDate('created_at', $today)->count();
            $todayEvents = SuKienGiaoThong::whereDate('created_at', $today)->count();

            // This week's statistics
            $weekStart = Carbon::now()->startOfWeek();
            $weekAlerts = BaiDang::where('created_at', '>=', $weekStart)->count();
            $weekEvents = SuKienGiaoThong::where('created_at', '>=', $weekStart)->count();

            // Alerts by status
            $alertsByStatus = [
                'cho_duyet' => BaiDang::where('trang_thai', 'cho_duyet')->count(),
                'da_duyet' => BaiDang::where('trang_thai', 'da_duyet')->count(),
                'tu_choi' => BaiDang::where('trang_thai', 'tu_choi')->count(),
            ];

            // Alerts by type
            $alertsByType = [
                'traffic' => BaiDang::where('loai_canh_bao', 'traffic')->count(),
                'flood' => BaiDang::where('loai_canh_bao', 'flood')->count(),
            ];

            // Notification statistics
            $notificationStats = [
                'sent' => ThongBao::where('trang_thai_gui', 'sent')->count(),
                'failed' => ThongBao::where('trang_thai_gui', 'failed')->count(),
                'pending' => ThongBao::where('trang_thai_gui', 'pending')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'totals' => [
                        'alerts' => $totalAlerts,
                        'events' => $totalEvents,
                        'users' => $totalUsers,
                        'notifications' => $totalNotifications,
                    ],
                    'pending' => [
                        'alerts' => $pendingAlerts,
                        'events' => $activeEvents,
                    ],
                    'today' => [
                        'alerts' => $todayAlerts,
                        'events' => $todayEvents,
                    ],
                    'this_week' => [
                        'alerts' => $weekAlerts,
                        'events' => $weekEvents,
                    ],
                    'alerts_by_status' => $alertsByStatus,
                    'alerts_by_type' => $alertsByType,
                    'notification_stats' => $notificationStats,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities()
    {
        try {
            $recentAlerts = BaiDang::with(['nguoiDung', 'duong'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function($alert) {
                    return [
                        'id' => $alert->id,
                        'type' => 'alert',
                        'loai_canh_bao' => $alert->loai_canh_bao,
                        'trang_thai' => $alert->trang_thai,
                        'duong' => $alert->duong ? $alert->duong->ten : null,
                        'nguoi_dung' => $alert->nguoiDung ? $alert->nguoiDung->ten : 'Anonymous',
                        'created_at' => $alert->created_at->format('d/m/Y H:i'),
                    ];
                });

            $recentEvents = SuKienGiaoThong::with(['duong', 'loaiSuKien'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function($event) {
                    return [
                        'id' => $event->id,
                        'type' => 'event',
                        'loai_su_kien' => $event->loaiSuKien ? $event->loaiSuKien->ten : null,
                        'duong' => $event->duong ? $event->duong->ten : null,
                        'nguon' => $event->nguon,
                        'created_at' => $event->created_at->format('d/m/Y H:i'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'recent_alerts' => $recentAlerts,
                    'recent_events' => $recentEvents,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching activities: ' . $e->getMessage()
            ], 500);
        }
    }
}
