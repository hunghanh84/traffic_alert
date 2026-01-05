<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MucDoSuKienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('muc_do_su_kien')->insert([
            [
                'ma' => 'low',
                'ten' => 'Thấp',
                'mo_ta' => 'Mức độ ảnh hưởng thấp, giao thông vẫn lưu thông được',
                'uu_tien' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma' => 'medium',
                'ten' => 'Trung bình',
                'mo_ta' => 'Mức độ ảnh hưởng trung bình, giao thông chậm lại',
                'uu_tien' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma' => 'high',
                'ten' => 'Cao',
                'mo_ta' => 'Mức độ ảnh hưởng cao, giao thông tắc nghẽn',
                'uu_tien' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma' => 'critical',
                'ten' => 'Nghiêm trọng',
                'mo_ta' => 'Mức độ nghiêm trọng, giao thông tê liệt hoặc nguy hiểm',
                'uu_tien' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
