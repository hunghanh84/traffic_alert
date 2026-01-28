<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CameraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cameras = [
            [
                'ma_camera' => 'CAM001',
                'ten_camera' => 'Nút giao thông phía tây cầu',
                'duong_id' => 10,
                'stream_url' => 'https://www.youtube.com/watch?v=WUFEgl1Hyb8',
                'trang_thai_ket_noi' => 'active',
            ],
            [
                'ma_camera' => 'CAM002',
                'ten_camera' => 'Trường Tiểu học Lý Tự Trọng',
                'duong_id' => 5,
                'stream_url' => 'https://www.youtube.com/watch?v=sECNGJvGpwA',
                'trang_thai_ket_noi' => 'active',
            ],
            [
                'ma_camera' => 'CAM003',
                'ten_camera' => 'Khách sạn Hải Triều',
                'duong_id' => 30,
                'stream_url' => 'https://www.youtube.com/watch?v=oNbdqkEozX0',
                'trang_thai_ket_noi' => 'active',
            ],
            [
                'ma_camera' => 'CAM004',
                'ten_camera' => 'Cổng sau Bệnh viện C',
                'duong_id' => 12,
                'stream_url' => 'https://www.youtube.com/watch?v=muijHPW82vI',
                'trang_thai_ket_noi' => 'active',
            ],
            [
                'ma_camera' => 'CAM005',
                'ten_camera' => 'Trường Nguyễn Huệ Đà Nẵng',
                'duong_id' => 8,
                'stream_url' => 'https://www.youtube.com/watch?v=xCNRP131kNY',
                'trang_thai_ket_noi' => 'active',
            ],
        ];

        foreach ($cameras as $camera) {
            DB::table('camera')->insert([
                'ma_camera' => $camera['ma_camera'],
                'ten_camera' => $camera['ten_camera'],
                'duong_id' => $camera['duong_id'],
                'stream_url' => $camera['stream_url'],
                'trang_thai_ket_noi' => $camera['trang_thai_ket_noi'],
                'so_lan_kiem_tra' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Imported 5 cameras successfully!');
    }
}
