<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NguyenVanLinhCameraSeeder extends Seeder
{
    public function run()
    {
        DB::table('camera')->updateOrInsert(
            ['id' => 3],
            [
                'ma_camera' => 'CAM003',
                'ten_camera' => 'Đường Nguyễn Văn Linh',
                'duong_id' => 11,
                'stream_url' => 'https://www.youtube.com/watch?v=WUFEgl1Hyb8',
                'trang_thai_ket_noi' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        echo "✅ Camera created/updated successfully!\n";
        echo "   - ID: 3\n";
        echo "   - Đường ID: 11 (Nguyễn Văn Linh)\n";
        echo "   - YouTube: https://www.youtube.com/watch?v=WUFEgl1Hyb8\n";
    }
}
