<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminCameraController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('camera')
            ->leftJoin('duong', 'camera.duong_id', '=', 'duong.id')
            ->select('camera.*', 'duong.ten as ten_duong');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('camera.ten_camera', 'like', "%{$search}%")
                  ->orWhere('camera.ma_camera', 'like', "%{$search}%");
            });
        }

        if ($request->has('trang_thai')) {
            $query->where('camera.trang_thai_ket_noi', $request->trang_thai);
        }

        $cameras = $query->orderBy('camera.created_at', 'desc')
                        ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $cameras
        ]);
    }

    public function show($id)
    {
        $camera = DB::table('camera')
            ->leftJoin('duong', 'camera.duong_id', '=', 'duong.id')
            ->leftJoin('phuong_xa', 'duong.phuong_id', '=', 'phuong_xa.id')
            ->select('camera.*', 'duong.ten as ten_duong', 'phuong_xa.ten as ten_phuong')
            ->where('camera.id', $id)
            ->first();

        if (!$camera) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy camera'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $camera
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ma_camera' => 'required|unique:camera,ma_camera',
            'ten_camera' => 'required|string|max:255',
            'duong_id' => 'required|exists:duong,id',
            'stream_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $id = DB::table('camera')->insertGetId([
            'ma_camera' => $request->ma_camera,
            'ten_camera' => $request->ten_camera,
            'duong_id' => $request->duong_id,
            'stream_url' => $request->stream_url,
            'trang_thai_ket_noi' => 'active',
            'so_lan_kiem_tra' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo camera thành công',
            'data' => DB::table('camera')->find($id)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $camera = DB::table('camera')->find($id);
        
        if (!$camera) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy camera'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ma_camera' => 'sometimes|unique:camera,ma_camera,' . $id,
            'ten_camera' => 'sometimes|string|max:255',
            'duong_id' => 'sometimes|exists:duong,id',
            'stream_url' => 'sometimes|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('camera')->where('id', $id)->update([
            'ma_camera' => $request->ma_camera ?? $camera->ma_camera,
            'ten_camera' => $request->ten_camera ?? $camera->ten_camera,
            'duong_id' => $request->duong_id ?? $camera->duong_id,
            'stream_url' => $request->stream_url ?? $camera->stream_url,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật camera thành công',
            'data' => DB::table('camera')->find($id)
        ]);
    }

    public function destroy($id)
    {
        $camera = DB::table('camera')->find($id);
        
        if (!$camera) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy camera'
            ], 404);
        }

        DB::table('camera')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa camera thành công'
        ]);
    }

    public function toggleStatus($id)
    {
        $camera = DB::table('camera')->find($id);
        
        if (!$camera) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy camera'
            ], 404);
        }

        $newStatus = $camera->trang_thai_ket_noi === 'active' ? 'inactive' : 'active';
        
        DB::table('camera')->where('id', $id)->update([
            'trang_thai_ket_noi' => $newStatus,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => DB::table('camera')->find($id)
        ]);
    }
}
