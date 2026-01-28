<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestCamera2Seeder extends Seeder
{
    public function run()
    {
        // Camera 2 cho test với Đường Cao Thắng (ID 26)
        DB::table('camera')->updateOrInsert(
            ['id' => 2],
            [
                'ma_camera' => '',
                'ten_camera' => 'Camera Đường Cao Thắng',
                'duong_id' => 26,
                'stream_url' => 'D:/php/DATT_AI/ai-detection/videos/camera6.mp4',
                'trang_thai_ket_noi' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        echo "✅ Camera  7 created/updated successfully!\n";
        echo "   - ID: 2\n";
        echo "   - Đường: Cao Thắng (ID: 26)\n";
        echo "   - Phường ID: 1\n";
        echo "   - Video: downloaded_video.mp4 (Flooding)\n";
    }
}
