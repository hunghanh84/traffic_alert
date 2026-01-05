<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BaiDang;
use App\Models\Duong;

class TestApprovedAlertsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các đường đã có tọa độ
        $duongWithCoords = Duong::whereNotNull('coordinates')->get();
        
        if ($duongWithCoords->isEmpty()) {
            $this->command->error('Không có đường nào có tọa độ! Chạy HaiChauStreetCoordinatesSeeder trước.');
            return;
        }

        $this->command->info("Tìm thấy {$duongWithCoords->count()} đường có tọa độ");
        
        // Tạo 5 cảnh báo mẫu đã được duyệt
        $alertTypes = ['traffic', 'flood'];
        $severities = ['low', 'medium', 'high', 'critical'];
        $descriptions = [
            'traffic' => [
                'Tắc đường nghiêm trọng do tai nạn giao thông',
                'Đường đông xe, di chuyển chậm',
                'Ùn tắc kéo dài hơn 30 phút',
                'Nhiều phương tiện, nên tránh',
            ],
            'flood' => [
                'Ngập sâu 20-30cm, khó di chuyển',
                'Đường bị ngập do mưa lớn',
                'Nước dâng cao, cần cẩn thận',
                'Ngập cục bộ một số đoạn',
            ],
        ];

        $created = 0;
        
        // Tạo 5 cảnh báo cho các đường khác nhau
        foreach ($duongWithCoords->take(5) as $index => $duong) {
            $type = $alertTypes[array_rand($alertTypes)];
            $severity = $severities[array_rand($severities)];
            $desc = $descriptions[$type][array_rand($descriptions[$type])];
            
            $alert = BaiDang::create([
                'nguoi_dung_id' => 1, // Giả sử user ID 1 tồn tại
                'duong_id' => $duong->id,
                'loai_canh_bao' => $type,
                'muc_do' => $severity,
                'mo_ta' => $desc,
                'trang_thai' => 'da_duyet', // Đã được duyệt
            ]);
            
            $created++;
            $this->command->info("✓ Tạo cảnh báo #{$alert->id}: {$type} - {$severity} tại {$duong->ten}");
        }

        $this->command->info("\n=== Kết quả ===");
        $this->command->info("✓ Đã tạo {$created} cảnh báo mẫu đã duyệt");
        
        $total = BaiDang::where('trang_thai', 'da_duyet')
            ->whereHas('duong', function($q) {
                $q->whereNotNull('coordinates');
            })
            ->count();
            
        $this->command->info("Tổng số cảnh báo đã duyệt có tọa độ: {$total}");
        $this->command->info("\nBạn có thể xem trên bản đồ tại: http://localhost:5173/map");
    }
}
