<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Duong;

class HaiChauStreetCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dữ liệu thực tế từ Phường Hải Châu
        // Tạo polyline dọc theo đường (không chéo)
        
        $streetData = [
            'Yên Bái' => [16.0671992, 108.222593],
            'Kiệt 144 Hải Phòng' => [16.0714025, 108.2140979],
            'Nguyễn Thị Minh Khai' => [16.0756889, 108.2178047],
            'Cao Thắng' => [16.0765599, 108.2136369],
            'Bạch Đằng' => [16.0693987, 108.2250357],
            'Hoàng Diệu' => [16.056263, 108.217376],
            'Quang Trung' => [16.0755804, 108.2238862],
            'Ông Ích Khiêm' => [16.0631458, 108.2157225],
            'Thái Phiên' => [16.0649019, 108.2238248],
            '2 Tháng 9' => [16.0572265, 108.2229394],
            'Nguyễn Chí Thanh' => [16.0704961, 108.2211611],
            'Phan Đình Phùng' => [16.0704164, 108.2222811],
            'Phan Châu Trinh' => [16.0695966, 108.2204265],
        ];

        $updated = 0;
        $notFound = [];

        foreach ($streetData as $streetName => $centerPoint) {
            // Tìm đường trong database
            $duong = Duong::where('ten', 'LIKE', "%{$streetName}%")->first();
            
            if ($duong) {
                $lat = $centerPoint[0];
                $lng = $centerPoint[1];
                
                // Tạo polyline dọc theo đường (chỉ thay đổi latitude hoặc longitude)
                // Giả sử đường chạy theo hướng Đông-Tây (thay đổi longitude)
                $coordinates = [
                    [$lat, $lng - 0.001],      // Điểm Tây
                    [$lat, $lng - 0.0005],     // 1/4
                    [$lat, $lng],              // Giữa
                    [$lat, $lng + 0.0005],     // 3/4
                    [$lat, $lng + 0.001],      // Điểm Đông
                ];
                
                $duong->coordinates = $coordinates;
                $duong->save();
                $updated++;
                $this->command->info("✓ Đã cập nhật tọa độ cho: {$streetName} (ID: {$duong->id})");
            } else {
                $notFound[] = $streetName;
                $this->command->warn("✗ Không tìm thấy đường: {$streetName}");
            }
        }

        $this->command->info("\n=== Kết quả ===");
        $this->command->info("✓ Đã cập nhật: {$updated} đường");
        
        if (count($notFound) > 0) {
            $this->command->warn("✗ Không tìm thấy: " . count($notFound) . " đường");
            $this->command->warn("Danh sách: " . implode(', ', $notFound));
            $this->command->info("\nGợi ý: Kiểm tra tên đường trong database có khớp không");
        }
        
        // Hiển thị tổng số đường có tọa độ
        $totalWithCoords = Duong::whereNotNull('coordinates')->count();
        $this->command->info("\nTổng số đường có tọa độ trong hệ thống: {$totalWithCoords}");
    }
}
