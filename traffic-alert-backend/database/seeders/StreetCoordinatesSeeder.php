<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Duong;

class StreetCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tọa độ mẫu cho các đường ở Đà Nẵng
        // Format: [latitude, longitude]
        
        $streetCoordinates = [
            // Nguyễn Văn Linh (đường chính chạy dọc biển)
            'Nguyễn Văn Linh' => [
                [16.0544, 108.2022],
                [16.0545, 108.2025],
                [16.0547, 108.2028],
                [16.0550, 108.2032],
                [16.0553, 108.2036],
                [16.0556, 108.2040],
                [16.0559, 108.2044],
                [16.0562, 108.2048],
            ],
            
            // Lê Duẩn
            'Lê Duẩn' => [
                [16.0470, 108.2200],
                [16.0472, 108.2205],
                [16.0475, 108.2210],
                [16.0478, 108.2215],
                [16.0481, 108.2220],
            ],
            
            // Trần Phú
            'Trần Phú' => [
                [16.0600, 108.2100],
                [16.0605, 108.2105],
                [16.0610, 108.2110],
                [16.0615, 108.2115],
                [16.0620, 108.2120],
            ],
            
            // Ngô Quyền
            'Ngô Quyền' => [
                [16.0520, 108.2180],
                [16.0523, 108.2185],
                [16.0526, 108.2190],
                [16.0529, 108.2195],
            ],
            
            // Hùng Vương
            'Hùng Vương' => [
                [16.0490, 108.2150],
                [16.0493, 108.2155],
                [16.0496, 108.2160],
                [16.0499, 108.2165],
            ],
            
            // Phan Châu Trinh
            'Phan Châu Trinh' => [
                [16.0450, 108.2220],
                [16.0453, 108.2225],
                [16.0456, 108.2230],
                [16.0459, 108.2235],
            ],
            
            // Bạch Đằng
            'Bạch Đằng' => [
                [16.0580, 108.2250],
                [16.0583, 108.2255],
                [16.0586, 108.2260],
                [16.0589, 108.2265],
            ],
            
            // Điện Biên Phủ
            'Điện Biên Phủ' => [
                [16.0510, 108.2130],
                [16.0513, 108.2135],
                [16.0516, 108.2140],
                [16.0519, 108.2145],
            ],
        ];

        $updated = 0;
        $notFound = [];

        foreach ($streetCoordinates as $streetName => $coordinates) {
            $duong = Duong::where('ten', 'LIKE', "%{$streetName}%")->first();
            
            if ($duong) {
                $duong->coordinates = $coordinates;
                $duong->save();
                $updated++;
                $this->command->info("✓ Đã cập nhật tọa độ cho: {$streetName}");
            } else {
                $notFound[] = $streetName;
                $this->command->warn("✗ Không tìm thấy đường: {$streetName}");
            }
        }

        $this->command->info("\n=== Kết quả ===");
        $this->command->info("Đã cập nhật: {$updated} đường");
        
        if (count($notFound) > 0) {
            $this->command->warn("Không tìm thấy: " . count($notFound) . " đường");
            $this->command->warn("Danh sách: " . implode(', ', $notFound));
        }
    }
}
