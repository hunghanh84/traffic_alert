<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThanhPho;

class ThanhPhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $thanhPhos = [
            ['ten' => 'Đà Nẵng', 'ma' => '48'],
            // Có thể thêm các thành phố khác ở đây
        ];

        foreach ($thanhPhos as $thanhPho) {
            ThanhPho::create($thanhPho);
        }
    }
}
