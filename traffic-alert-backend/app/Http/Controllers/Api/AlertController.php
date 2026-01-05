<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiDang;
use App\Models\Media;
use App\Models\KetQuaAI;
use App\Models\MucDoSuKien;
use App\Models\ThietLapCanhBao;
use App\Models\SuKienGiaoThong;
use App\Models\LoaiSuKien;
use App\Models\TrangThaiSuKien;
use App\Models\Duong;
use App\Services\TelegramService;
use App\Jobs\UpdateAlertStatusJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AlertController extends Controller
{
    /**
     * Get all alerts with pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = BaiDang::with(['khuVuc.phuongXa.thanhPho', 'duong', 'media', 'nguoiDung', 'phuongXa.thanhPho', 'mucDoSuKien']);

        // Filter for current user's alerts if requested
        if ($request->has('mine') && Auth::guard('sanctum')->check()) {
            $query->where('nguoi_dung_id', Auth::guard('sanctum')->id());
        }

        $alerts = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $alerts->map(function ($alert) {
                $duongTen = $alert->duong->ten ?? '';
                $phuongTen = $alert->phuongXa->ten ?? ($alert->khuVuc->phuongXa->ten ?? '');
                $thanhPhoTen = $alert->phuongXa?->thanhPho?->ten ?? ($alert->khuVuc?->phuongXa?->thanhPho?->ten ?? 'Đà Nẵng');
                
                return [
                    'id' => $alert->id,
                    'loai_canh_bao' => $alert->loai_canh_bao,
                    'muc_do' => $alert->mucDoSuKien ? $alert->mucDoSuKien->ma : null,
                    'muc_do_ten' => $alert->mucDoSuKien ? $alert->mucDoSuKien->ten : null,
                    'mo_ta' => $alert->mo_ta,
                    'trang_thai' => $alert->trang_thai,
                    'duong' => $duongTen,
                    'phuong' => $phuongTen,
                    'thanh_pho' => $thanhPhoTen,
                    'dia_chi' => "Đường {$duongTen}, Phường {$phuongTen}, {$thanhPhoTen}",
                    'nguoi_dung' => $alert->nguoiDung ? [
                        'id' => $alert->nguoiDung->id,
                        'ten' => $alert->nguoiDung->ten,
                        'ho_ten' => $alert->nguoiDung->ho_ten,
                        'anh_dai_dien' => $alert->nguoiDung->anh_dai_dien,
                    ] : null,
                    'media' => $alert->media->map(function ($m) {
                        return [
                            'id' => $m->id,
                            'loai' => $m->loai_media,
                            'url' => $m->url,
                            'dinh_dang' => $m->dinh_dang,
                        ];
                    }),
                    'created_at' => $alert->created_at->format('d-m-Y H:i:s'),
                    'updated_at' => $alert->updated_at->format('d-m-Y H:i:s'),
                ];
            }),
            'pagination' => [
                'total' => $alerts->total(),
                'per_page' => $alerts->perPage(),
                'current_page' => $alerts->currentPage(),
                'last_page' => $alerts->lastPage(),
            ]
        ]);
    }

    /**
     * Get approved alerts for map visualization
     * Only returns alerts created within the last 5 minutes (auto-expire after 5 minutes)
     */
    public function getApprovedAlertsForMap(Request $request)
    {
        // Get active alerts from thiet_lap_canh_bao table
        $activeAlerts = ThietLapCanhBao::with(['baiDang.media', 'baiDang.mucDoSuKien', 'duong.phuongXa.thanhPho'])
            ->where('trang_thai', 'active')
            ->where('kich_hoat', true)
            ->where('thoi_gian_ket_thuc', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activeAlerts->map(function ($active) {
                $alert = $active->baiDang;
                $duong = $active->duong;
                
                if (!$alert || !$duong) return null;

                $phuongTen = $duong->phuongXa->ten ?? '';
                $thanhPhoTen = $duong->phuongXa?->thanhPho?->ten ?? 'Đà Nẵng';
                $duongTen = $duong->ten;
                
                $diaChi = $phuongTen ? "{$phuongTen}, {$thanhPhoTen}" : $thanhPhoTen;

                return [
                    'id' => $alert->id,
                    'active_id' => $active->id,
                    'loai_canh_bao' => $active->loai_canh_bao,
                    'muc_do' => $alert->mucDoSuKien ? $alert->mucDoSuKien->ma : null,
                    'muc_do_ten' => $alert->mucDoSuKien ? $alert->mucDoSuKien->ten : null,
                    'mo_ta' => $alert->mo_ta,
                    'duong' => [
                        'id' => $duong->id,
                        'ten' => $duongTen,
                        'coordinates' => $duong->coordinates ?? null,
                    ],
                    'phuong_ten' => $phuongTen,
                    'thanh_pho_ten' => $thanhPhoTen,
                    'dia_chi' => $diaChi,
                    'media' => $alert->media->map(function ($m) {
                        return [
                            'id' => $m->id,
                            'url' => $m->url,
                        ];
                    }),
                    'created_at' => $active->created_at->format('d-m-Y H:i:s'),
                    'expires_at' => $active->thoi_gian_ket_thuc->format('d-m-Y H:i:s'),
                ];
            })->filter()
        ]);
    }

    /**
     * Store a new alert
     */
    public function store(Request $request)
    {
        $request->validate([
            'loai_canh_bao' => 'required|in:traffic,flood',
            'muc_do' => 'required|in:low,medium,high,critical',
            'duong_id' => 'required|exists:duong,id',
            'mo_ta' => 'nullable|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ]);

        DB::beginTransaction();
        try {
            // Find muc_do_id from slug
            $mucDo = MucDoSuKien::where('ma', $request->muc_do)->first();
            
            // Create bai_dang (default: cho_duyet)
            // khu_vuc_id and phuong_xa_id will be determined automatically through duong relationship
            $baiDang = BaiDang::create([
                'nguoi_dung_id' => auth()->id() ?? null,
                'duong_id' => $request->duong_id,
                'loai_canh_bao' => $request->loai_canh_bao,
                'muc_do_id' => $mucDo ? $mucDo->id : null,
                'mo_ta' => $request->mo_ta,
            ]);

            $uploadedImagePath = null;
            
            // Upload and save media
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('alerts', 'public');
                    
                    $media = Media::create([
                        'bai_dang_id' => $baiDang->id,
                        'loai_media' => 'image',
                        'duong_dan' => $path,
                        'kich_thuoc_byte' => $image->getSize(),
                        'dinh_dang' => $image->getClientOriginalExtension(),
                    ]);
                    
                    // Run AI detection on each image
                    $imagePath = storage_path('app/public/' . $path);
                    if (file_exists($imagePath)) {
                        try {
                            $detectionService = app(\App\Services\DetectionService::class);
                            $detectionResult = $detectionService->detectImage($imagePath);
                            
                            if ($detectionResult) {
                                // Save AI result to ket_qua_ai table
                                KetQuaAI::create([
                                    'media_id' => $media->id,
                                    'nhan' => $detectionResult['prediction'], // 'traffic', 'flood', 'normal'
                                    'do_tin_cay' => $detectionResult['confidence'],
                                    'raw_json' => $detectionResult, // Store full AI response
                                    'da_xac_minh' => false,
                                ]);
                                
                                // Use first image's result for auto-approval logic
                                if ($uploadedImagePath === null) {
                                    $uploadedImagePath = $imagePath;
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error("AI detection failed for media #{$media->id}: " . $e->getMessage());
                        }
                    }
                }
            }

            // SYNCHRONOUS AI Detection - Chạy đồng bộ, chờ kết quả trước khi response
            $aiDetectionResult = null;
            if ($uploadedImagePath && file_exists($uploadedImagePath)) {
                try {
                    $detectionService = app(\App\Services\DetectionService::class);
                    $detectionResult = $detectionService->detectImage($uploadedImagePath);
                    
                    if ($detectionResult) {
                        $aiPrediction = $detectionResult['prediction']; // 'traffic' | 'flood' | 'normal'
                        $aiConfidence = $detectionResult['confidence'];
                        
                        $aiDetectionResult = [
                            'prediction' => $aiPrediction,
                            'confidence' => $aiConfidence,
                            'probabilities' => $detectionResult['probabilities'] ?? null,
                            'auto_approved' => false,
                        ];
                        
                        // SIMPLE AUTO-APPROVAL LOGIC:
                        // Confidence > 80% AND AI prediction matches user input → Auto-approve
                        if ($aiConfidence > 0.8 && $aiPrediction === $request->loai_canh_bao) {
                            $baiDang->trang_thai = 'da_duyet';
                            $baiDang->save();
                            $aiDetectionResult['auto_approved'] = true;
                            
                            // Create Active Alert in thiet_lap_canh_bao
                            $activeAlert = ThietLapCanhBao::create([
                                'bai_dang_id' => $baiDang->id,
                                'duong_id' => $baiDang->duong_id,
                                'loai_canh_bao' => $baiDang->loai_canh_bao,
                                'muc_do_toi_thieu_id' => $baiDang->muc_do_id,
                                'thoi_gian_bat_dau' => now(),
                                'thoi_gian_ket_thuc' => now()->addMinutes(30),
                                'kich_hoat' => true,
                                'trang_thai' => 'active',
                            ]);

                            // Dispatch job to expire alert after 30 mins
                            UpdateAlertStatusJob::dispatch($activeAlert->id)->delay(now()->addMinutes(30));
                            
                            Log::info("✅ Auto-approved alert #{$baiDang->id} and created Active Alert #{$activeAlert->id}", [
                                'ai_prediction' => $aiPrediction,
                                'ai_confidence' => $aiConfidence,
                                'user_type' => $request->loai_canh_bao
                            ]);
                        } 
                        // All other cases: Pending for admin review
                        else {
                            Log::info("⏳ Alert #{$baiDang->id} pending admin review", [
                                'ai_prediction' => $aiPrediction,
                                'ai_confidence' => $aiConfidence,
                                'user_type' => $request->loai_canh_bao,
                                'reason' => $aiConfidence <= 0.8 ? 'Low confidence' : 'Type mismatch'
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Detection failed, keep status as cho_duyet
                    Log::warning("AI detection failed for alert #{$baiDang->id}: " . $e->getMessage());
                }
            }

            DB::commit();

            // Check and create traffic event if 3+ reports
            try {
                $this->checkAndCreateTrafficEvent($baiDang->duong_id, $baiDang->loai_canh_bao);
            } catch (\Exception $e) {
                Log::error("Error in checkAndCreateTrafficEvent: " . $e->getMessage());
            }

            // Response với thông tin AI detection
            $responseData = [
                'success' => true,
                'message' => $baiDang->trang_thai === 'da_duyet' 
                    ? 'Tạo cảnh báo thành công và đã được tự động duyệt bởi AI' 
                    : 'Tạo cảnh báo thành công, đang chờ duyệt',
                'data' => $baiDang->load(['khuVuc.phuongXa', 'duong', 'media'])
            ];

            // Thêm thông tin AI detection nếu có
            if ($aiDetectionResult) {
                $responseData['ai_detection'] = $aiDetectionResult;
            }

            return response()->json($responseData, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Get a single alert
 */
public function show($id)
{
    $alert = BaiDang::with(['khuVuc.phuongXa.thanhPho', 'duong.phuongXa.thanhPho', 'media', 'nguoiDung', 'phuongXa.thanhPho', 'mucDoSuKien'])
        ->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $alert->id,
            'loai_canh_bao' => $alert->loai_canh_bao,
            'muc_do' => $alert->mucDoSuKien ? $alert->mucDoSuKien->ma : null,
            'muc_do_su_kien' => $alert->mucDoSuKien ? [
                'ma' => $alert->mucDoSuKien->ma,
                'ten' => $alert->mucDoSuKien->ten,
            ] : null,
            'mo_ta' => $alert->mo_ta,
            'trang_thai' => $alert->trang_thai,
            'duong' => $alert->duong ? [
                'id' => $alert->duong->id,
                'ten' => $alert->duong->ten,
                'phuong_xa' => $alert->duong->phuongXa ? [
                    'id' => $alert->duong->phuongXa->id,
                    'ten' => $alert->duong->phuongXa->ten,
                ] : null,
            ] : null,
            'khu_vuc' => $alert->khuVuc,
            'phuong_xa' => $alert->phuongXa ? [
                'id' => $alert->phuongXa->id,
                'ten' => $alert->phuongXa->ten,
                'thanh_pho' => $alert->phuongXa->thanhPho,
            ] : null,
            'thanh_pho' => $alert->phuongXa?->thanhPho,
            'nguoi_dung' => $alert->nguoiDung ? [
                'id' => $alert->nguoiDung->id,
                'ten_dang_nhap' => $alert->nguoiDung->ten_dang_nhap,
                'email' => $alert->nguoiDung->email,
                'so_dien_thoai' => $alert->nguoiDung->so_dien_thoai,
            ] : null,
            'media' => $alert->media->map(function ($m) {
                return [
                    'id' => $m->id,
                    'loai' => $m->loai_media,
                    'duong_dan' => $m->url,
                    'ten_file' => $m->dinh_dang,
                ];
            }),
            'created_at' => $alert->created_at->toISOString(),
            'updated_at' => $alert->updated_at->toISOString(),
        ]
    ]);
}
    /**
     * Update an alert
     */
    public function update(Request $request, $id)
    {
        $alert = BaiDang::findOrFail($id);
        
        // Only allow update if status is 'cho_duyet'
        if ($alert->trang_thai !== 'cho_duyet') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể chỉnh sửa cảnh báo đang chờ duyệt'
            ], 403);
        }

        $request->validate([
            'loai_canh_bao' => 'required|in:traffic,flood',
            'muc_do' => 'required|in:low,medium,high,critical',
            'duong_id' => 'required|exists:duong,id',
            'mo_ta' => 'nullable|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ]);

        DB::beginTransaction();
        try {
            // Find muc_do_id from slug
            $mucDo = MucDoSuKien::where('ma', $request->muc_do)->first();

            // Update bai_dang
            // khu_vuc_id will be determined automatically through duong relationship
            $alert->update([
                'duong_id' => $request->duong_id,
                'loai_canh_bao' => $request->loai_canh_bao,
                'muc_do_id' => $mucDo ? $mucDo->id : null,
                'mo_ta' => $request->mo_ta,
            ]);

            // Handle new images if provided
            if ($request->hasFile('images')) {
                // Delete old images
                foreach ($alert->media as $media) {
                    Storage::disk('public')->delete($media->duong_dan);
                    $media->delete();
                }

                // Upload new images
                foreach ($request->file('images') as $image) {
                    $path = $image->store('alerts', 'public');
                    
                    Media::create([
                        'bai_dang_id' => $alert->id,
                        'loai_media' => 'image',
                        'duong_dan' => $path,
                        'kich_thuoc_byte' => $image->getSize(),
                        'dinh_dang' => $image->getClientOriginalExtension(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật cảnh báo thành công',
                'data' => $alert->load(['khuVuc.phuongXa', 'duong', 'media'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an alert (Soft Delete)
     */
    public function destroy($id)
    {
        $alert = BaiDang::findOrFail($id);
        
        // Soft delete - không xóa media files để có thể khôi phục
        $alert->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa cảnh báo thành công'
        ]);
    }

    /**
     * Approve an alert (Duyệt)
     */
    public function approve($id)
    {
        $alert = BaiDang::findOrFail($id);
        $alert->trang_thai = 'da_duyet';
        $alert->save();

        // Create Active Alert in thiet_lap_canh_bao
        $activeAlert = ThietLapCanhBao::create([
            'bai_dang_id' => $alert->id,
            'duong_id' => $alert->duong_id,
            'loai_canh_bao' => $alert->loai_canh_bao,
            'muc_do_toi_thieu_id' => $alert->muc_do_id,
            'thoi_gian_bat_dau' => now(),
            'thoi_gian_ket_thuc' => now()->addMinutes(30),
            'kich_hoat' => true,
            'trang_thai' => 'active',
        ]);

        // Dispatch job to expire alert after 30 mins
        UpdateAlertStatusJob::dispatch($activeAlert->id)->delay(now()->addMinutes(30));

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt cảnh báo và kích hoạt hiển thị trong 30 phút',
            'data' => $alert->load(['khuVuc.phuongXa', 'duong', 'media']),
            'active_alert_id' => $activeAlert->id
        ]);
    }

    /**
     * Reject an alert (Từ chối)
     */
    public function reject($id)
    {
        $alert = BaiDang::findOrFail($id);
        $alert->trang_thai = 'tu_choi';
        $alert->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối cảnh báo',
            'data' => $alert->load(['khuVuc.phuongXa', 'duong', 'media'])
        ]);
    }

    /**
     * Restore a soft-deleted alert (Khôi phục)
     */
    public function restore($id)
    {
        $alert = BaiDang::withTrashed()->findOrFail($id);
        
        if (!$alert->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cảnh báo này chưa bị xóa'
            ], 400);
        }

        $alert->restore();

        return response()->json([
            'success' => true,
            'message' => 'Khôi phục cảnh báo thành công',
            'data' => $alert->load(['khuVuc.phuongXa', 'duong', 'media'])
        ]);
    }

    /**
     * Update street coordinates
     */
    public function updateStreetCoordinates(Request $request, $id)
    {
        $duong = \App\Models\Duong::find($id);
        if (!$duong) {
            return response()->json(['success' => false, 'message' => 'Street not found'], 404);
        }

        // Validate coordinates format if needed, for now accept array
        $coordinates = $request->input('coordinates');
        
        $duong->coordinates = $coordinates;
        $duong->save();

        return response()->json([
            'success' => true, 
            'message' => 'Updated coordinates successfully',
            'data' => $duong
        ]);
    }
    /**
     * Check and create a formal traffic event if 3+ active alert settings exist for the same street/type
     */
    private function checkAndCreateTrafficEvent($duongId, $type)
    {
        $loaiMa = ($type === 'traffic') ? 'traffic_jam' : $type;
        $now = Carbon::now();

        // 1. Count active alert settings for this street and type
        // We look for alerts that are 'active', 'kich_hoat' = true, and haven't expired
        $activeAlertsCount = ThietLapCanhBao::whereHas('baiDang', function($q) use ($duongId, $type) {
                $q->where('duong_id', $duongId)
                  ->where('loai_canh_bao', $type);
            })
            ->where('trang_thai', 'active')
            ->where('kich_hoat', true)
            ->where('thoi_gian_ket_thuc', '>', $now)
            ->count();

        Log::info("Checking consensus for street #{$duongId} type {$type}. Active alerts: {$activeAlertsCount}");

        if ($activeAlertsCount >= 3) {
            // 2. Check if an active event already exists
            $loaiSuKien = LoaiSuKien::where('ma', $loaiMa)->first();
            $trangThaiDangXayRa = TrangThaiSuKien::where('ma', 'occuring')->first();

            if ($loaiSuKien && $trangThaiDangXayRa) {
                $existingEvent = SuKienGiaoThong::where('duong_id', $duongId)
                    ->where('loai_su_kien_id', $loaiSuKien->id)
                    ->where('trang_thai_id', $trangThaiDangXayRa->id)
                    ->first();

                if (!$existingEvent) {
                    $duong = Duong::find($duongId);
                    
                    // 3. Create new official traffic event
                    $suKien = SuKienGiaoThong::create([
                        'duong_id' => $duongId,
                        'khu_vuc_id' => $duong ? $duong->khu_vuc_id : null,
                        'loai_su_kien_id' => $loaiSuKien->id,
                        'trang_thai_id' => $trangThaiDangXayRa->id,
                        'nguon' => 'he_thong',
                        'bat_dau_luc' => $now,
                        'mo_ta' => "Sự kiện được xác nhận tự động bởi hệ thống dựa trên sự đồng thuận của 3+ báo cáo từ cộng đồng.",
                    ]);
                    
                    Log::info("Official Traffic Event created for street #{$duongId} type {$loaiMa}");
                    
                    // 4. Create notification record
                    $duongTen = $duong ? $duong->ten : "Đường #{$duongId}";
                    $phuongTen = $duong && $duong->phuongXa ? $duong->phuongXa->ten : '';
                    
                    $tieuDe = "🚨 Cảnh báo: {$loaiSuKien->ten} - Đường {$duongTen}";
                    $noiDung = "Sự kiện {$loaiSuKien->ten} đang xảy ra tại Đường {$duongTen}";
                    if ($phuongTen) {
                        $noiDung .= ", Phường {$phuongTen}";
                    }
                    $noiDung .= ". Thông tin được xác nhận bởi hệ thống dựa trên báo cáo từ cộng đồng.";
                    
                    $thongBao = \App\Models\ThongBao::create([
                        'su_kien_id' => $suKien->id,
                        'tieu_de' => $tieuDe,
                        'noi_dung' => $noiDung,
                        'muc_do_uu_tien' => 'high',
                        'trang_thai_gui' => 'pending',
                        'loai_thong_bao' => 'traffic_event',
                        'tao_boi' => 'he_thong',
                    ]);
                    
                    Log::info("Notification record created #{$thongBao->id} for event #{$suKien->id}");
                    
                    // 5. Send Telegram notification
                    try {
                        $telegramService = new TelegramService();
                        $sendResult = $telegramService->sendNotificationFromThongBao($thongBao->id);
                        
                        if ($sendResult) {
                            $thongBao->update(['trang_thai_gui' => 'sent']);
                            Log::info("Telegram notification sent successfully for ThongBao #{$thongBao->id}");
                        } else {
                            $thongBao->update(['trang_thai_gui' => 'failed']);
                            Log::warning("Failed to send Telegram notification for ThongBao #{$thongBao->id}");
                        }
                    } catch (\Exception $e) {
                        $thongBao->update(['trang_thai_gui' => 'failed']);
                        Log::error("Telegram notification error: " . $e->getMessage());
                    }
                }
            }
        }
    }
}
