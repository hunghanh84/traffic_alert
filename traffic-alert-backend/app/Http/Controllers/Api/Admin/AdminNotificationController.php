<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\SuKienGiaoThong;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Lấy danh sách thông báo
     */
    public function index(Request $request)
    {
        $query = ThongBao::with(['suKien.duong.phuongXa', 'suKien.loaiSuKien']);

        // Filter by status
        if ($request->has('trang_thai_gui')) {
            $query->where('trang_thai_gui', $request->trang_thai_gui);
        }

        // Filter by type
        if ($request->has('loai_thong_bao')) {
            $query->where('loai_thong_bao', $request->loai_thong_bao);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                  ->orWhere('noi_dung', 'like', "%{$search}%");
            });
        }

        $notifications = $query->orderBy('created_at', 'desc')
                              ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Lấy chi tiết thông báo
     */
    public function show($id)
    {
        $notification = ThongBao::with([
            'suKien.duong.phuongXa.thanhPho',
            'suKien.loaiSuKien',
            'suKien.mucDoSuKien',
            'guiThongBaos.nguoiDung'
        ])->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $notification
        ]);
    }

    /**
     * Tạo thông báo mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'su_kien_id' => 'required|exists:su_kien_giao_thong,id',
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'muc_do_uu_tien' => 'required|in:low,medium,high,urgent',
            'loai_thong_bao' => 'required|in:email,sms,push,telegram',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $notification = ThongBao::create([
            'su_kien_id' => $request->su_kien_id,
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'muc_do_uu_tien' => $request->muc_do_uu_tien,
            'loai_thong_bao' => $request->loai_thong_bao,
            'trang_thai_gui' => 'pending',
            'tao_boi' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo thông báo thành công',
            'data' => $notification->load('suKien')
        ], 201);
    }

    /**
     * Cập nhật thông báo
     */
    public function update(Request $request, $id)
    {
        $notification = ThongBao::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tieu_de' => 'sometimes|required|string|max:255',
            'noi_dung' => 'sometimes|required|string',
            'muc_do_uu_tien' => 'sometimes|required|in:low,medium,high,urgent',
            'loai_thong_bao' => 'sometimes|required|in:email,sms,push,telegram',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $notification->update($request->only([
            'tieu_de',
            'noi_dung',
            'muc_do_uu_tien',
            'loai_thong_bao'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông báo thành công',
            'data' => $notification->load('suKien')
        ]);
    }

    /**
     * Gửi thông báo
     */
    public function send($id)
    {
        $notification = ThongBao::with('suKien')->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }

        if ($notification->trang_thai_gui === 'sent') {
            return response()->json([
                'success' => false,
                'message' => 'Thông báo đã được gửi'
            ], 400);
        }

        // Gửi qua Telegram
        if ($notification->loai_thong_bao === 'telegram') {
            $result = $this->telegramService->sendNotificationFromThongBao($id);
            
            if ($result) {
                $notification->update(['trang_thai_gui' => 'sent']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Gửi thông báo thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gửi thông báo thất bại'
                ], 500);
            }
        }

        // TODO: Implement other notification types (email, sms, push)
        
        return response()->json([
            'success' => false,
            'message' => 'Loại thông báo chưa được hỗ trợ'
        ], 400);
    }

    /**
     * Xóa thông báo
     */
    public function destroy($id)
    {
        $notification = ThongBao::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo'
            ], 404);
        }

        if ($notification->trang_thai_gui === 'sent') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa thông báo đã gửi'
            ], 400);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa thông báo thành công'
        ]);
    }

    /**
     * Lấy danh sách sự kiện để tạo thông báo
     */
    public function getEvents()
    {
        $events = SuKienGiaoThong::with([
            'duong.phuongXa.thanhPho',
            'loaiSuKien',
            'mucDoSuKien'
        ])
        ->where('trang_thai_id', '!=', 3) // Không lấy sự kiện đã kết thúc
        ->orderBy('bat_dau_luc', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }
}
