<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuKienGiaoThong;
use App\Models\NguoiDung;
use App\Models\KetQuaAI;
use App\Models\ThongBao;
use App\Models\BaiDang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\StatisticsExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AdminStatisticsController extends Controller
{
    /**
     * Lấy thống kê tổng quan
     */
    public function overview()
    {
        $totalEvents = SuKienGiaoThong::count();
        $totalUsers = NguoiDung::count();
        $totalAIResults = KetQuaAI::count();
        $totalNotifications = ThongBao::count();

        $activeEvents = SuKienGiaoThong::whereIn('trang_thai_id', [1, 2])->count();
        $verifiedAI = KetQuaAI::where('da_xac_minh', true)->count();
        $sentNotifications = ThongBao::where('trang_thai_gui', 'sent')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_events' => $totalEvents,
                'total_users' => $totalUsers,
                'total_ai_results' => $totalAIResults,
                'total_notifications' => $totalNotifications,
                'active_events' => $activeEvents,
                'verified_ai' => $verifiedAI,
                'sent_notifications' => $sentNotifications,
            ]
        ]);
    }

    /**
     * Thống kê sự kiện theo loại
     */
    public function eventsByType()
    {
        $events = SuKienGiaoThong::select('loai_su_kien_id', DB::raw('count(*) as total'))
            ->with('loaiSuKien')
            ->groupBy('loai_su_kien_id')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->loaiSuKien->ten ?? 'Unknown',
                    'value' => $item->total
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Thống kê theo thời gian (7 ngày gần nhất)
     */
    public function eventsTrend()
    {
        $trend = SuKienGiaoThong::select(
                DB::raw('DATE(bat_dau_luc) as date'),
                DB::raw('count(*) as count')
            )
            ->where('bat_dau_luc', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $trend
        ]);
    }

    /**
     * Thống kê AI theo nhãn
     */
    public function aiByLabel()
    {
        $results = KetQuaAI::select('nhan', DB::raw('count(*) as total'))
            ->groupBy('nhan')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->nhan,
                    'value' => $item->total
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Top người dùng hoạt động
     */
    public function topUsers()
    {
        $users = NguoiDung::withCount('baiDang')
            ->orderBy('bai_dang_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->ten_dang_nhap,
                    'count' => $user->bai_dang_count
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Thống kê theo khu vực
     */
    public function eventsByArea()
    {
        $events = SuKienGiaoThong::select('duong_id', DB::raw('count(*) as total'))
            ->with('duong.phuongXa')
            ->groupBy('duong_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'area' => $item->duong->phuongXa->ten ?? 'Unknown',
                    'count' => $item->total
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Xuất báo cáo Excel
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $fileName = 'thong-ke-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return Excel::download(
            new StatisticsExport($startDate, $endDate),
            $fileName
        );
    }
}
