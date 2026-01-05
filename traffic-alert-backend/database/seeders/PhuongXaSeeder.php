<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhuongXa;

class PhuongXaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phuongXas = [
            ['thanh_pho_id' => 1, 'ten' => 'Phường Hải Châu', 'ma' => '4801'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Hòa Cường', 'ma' => '4802'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Thanh Khê', 'ma' => '4803'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường An Khê', 'ma' => '4804'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường An Hải', 'ma' => '4805'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Sơn Trà', 'ma' => '4806'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Ngũ Hành Sơn', 'ma' => '4807'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Hòa Khánh', 'ma' => '4808'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Hải Vân', 'ma' => '4809'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Liên Chiểu', 'ma' => '4810'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Cẩm Lệ', 'ma' => '4811'],
            ['thanh_pho_id' => 1, 'ten' => 'Phường Hòa Xuân', 'ma' => '4812'],
            ['thanh_pho_id' => 1, 'ten' => 'Xã Hòa Vang', 'ma' => '4813'],
            ['thanh_pho_id' => 1, 'ten' => 'Xã Hòa Tiến', 'ma' => '4814'],
            ['thanh_pho_id' => 1, 'ten' => 'Xã Bà Nà', 'ma' => '4815'],
        ];

        foreach ($phuongXas as $phuongXa) {
            PhuongXa::create($phuongXa);
        }
    }
}
