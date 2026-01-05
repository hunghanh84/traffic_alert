<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThanhPho;
use App\Models\PhuongXa;
use App\Models\KhuVuc;
use App\Models\Duong;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo Thành phố Hồ Chí Minh
        $hcm = ThanhPho::create([
            'ten' => 'Thành phố Hồ Chí Minh',
            'ma' => 'HCM'
        ]);

        // Tạo các phường
        $phuongs = [
            ['ten' => 'Phường 1', 'ma' => 'P01'],
            ['ten' => 'Phường 2', 'ma' => 'P02'],
            ['ten' => 'Phường 3', 'ma' => 'P03'],
            ['ten' => 'Phường 4', 'ma' => 'P04'],
            ['ten' => 'Phường 5', 'ma' => 'P05'],
            ['ten' => 'Phường 6', 'ma' => 'P06'],
            ['ten' => 'Phường 7', 'ma' => 'P07'],
            ['ten' => 'Phường 8', 'ma' => 'P08'],
            ['ten' => 'Phường 9', 'ma' => 'P09'],
            ['ten' => 'Phường 10', 'ma' => 'P10'],
            ['ten' => 'Phường An Khánh', 'ma' => 'PAK'],
            ['ten' => 'Phường An Phú', 'ma' => 'PAP'],
            ['ten' => 'Phường Bình An', 'ma' => 'PBA'],
            ['ten' => 'Phường Bình Trưng Đông', 'ma' => 'PBTD'],
            ['ten' => 'Phường Bình Trưng Tây', 'ma' => 'PBTT'],
            ['ten' => 'Phường Cát Lái', 'ma' => 'PCL'],
            ['ten' => 'Phường Thảo Điền', 'ma' => 'PTD'],
            ['ten' => 'Phường Thủ Thiêm', 'ma' => 'PTT'],
        ];

        $phuongModels = [];
        foreach ($phuongs as $phuong) {
            $phuongModels[] = PhuongXa::create([
                'thanh_pho_id' => $hcm->id,
                'ten' => $phuong['ten'],
                'ma' => $phuong['ma']
            ]);
        }

        // Tạo khu vực cho mỗi phường (1 khu vực mặc định)
        $khuVucModels = [];
        foreach ($phuongModels as $index => $phuong) {
            $khuVucModels[] = KhuVuc::create([
                'phuong_id' => $phuong->id,
                'ten' => 'Khu vực trung tâm ' . $phuong->ten,
                'vi_do' => 10.7769 + ($index * 0.01), // Tọa độ mẫu
                'kinh_do' => 106.7009 + ($index * 0.01)
            ]);
        }

        // Tạo các đường
        $duongs = [
            ['ten' => 'Nguyễn Văn Linh', 'ma' => 'NVL', 'loai_duong' => 'quốc lộ'],
            ['ten' => 'Lê Văn Việt', 'ma' => 'LVV', 'loai_duong' => 'nội đô'],
            ['ten' => 'Võ Văn Ngân', 'ma' => 'VVN', 'loai_duong' => 'nội đô'],
            ['ten' => 'Phạm Văn Đồng', 'ma' => 'PVD', 'loai_duong' => 'quốc lộ'],
            ['ten' => 'Xa lộ Hà Nội', 'ma' => 'XLHN', 'loai_duong' => 'quốc lộ'],
            ['ten' => 'Điện Biên Phủ', 'ma' => 'DBP', 'loai_duong' => 'nội đô'],
            ['ten' => 'Nguyễn Thị Minh Khai', 'ma' => 'NTMK', 'loai_duong' => 'nội đô'],
            ['ten' => 'Cách Mạng Tháng Tám', 'ma' => 'CMTT', 'loai_duong' => 'nội đô'],
            ['ten' => 'Trần Hưng Đạo', 'ma' => 'THD', 'loai_duong' => 'nội đô'],
            ['ten' => 'Lê Lợi', 'ma' => 'LL', 'loai_duong' => 'nội đô'],
            ['ten' => 'Nguyễn Huệ', 'ma' => 'NH', 'loai_duong' => 'nội đô'],
            ['ten' => 'Hai Bà Trưng', 'ma' => 'HBT', 'loai_duong' => 'nội đô'],
            ['ten' => 'Nam Kỳ Khởi Nghĩa', 'ma' => 'NKKN', 'loai_duong' => 'nội đô'],
            ['ten' => 'Tôn Đức Thắng', 'ma' => 'TDT', 'loai_duong' => 'nội đô'],
            ['ten' => 'Võ Thị Sáu', 'ma' => 'VTS', 'loai_duong' => 'nội đô'],
            ['ten' => 'Pasteur', 'ma' => 'PST', 'loai_duong' => 'nội đô'],
            ['ten' => 'Lý Thường Kiệt', 'ma' => 'LTK', 'loai_duong' => 'nội đô'],
            ['ten' => 'Trần Quốc Toản', 'ma' => 'TQT', 'loai_duong' => 'nội đô'],
            ['ten' => 'Nguyễn Đình Chiểu', 'ma' => 'NDC', 'loai_duong' => 'nội đô'],
            ['ten' => 'Hoàng Sa', 'ma' => 'HS', 'loai_duong' => 'nội đô'],
        ];

        // Phân bổ đường cho các khu vực
        foreach ($duongs as $index => $duong) {
            $khuVucIndex = $index % count($khuVucModels);
            Duong::create([
                'khu_vuc_id' => $khuVucModels[$khuVucIndex]->id,
                'ten' => $duong['ten'],
                'ma' => $duong['ma'],
                'loai_duong' => $duong['loai_duong'],
                'kich_hoat' => true
            ]);
        }
    }
}
