<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KhuVuc;

class KhuVucSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $khuVucs = [
            // Phường Hải Châu (phuong_id = 1)
            ['phuong_id' => 1, 'ten' => 'Khu trung tâm', 'vi_do' => 16.0673, 'kinh_do' => 108.2202],
            ['phuong_id' => 1, 'ten' => 'Chợ Hàn', 'vi_do' => 16.0657, 'kinh_do' => 108.2234],
            ['phuong_id' => 1, 'ten' => 'Cầu Rồng', 'vi_do' => 16.0608, 'kinh_do' => 108.2225],
            ['phuong_id' => 1, 'ten' => 'Bạch Đằng', 'vi_do' => 16.0632, 'kinh_do' => 108.2220],
            ['phuong_id' => 1, 'ten' => 'Trần Phú', 'vi_do' => 16.0685, 'kinh_do' => 108.2185],
            
            
            // Phường Hòa Cường (phuong_id = 2)
            ['phuong_id' => 2, 'ten' => 'Khu chợ Hòa Cường', 'vi_do' => 16.0725, 'kinh_do' => 108.2190],
           
           
            
            // Phường Thanh Khê (phuong_id = 3)
            ['phuong_id' => 3, 'ten' => 'Khu chợ Thanh Khê', 'vi_do' => 16.0612, 'kinh_do' => 108.2045],
            
            
            
            // Phường An Khê (phuong_id = 4)
            ['phuong_id' => 4, 'ten' => 'Khu chợ An Khê', 'vi_do' => 16.0735, 'kinh_do' => 108.2132],
            ['phuong_id' => 4, 'ten' => 'Khu Nguyễn Hoàng', 'vi_do' => 16.0740, 'kinh_do' => 108.2150],
            ['phuong_id' => 4, 'ten' => 'Khu Trần Cao Vân', 'vi_do' => 16.0728, 'kinh_do' => 108.2163],
            ['phuong_id' => 4, 'ten' => 'Khu Lê Duẩn', 'vi_do' => 16.0715, 'kinh_do' => 108.2148],
            ['phuong_id' => 4, 'ten' => 'Khu đường Tôn Đức Thắng', 'vi_do' => 16.0732, 'kinh_do' => 108.2175],
           
            
            // Phường An Hải (phuong_id = 5)
            ['phuong_id' => 5, 'ten' => 'Khu chợ An Hải', 'vi_do' => 16.0782, 'kinh_do' => 108.2264],
            ['phuong_id' => 5, 'ten' => 'Khu Trần Hưng Đạo', 'vi_do' => 16.0790, 'kinh_do' => 108.2240],
            ['phuong_id' => 5, 'ten' => 'Khu Nguyễn Văn Thoại', 'vi_do' => 16.0775, 'kinh_do' => 108.2271],
            ['phuong_id' => 5, 'ten' => 'Khu Võ Nguyên Giáp', 'vi_do' => 16.0801, 'kinh_do' => 108.2250],
            ['phuong_id' => 5, 'ten' => 'Khu đường Hoàng Sa', 'vi_do' => 16.0787, 'kinh_do' => 108.2233],
           
            // Phường Sơn Trà (phuong_id = 6)
           
            ['phuong_id' => 6, 'ten' => 'Khu bán đảo Sơn Trà', 'vi_do' => 16.0740, 'kinh_do' => 108.2520],
            
            // Phường Ngũ Hành Sơn (phuong_id = 7)
            ['phuong_id' => 7, 'ten' => 'Khu Non Nước', 'vi_do' => 16.0335, 'kinh_do' => 108.2410],
            ['phuong_id' => 7, 'ten' => 'Khu Mỹ An', 'vi_do' => 16.0348, 'kinh_do' => 108.2395],
            ['phuong_id' => 7, 'ten' => 'Khu đường Lê Văn Hiến', 'vi_do' => 16.0320, 'kinh_do' => 108.2425],
            ['phuong_id' => 7, 'ten' => 'Khu Khuê Mỹ', 'vi_do' => 16.0312, 'kinh_do' => 108.2380],
            ['phuong_id' => 7, 'ten' => 'Khu Bãi biển Mỹ Khê', 'vi_do' => 16.0355, 'kinh_do' => 108.2403],
           
            
            // Phường Hòa Khánh (phuong_id = 8)
            ['phuong_id' => 8, 'ten' => 'Khu vực ngã tư Hòa Khánh', 'vi_do' => 16.0378, 'kinh_do' => 108.1823],
            
    
            
            // Phường Hải Vân (phuong_id = 9)
            ['phuong_id' => 9, 'ten' => 'Khu vực cầu Hải Vân', 'vi_do' => 16.0221, 'kinh_do' => 108.1875],
           
            
            // Phường Liên Chiểu (phuong_id = 10)
            ['phuong_id' => 10, 'ten' => 'Khu vực đường Lê Văn Hiến', 'vi_do' => 16.0555, 'kinh_do' => 108.1900],
            
            
            // Phường Cẩm Lệ (phuong_id = 11)
            ['phuong_id' => 11, 'ten' => 'Khu vực cầu vượt Cẩm Lệ', 'vi_do' => 16.0403, 'kinh_do' => 108.2031],
           
            
            // Phường Hòa Xuân (phuong_id = 12)
            ['phuong_id' => 12, 'ten' => 'Khu vực ngã tư Hòa Xuân', 'vi_do' => 16.0425, 'kinh_do' => 108.2182],
           
            
            // Phường Hòa Vang (phuong_id = 13)
            ['phuong_id' => 13, 'ten' => 'Khu vực trung tâm Hòa Vang', 'vi_do' => 16.0344, 'kinh_do' => 108.2101],
           
            
            // Xã Hòa Tiến (phuong_id = 14)
            ['phuong_id' => 14, 'ten' => 'Khu vực ngập lụt Hòa Tiến', 'vi_do' => 16.0642, 'kinh_do' => 108.2301],
            
            
            // Xã Bà Nà (phuong_id = 15)
            ['phuong_id' => 15, 'ten' => 'Khu vực đường Vành đai phía Tây', 'vi_do' => 16.0233, 'kinh_do' => 108.0582]
           
        ];

        foreach ($khuVucs as $khuVuc) {
            KhuVuc::create($khuVuc);
        }
    }
}
