<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestCameraSeeder extends Seeder
{
    public function run()
    {
        // Camera 1: Đường Triệu Nữ Vương
        DB::table('camera')->updateOrInsert(
            ['id' => 1],
            [
                'ma_camera' => 'CAM001',
                'ten_camera' => 'Đường Triệu Nữ Vương',
                'duong_id' => 69,
                'stream_url' => 'D:/php/DATT_AI/ai-detection/videos/camera7.mp4',
                'trang_thai_ket_noi' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Camera 2: Đường Phan Châu Trinh
        DB::table('camera')->updateOrInsert(
            ['id' => 2],
            [
                'ma_camera' => 'CAM006',
                'ten_camera' => 'Đường Phan Châu Trinh',
                'duong_id' => 3,
                'stream_url' => 'D:/php/DATT_AI/ai-detection/videos/camera6.mp4',
                'trang_thai_ket_noi' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Camera 3: Đường Nguyễn Thị Minh Khai
        DB::table('camera')->updateOrInsert(
            ['id' => 3],
            [
                'ma_camera' => 'CAM007',
                'ten_camera' => 'Đường Nguyễn Thị Minh Khai',
                'duong_id' => 25,
                'stream_url' => 'D:/php/DATT_AI/ai-detection/videos/camera2.mp4',
                'trang_thai_ket_noi' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Camera 4: Đường Hoàng Diệu
        DB::table('camera')->updateOrInsert(
            ['id' => 4],
            [
                'ma_camera' => 'CAM008',
                'ten_camera' => 'Đường Hoàng Diệu',
                'duong_id' => 27,
                'stream_url' => 'D:/php/DATT_AI/ai-detection/videos/camera7.mp4',
                'trang_thai_ket_noi' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Camera 5: Đường Nguyễn Văn Linh
        DB::table('camera')->updateOrInsert(
            ['id' => 5],
            [
                'ma_camera' => 'CAM009',
                'ten_camera' => 'Đường Nguyễn Văn Linh',
                'duong_id' => 11,
                'stream_url' => 'D:/php/DATT_AI/ai-detection/videos/camera4.mp4',
                'trang_thai_ket_noi' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        echo "✅ Cameras created/updated successfully!\n";
        echo "   - CAM001: Đường Triệu Nữ Vương (ID: 69)\n";
        echo "   - CAM006: Đường Phan Châu Trinh (ID: 3)\n";
        echo "   - CAM007: Đường Nguyễn Thị Minh Khai (ID: 5)\n";
        echo "   - CAM008: Đường Hoàng Diệu (ID: 27)\n";
        echo "   - CAM009: Đường Nguyễn Văn Linh (ID: 11)\n";
    }
}
