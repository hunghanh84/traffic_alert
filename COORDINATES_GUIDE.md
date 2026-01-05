# Hướng dẫn thêm tọa độ cho đường (Streets Coordinates)

## 📍 Cách lấy tọa độ từ OpenStreetMap

### Bước 1: Truy cập OpenStreetMap

1. Mở https://www.openstreetmap.org
2. Tìm kiếm đường cần lấy tọa độ (ví dụ: "Nguyễn Văn Linh, Đà Nẵng")

### Bước 2: Xuất dữ liệu

1. Click vào đường trên bản đồ
2. Click "Export" hoặc sử dụng Overpass API
3. Lấy danh sách tọa độ [lat, lng]

### Bước 3: Cập nhật database

#### Cách 1: Sử dụng Tinker (Khuyến nghị cho testing)

```bash
php artisan tinker
```

```php
// Ví dụ: Cập nhật tọa độ cho đường Nguyễn Văn Linh
$duong = App\Models\Duong::where('ten', 'Nguyễn Văn Linh')->first();
$duong->coordinates = [
    [16.0544, 108.2022],
    [16.0545, 108.2025],
    [16.0547, 108.2028],
    [16.0550, 108.2032],
    // ... thêm các điểm khác
];
$duong->save();
```

#### Cách 2: Tạo Seeder

Tạo file `database/seeders/StreetCoordinatesSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Duong;

class StreetCoordinatesSeeder extends Seeder
{
    public function run()
    {
        $coordinates = [
            'Nguyễn Văn Linh' => [
                [16.0544, 108.2022],
                [16.0545, 108.2025],
                [16.0547, 108.2028],
                [16.0550, 108.2032],
            ],
            'Lê Duẩn' => [
                [16.0470, 108.2200],
                [16.0472, 108.2205],
                [16.0475, 108.2210],
            ],
            // Thêm các đường khác...
        ];

        foreach ($coordinates as $streetName => $coords) {
            Duong::where('ten', $streetName)->update([
                'coordinates' => $coords
            ]);
        }
    }
}
```

Chạy seeder:

```bash
php artisan db:seed --class=StreetCoordinatesSeeder
```

## 🗺️ Format tọa độ

Tọa độ phải là mảng JSON với format:

```json
[
    [latitude1, longitude1],
    [latitude2, longitude2],
    [latitude3, longitude3]
]
```

**Lưu ý:**

- Latitude (vĩ độ): -90 đến 90
- Longitude (kinh độ): -180 đến 180
- Đà Nẵng: Latitude ~16.0, Longitude ~108.2
- Càng nhiều điểm, đường vẽ càng chính xác

## 🎨 Màu sắc trên bản đồ

Hệ thống tự động tô màu theo mức độ:

- 🟢 **Thấp (low)**: Màu xanh lá (#10b981)
- 🟠 **Trung bình (medium)**: Màu cam (#f59e0b)
- 🔴 **Cao (high)**: Màu đỏ (#ef4444)
- 🔴 **Nghiêm trọng (critical)**: Màu đỏ đậm (#dc2626)

Độ dày đường:

- Critical: 8px
- High: 6px
- Medium/Low: 4px

## 📱 Truy cập bản đồ

Sau khi cập nhật tọa độ, truy cập:

- Frontend: http://localhost:5173/map
- API: http://127.0.0.1:8000/api/alerts/map

## 🔄 Cập nhật thời gian thực

Bản đồ tự động refresh mỗi 30 giây để hiển thị cảnh báo mới nhất.

## 🛠️ Tools hữu ích

1. **geojson.io**: Vẽ và xuất tọa độ
2. **OpenStreetMap Overpass API**: Lấy dữ liệu đường
3. **Google Maps**: Lấy tọa độ điểm (cần chuyển đổi format)
