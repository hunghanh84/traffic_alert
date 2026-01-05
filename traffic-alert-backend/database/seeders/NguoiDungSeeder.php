<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NguoiDung;
use App\Models\KhuVuc;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first khu_vuc for demo
        $khuVuc = KhuVuc::first();

        // Admin user
        NguoiDung::create([
            'ten_dang_nhap' => 'admin',
            'mat_khau' => 'admin123', // Will be hashed by mutator
            'so_dien_thoai' => '0901234567',
            'email' => 'admin@trafficalert.com',
            'khu_vuc_id' => $khuVuc?->id,
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
        ]);

        // Điều hành user
        NguoiDung::create([
            'ten_dang_nhap' => 'dieuhanh',
            'mat_khau' => 'dieuhanh123',
            'so_dien_thoai' => '0902345678',
            'email' => 'dieuhanh@trafficalert.com',
            'khu_vuc_id' => $khuVuc?->id,
            'vai_tro' => 'dieu_hanh',
            'trang_thai' => 'hoat_dong',
        ]);

        // Regular users
        NguoiDung::create([
            'ten_dang_nhap' => 'user1',
            'mat_khau' => 'user123',
            'so_dien_thoai' => '0903456789',
            'email' => 'user1@example.com',
            'khu_vuc_id' => $khuVuc?->id,
            'vai_tro' => 'nguoi_dung',
            'trang_thai' => 'hoat_dong',
        ]);

        NguoiDung::create([
            'ten_dang_nhap' => 'user2',
            'mat_khau' => 'user123',
            'so_dien_thoai' => '0904567890',
            'email' => 'user2@example.com',
            'khu_vuc_id' => $khuVuc?->id,
            'vai_tro' => 'nguoi_dung',
            'trang_thai' => 'hoat_dong',
        ]);

        // Locked user (for testing)
        NguoiDung::create([
            'ten_dang_nhap' => 'locked_user',
            'mat_khau' => 'user123',
            'so_dien_thoai' => '0905678901',
            'email' => 'locked@example.com',
            'khu_vuc_id' => $khuVuc?->id,
            'vai_tro' => 'nguoi_dung',
            'trang_thai' => 'khoa',
        ]);

        // Suspended user (for testing)
        NguoiDung::create([
            'ten_dang_nhap' => 'suspended_user',
            'mat_khau' => 'user123',
            'so_dien_thoai' => '0906789012',
            'email' => 'suspended@example.com',
            'khu_vuc_id' => $khuVuc?->id,
            'vai_tro' => 'nguoi_dung',
            'trang_thai' => 'tam_dung',
        ]);

        $this->command->info('Created ' . NguoiDung::count() . ' users');
        $this->command->info('Admin credentials: admin / admin123');
        $this->command->info('Điều hành credentials: dieuhanh / dieuhanh123');
        $this->command->info('User credentials: user1 / user123');
    }
}
