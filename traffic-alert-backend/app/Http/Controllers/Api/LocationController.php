<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhuongXa;
use App\Models\Duong;
use App\Models\ThanhPho;
use App\Models\KhuVuc;
use App\Models\BaiDang;
use App\Models\MucDoSuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Get all wards (phuong/xa)
     */
    public function getWards()
    {
        $wards = PhuongXa::select('id', 'ten', 'ma')
            ->orderBy('ten')
            ->get()
            ->map(function ($ward) {
                return [
                    'value' => $ward->id, // Sử dụng ID thay vì mã để tránh null
                    'label' => $ward->ten,
                    'id' => $ward->id
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $wards
        ]);
    }

    /**
     * Get all streets (duong)
     */
    public function getStreets(Request $request)
    {
        $query = Duong::active()
            ->select('id', 'ten', 'ma', 'loai_duong', 'khu_vuc_id', 'phuong_id')
            ->with(['phuongXa:id,ten']);

        // Filter by streets that have at least one active alert in thiet_lap_canh_bao
        if ($request->has('has_alerts')) {
            $query->whereHas('thietLapCanhBaos', function ($q) {
                $q->where('trang_thai', 'active')
                  ->where('kich_hoat', true)
                  ->where('thoi_gian_ket_thuc', '>', now());
            });

            // Order by the most recent active alert
            $query->withMax(['thietLapCanhBaos as latest_alert_at' => function($q) {
                $q->where('trang_thai', 'active')
                  ->where('kich_hoat', true)
                  ->where('thoi_gian_ket_thuc', '>', now());
            }], 'created_at');

            // Get expiration time
            $query->withMax(['thietLapCanhBaos as max_expires_at' => function($q) {
                $q->where('trang_thai', 'active')
                  ->where('kich_hoat', true)
                  ->where('thoi_gian_ket_thuc', '>', now());
            }], 'thoi_gian_ket_thuc');

            $query->orderBy('latest_alert_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $streets = $query->get()
            ->map(function ($street) {
                $expiresAt = null;
                if ($street->max_expires_at) {
                    try {
                        $expiresAt = \Carbon\Carbon::parse($street->max_expires_at)->format('H:i');
                    } catch (\Exception $e) {
                        Log::error("Error parsing date: " . $e->getMessage());
                    }
                }

                return [
                    'value' => $street->id,
                    'label' => $street->ten,
                    'ten' => $street->ten,
                    'id' => $street->id,
                    'khu_vuc_id' => $street->khu_vuc_id,
                    'phuong_id' => $street->phuong_id,
                    'phuong_ten' => $street->phuongXa ? $street->phuongXa->ten : null,
                    'type' => $street->loai_duong,
                    'expires_at' => $expiresAt,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $streets
        ]);
    }

    public function getStreetsByWard($wardId)
    {
        $streets = Duong::active()
            ->where('phuong_id', $wardId)
            ->select('id', 'ten', 'ma', 'loai_duong', 'khu_vuc_id', 'phuong_id')
            ->orderBy('ten')
            ->get()
            ->map(function ($street) {
                return [
                    'value' => $street->id, // Sử dụng ID thay vì mã để tránh null
                    'label' => $street->ten,
                    'id' => $street->id,
                    'khu_vuc_id' => $street->khu_vuc_id,
                    'phuong_id' => $street->phuong_id,
                    'type' => $street->loai_duong
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $streets,
            'count' => $streets->count()
        ]);
    }

    /**
     * Get location data for alert form
     */
    public function getSeverityLevels()
    {
        $levels = MucDoSuKien::orderBy('uu_tien', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $levels->map(function($level) {
                // Color mapping as requested by user
                $colors = [
                    'low' => '#10b981',
                    'medium' => '#f59e0b',
                    'high' => '#ef4444',
                    'critical' => '#dc2626'
                ];
                
                return [
                    'value' => $level->ma,
                    'label' => $level->ten,
                    'color' => $colors[$level->ma] ?? '#6b7280',
                    'priority' => $level->uu_tien
                ];
            })
        ]);
    }

    public function getLocationData()
    {
        // Get all cities
        $cities = ThanhPho::select('id', 'ten', 'ma')
            ->orderBy('ten')
            ->get()
            ->map(function ($city) {
                return [
                    'value' => $city->id,
                    'label' => $city->ten,
                    'id' => $city->id,
                    'ten_thanh_pho' => $city->ten
                ];
            });

        // Get all wards with city relationship
        $wards = PhuongXa::select('id', 'ten', 'ma', 'thanh_pho_id')
            ->orderBy('ten')
            ->get()
            ->map(function ($ward) {
                return [
                    'value' => $ward->id,
                    'label' => $ward->ten,
                    'id' => $ward->id,
                    'ten_phuong' => $ward->ten,
                    'thanh_pho_id' => $ward->thanh_pho_id
                ];
            });

        // Get all zones with ward relationship
        $zones = KhuVuc::select('id', 'ten', 'phuong_id')
            ->orderBy('ten')
            ->get()
            ->map(function ($zone) {
                return [
                    'value' => $zone->id,
                    'label' => $zone->ten,
                    'id' => $zone->id,
                    'ten_khu_vuc' => $zone->ten,
                    'phuong_id' => $zone->phuong_id
                ];
            });

        $streets = Duong::active()
            ->select('id', 'ten', 'ma', 'loai_duong', 'khu_vuc_id', 'phuong_id')
            ->orderBy('ten')
            ->get()
            ->map(function ($street) {
                return [
                    'value' => $street->id,
                    'label' => $street->ten,
                    'id' => $street->id,
                    'khu_vuc_id' => $street->khu_vuc_id,
                    'phuong_id' => $street->phuong_id,
                    'type' => $street->loai_duong
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'cities' => $cities,
                'wards' => $wards,
                'zones' => $zones,
                'streets' => $streets
            ]
        ]);
    }

    /**
     * Get street detail with alerts
     */
    public function getStreetDetail($id)
    {
        $street = Duong::with(['phuongXa.thanhPho', 'khuVuc'])
            ->find($id);

        if (!$street) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tuyến đường'
            ], 404);
        }

        // Get active alerts from thiet_lap_canh_bao for this street
        $alerts = BaiDang::where('duong_id', $id)
            ->whereHas('thietLapCanhBaos', function($q) {
                $q->where('trang_thai', 'active')
                  ->where('kich_hoat', true)
                  ->where('thoi_gian_ket_thuc', '>', now());
            })
            ->with(['media', 'phuongXa.thanhPho', 'khuVuc.phuongXa.thanhPho', 'mucDoSuKien', 'nguoiDung', 'thietLapCanhBaos' => function($q) {
                $q->where('trang_thai', 'active')
                  ->where('kich_hoat', true)
                  ->where('thoi_gian_ket_thuc', '>', now());
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($alert) use ($street) {
                $phuongTen = $alert->phuongXa->ten ?? ($alert->khuVuc->phuongXa->ten ?? ($street->phuongXa->ten ?? ''));
                $thanhPhoTen = $alert->phuongXa?->thanhPho?->ten ?? ($alert->khuVuc?->phuongXa?->thanhPho?->ten ?? 'Đà Nẵng');
                $activeAlert = $alert->thietLapCanhBaos->first();

                return [
                    'id' => $alert->id,
                    'active_id' => $activeAlert ? $activeAlert->id : null,
                    'loai_canh_bao' => $activeAlert ? $activeAlert->loai_canh_bao : $alert->loai_canh_bao,
                    'muc_do' => $alert->mucDoSuKien ? $alert->mucDoSuKien->ma : null,
                    'muc_do_ten' => $alert->mucDoSuKien ? $alert->mucDoSuKien->ten : null,
                    'mo_ta' => $alert->mo_ta,
                    'dia_chi' => "Đường {$street->ten}, Phường {$phuongTen}, {$thanhPhoTen}",
                    'nguoi_dung' => $alert->nguoiDung ? [
                        'id' => $alert->nguoiDung->id,
                        'ten' => $alert->nguoiDung->ten,
                        'ho_ten' => $alert->nguoiDung->ho_ten,
                        'anh_dai_dien' => $alert->nguoiDung->anh_dai_dien,
                    ] : null,
                    'created_at' => $alert->created_at->format('d-m-Y H:i:s'),
                    'expires_at' => $activeAlert ? $activeAlert->thoi_gian_ket_thuc->format('d-m-Y H:i:s') : null,
                    'media' => $alert->media->map(function ($m) {
                        return [
                            'id' => $m->id,
                            'loai' => $m->loai_media,
                            'url' => $m->url,
                        ];
                    })
                ];
            });

        // Statistics
        $stats = [
            'total' => $alerts->count(),
            'by_severity' => [
                'low' => $alerts->where('muc_do', 'low')->count(),
                'medium' => $alerts->where('muc_do', 'medium')->count(),
                'high' => $alerts->where('muc_do', 'high')->count(),
                'critical' => $alerts->where('muc_do', 'critical')->count(),
            ],
            'by_type' => [
                'traffic' => $alerts->where('loai_canh_bao', 'traffic')->count(),
                'flood' => $alerts->where('loai_canh_bao', 'flood')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'street' => [
                    'id' => $street->id,
                    'ten' => $street->ten,
                    'loai_duong' => $street->loai_duong,
                    'phuong_xa' => $street->phuongXa ? [
                        'id' => $street->phuongXa->id,
                        'ten' => $street->phuongXa->ten,
                        'thanh_pho' => $street->phuongXa->thanhPho ? [
                            'id' => $street->phuongXa->thanhPho->id,
                            'ten' => $street->phuongXa->thanhPho->ten
                        ] : null
                    ] : null,
                    'khu_vuc' => $street->khuVuc ? [
                        'id' => $street->khuVuc->id,
                        'ten' => $street->khuVuc->ten
                    ] : null,
                    'coordinates' => $street->coordinates
                ],
                'alerts' => $alerts,
                'stats' => $stats
            ]
        ]);
    }
}
