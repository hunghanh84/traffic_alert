<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BaiDang;
use App\Models\Duong;

class UpdateKhuVucIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả bài đăng chưa có khu_vuc_id
        $baiDangs = BaiDang::whereNull('khu_vuc_id')
            ->whereNotNull('duong_id')
            ->get();

        $this->command->info("Tìm thấy {$baiDangs->count()} bài đăng cần cập nhật khu_vuc_id");

        $updated = 0;
        foreach ($baiDangs as $baiDang) {
            $duong = Duong::find($baiDang->duong_id);
            
            if ($duong && $duong->khu_vuc_id) {
                $baiDang->khu_vuc_id = $duong->khu_vuc_id;
                $baiDang->save();
                $updated++;
                $this->command->info("✓ Cập nhật bài đăng #{$baiDang->id}: khu_vuc_id = {$duong->khu_vuc_id}");
            } else {
                $this->command->warn("✗ Bài đăng #{$baiDang->id}: Đường không có khu_vuc_id");
            }
        }

        $this->command->info("\n=== Kết quả ===");
        $this->command->info("✓ Đã cập nhật: {$updated} bài đăng");
        
        // Kiểm tra lại
        $remaining = BaiDang::whereNull('khu_vuc_id')->whereNotNull('duong_id')->count();
        if ($remaining > 0) {
            $this->command->warn("⚠ Còn {$remaining} bài đăng chưa có khu_vuc_id");
        } else {
            $this->command->info("✅ Tất cả bài đăng đã có khu_vuc_id!");
        }
    }
}
