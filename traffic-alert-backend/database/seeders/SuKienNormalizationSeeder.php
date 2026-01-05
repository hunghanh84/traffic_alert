<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoaiSuKien;
use App\Models\TrangThaiSuKien;

class SuKienNormalizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Loai Su Kien (Event Types)
        $loaiSuKien = [
            ['ma' => 'traffic_jam', 'ten' => 'Tắc đường', 'mo_ta' => 'Mật độ giao thông cao, di chuyển chậm'],
            ['ma' => 'flood', 'ten' => 'Ngập lụt', 'mo_ta' => 'Đường bị ngập do mưa hoặc triều cường'],
            ['ma' => 'accident', 'ten' => 'Tai nạn giao thông', 'mo_ta' => 'Sự cố va chạm trên đường'],
            ['ma' => 'road_work', 'ten' => 'Sửa chữa đường', 'mo_ta' => 'Đang có công trình thi công'],
            ['ma' => 'fire', 'ten' => 'Hỏa hoạn', 'mo_ta' => 'Sự cố cháy nổ'],
            ['ma' => 'other', 'ten' => 'Sự cố khác', 'mo_ta' => 'Các loại sự cố không thuộc danh mục trên'],
        ];

        foreach ($loaiSuKien as $item) {
            LoaiSuKien::updateOrCreate(['ma' => $item['ma']], $item);
        }

        // 2. Seed Trang Thai Su Kien (Event Statuses)
        $trangThaiSuKien = [
            ['ma' => 'occuring', 'ten' => 'Đang xảy ra', 'mo_ta' => 'Sự cố đang diễn ra và ảnh hưởng trực tiếp'],
            ['ma' => 'cleared', 'ten' => 'Đã kết thúc', 'mo_ta' => 'Sự cố đã được giải quyết, giao thông bình thường'],
            ['ma' => 'cancelled', 'ten' => 'Bị hủy', 'mo_ta' => 'Tin báo bị hủy hoặc không đúng'],
            ['ma' => 'fake_report', 'ten' => 'Sai báo', 'mo_ta' => 'Thông tin báo cáo không có thật'],
        ];

        foreach ($trangThaiSuKien as $item) {
            TrangThaiSuKien::updateOrCreate(['ma' => $item['ma']], $item);
        }
    }
}
