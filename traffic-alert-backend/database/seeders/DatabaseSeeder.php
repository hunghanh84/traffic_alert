<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Đà Nẵng location data
        $this->call(ThanhPhoSeeder::class);
        $this->call(PhuongXaSeeder::class);
        $this->call(KhuVucSeeder::class);
        $this->call(DuongSeeder::class);
        
        // Seed users
        $this->call(NguoiDungSeeder::class);
    }
}
