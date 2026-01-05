<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Duong;
use App\Models\KhuVuc;

class DuongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy Phường Hải Châu
        $haiChau = \App\Models\PhuongXa::where('ten', 'Phường Hải Châu')->first();
        
        if (!$haiChau) {
            echo "⚠️ Không tìm thấy Phường Hải Châu. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy Phường An Hải
        $anHai = \App\Models\PhuongXa::where('ten', 'Phường An Hải')->first();
        
        if (!$anHai) {
            echo "⚠️ Không tìm thấy Phường An Hải. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy Phường An Khê
        $anKhe = \App\Models\PhuongXa::where('ten', 'Phường An Khê')->first();
        
        if (!$anKhe) {
            echo "⚠️ Không tìm thấy Phường An Khê. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Phường Hải Châu
        $khuVucTrungTam = KhuVuc::where('ten', 'Khu trung tâm')->first();
        $khuVucChoHan = KhuVuc::where('ten', 'Chợ Hàn')->first();
        $khuVucCauRong = KhuVuc::where('ten', 'Cầu Rồng')->first();
        $khuVucBachDang = KhuVuc::where('ten', 'Bạch Đằng')->first();
        $khuVucTranPhu = KhuVuc::where('ten', 'Trần Phú')->first();

        // Lấy các khu vực từ database - Phường An Hải
        $khuVucChoAnHai = KhuVuc::where('ten', 'Khu chợ An Hải')->first();
        $khuVucTranHungDao = KhuVuc::where('ten', 'Khu Trần Hưng Đạo')->first();
        $khuVucNguyenVanThoai = KhuVuc::where('ten', 'Khu Nguyễn Văn Thoại')->first();
        $khuVucVoNguyenGiap = KhuVuc::where('ten', 'Khu Võ Nguyên Giáp')->first();
        $khuVucHoangSa = KhuVuc::where('ten', 'Khu đường Hoàng Sa')->first();

        // Lấy các khu vực từ database - Phường An Khê
        $khuVucChoAnKhe = KhuVuc::where('ten', 'Khu chợ An Khê')->first();
        $khuVucNguyenHoang = KhuVuc::where('ten', 'Khu Nguyễn Hoàng')->first();
        $khuVucTranCaoVan = KhuVuc::where('ten', 'Khu Trần Cao Vân')->first();
        $khuVucLeDuan = KhuVuc::where('ten', 'Khu Lê Duẩn')->first();
        $khuVucTonDucThang = KhuVuc::where('ten', 'Khu đường Tôn Đức Thắng')->first();

        // Lấy Phường Cẩm Lệ
        $camLe = \App\Models\PhuongXa::where('ten', 'Phường Cẩm Lệ')->first();
        
        if (!$camLe) {
            echo "⚠️ Không tìm thấy Phường Cẩm Lệ. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Phường Cẩm Lệ
        $khuVucCauVuotCamLe = KhuVuc::where('ten', 'Khu vực cầu vượt Cẩm Lệ')->first();

        // Lấy Phường Hải Vân
        $haiVan = \App\Models\PhuongXa::where('ten', 'Phường Hải Vân')->first();
        
        if (!$haiVan) {
            echo "⚠️ Không tìm thấy Phường Hải Vân. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Phường Hải Vân
        $khuVucCauHaiVan = KhuVuc::where('ten', 'Khu vực cầu Hải Vân')->first();

        // Lấy Phường Hòa Cường
        $hoaCuong = \App\Models\PhuongXa::where('ten', 'Phường Hòa Cường')->first();
        
        if (!$hoaCuong) {
            echo "⚠️ Không tìm thấy Phường Hòa Cường. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy Xã Hòa Vang
        $hoaVang = \App\Models\PhuongXa::where('ten', 'Xã Hòa Vang')->first();
        
        if (!$hoaVang) {
            echo "⚠️ Không tìm thấy Xã Hòa Vang. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Phường Hòa Cường
        $khuVucChoHoaCuong = KhuVuc::where('ten', 'Khu chợ Hòa Cường')->first();

        // Lấy Phường Thanh Khê
        $thanhKhe = \App\Models\PhuongXa::where('ten', 'Phường Thanh Khê')->first();
        
        if (!$thanhKhe) {
            echo "⚠️ Không tìm thấy Phường Thanh Khê. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Phường Thanh Khê
        $khuVucChoThanhKhe = KhuVuc::where('ten', 'Khu chợ Thanh Khê')->first();

        // Lấy Phường Sơn Trà
        $sonTra = \App\Models\PhuongXa::where('ten', 'Phường Sơn Trà')->first();
        
        if (!$sonTra) {
            echo "⚠️ Không tìm thấy Phường Sơn Trà. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Phường Sơn Trà
        $khuVucBaiBienMyKhe = KhuVuc::where('ten', 'Khu bãi biển Mỹ Khê')->first();
        $khuVucBanDaoSonTra = KhuVuc::where('ten', 'Khu bán đảo Sơn Trà')->first();
        $khuVucHoangSaSonTra = KhuVuc::where('ten', 'Khu đường Hoàng Sa')->where('phuong_id', 6)->first();
        $khuVucNgoQuyenSonTra = KhuVuc::where('ten', 'Khu Ngô Quyền')->where('phuong_id', 6)->first();
        $khuVucVoNguyenGiapSonTra = KhuVuc::where('ten', 'Khu Võ Nguyên Giáp')->where('phuong_id', 6)->first();

        // Lấy Phường Ngũ Hành Sơn
        $nguHanhSon = \App\Models\PhuongXa::where('ten', 'Phường Ngũ Hành Sơn')->first();
        
        if (!$nguHanhSon) {
            echo "⚠️ Không tìm thấy Phường Ngũ Hành Sơn. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Phường Ngũ Hành Sơn
        $khuVucNonNuoc = KhuVuc::where('ten', 'Khu Non Nước')->first();
        $khuVucMyAn = KhuVuc::where('ten', 'Khu Mỹ An')->first();
        $khuVucLeVanHien = KhuVuc::where('ten', 'Khu đường Lê Văn Hiến')->first();
        $khuVucKhueMy = KhuVuc::where('ten', 'Khu Khuê Mỹ')->first();
        $khuVucBaiBienMyKheNHS = KhuVuc::where('ten', 'Khu Bãi biển Mỹ Khê')->where('phuong_id', 7)->first();

        // Lấy Xã Bà Nà
        $baNa = \App\Models\PhuongXa::where('ten', 'Xã Bà Nà')->first();
        
        if (!$baNa) {
            echo "⚠️ Không tìm thấy Xã Bà Nà. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Xã Bà Nà
        $khuVucVanhDaiPhiaTay = KhuVuc::where('ten', 'Khu vực đường Vành đai phía Tây')->first();

        // Lấy Xã Hòa Tiến
        $hoaTien = \App\Models\PhuongXa::where('ten', 'Xã Hòa Tiến')->first();
        
        if (!$hoaTien) {
            echo "⚠️ Không tìm thấy Xã Hòa Tiến. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy Xã Hòa Vang
        $hoaVang = \App\Models\PhuongXa::where('ten', 'Xã Hòa Vang')->first();
        
        if (!$hoaVang) {
            echo "⚠️ Không tìm thấy Xã Hòa Vang. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy Phường Hòa Khánh
        $hoaKhanh = \App\Models\PhuongXa::where('ten', 'Phường Hòa Khánh')->first();
        
        if (!$hoaKhanh) {
            echo "⚠️ Không tìm thấy Phường Hòa Khánh. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy Phường Liên Chiểu
        $lienChieu = \App\Models\PhuongXa::where('ten', 'Phường Liên Chiểu')->first();
        
        if (!$lienChieu) {
            echo "⚠️ Không tìm thấy Phường Liên Chiểu. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy Phường Hòa Xuân
        $hoaXuan = \App\Models\PhuongXa::where('ten', 'Phường Hòa Xuân')->first();
        
        if (!$hoaXuan) {
            echo "⚠️ Không tìm thấy Phường Hòa Xuân. Vui lòng chạy PhuongXaSeeder trước!\n";
            return;
        }

        // Lấy các khu vực từ database - Phường Hòa Xuân
        $khuVucNgaTuHoaXuan = KhuVuc::where('ten', 'Khu vực ngã tư Hòa Xuân')->first();

        // Định nghĩa các đường theo khu vực
        $duongData = [
            // 🔴 1. KHU TRUNG TÂM - Lõi hành chính, mật độ giao thông cao
            [
                'khu_vuc' => $khuVucTrungTam,
                'phuong' => $haiChau,
                'duong' => [
                    ['ten' => 'Đường Quang Trung', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Đường Nguyễn Chí Thanh', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Phan Châu Trinh', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Thái Phiên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lý Tự Trọng', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟩 2. CHỢ HÀN - Khu buôn bán, khách du lịch đông
            [
                'khu_vuc' => $khuVucChoHan,
                'phuong' => $haiChau,
                'duong' => [
                    ['ten' => 'Đường Trần Phú', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Bạch Đằng', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Hải Hồ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Bình Trọng', 'loai_duong' => 'Đường phụ'],
                ]
            ],
            
            // 🟥 3. CẦU RỒNG - Điểm nóng du lịch, sự kiện
            [
                'khu_vuc' => $khuVucCauRong,
                'phuong' => $haiChau,
                'duong' => [
                    ['ten' => 'Cầu Rồng', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Nguyễn Văn Linh', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường 2 Tháng 9', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường 3 Tháng 2', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Lê Đình Dương', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟨 4. BẠCH ĐẰNG (ven sông Hàn) - Ngập cục bộ + đông xe du lịch
            [
                'khu_vuc' => $khuVucBachDang,
                'phuong' => $haiChau,
                'duong' => [
                    ['ten' => 'Đường Thành Điện Hải', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 96 Hải Hồ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đầm Rong 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Trãi', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟪 5. TRẦN PHÚ - Trục lịch sử, hạ tầng cũ
            [
                'khu_vuc' => $khuVucTranPhu,
                'phuong' => $haiChau,
                'duong' => [
                    ['ten' => 'Đường Lê Lai', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Khắc Cần', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Hồng Thái', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Phú Thứ', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC - Không thuộc khu vực ưu tiên (khu_vuc_id = null)
            [
                'khu_vuc' => null,
                'phuong' => $haiChau,
                'duong' => [
                    ['ten' => 'Đường Yên Bái', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 144 Hải Phòng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Thị Minh Khai', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cao Thắng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hoàng Diệu', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Đường Ông Ích Khiêm', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Đường Phan Đình Phùng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phan Bội Châu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Du', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Vòng xoay Cầu Thuận Phước', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Cầu Thuận Phước', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Thanh Sơn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thanh Long', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thanh Hải', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thanh Thủy', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Văn Long', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hải Sơn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Pasteur', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Võ Thị Sáu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bùi Xuân Phái', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cô Bắc', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Kế Xương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tăng Bạt Hổ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 242 Hoàng Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 356 Hoàng Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 320 Hoàng Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 266 Hoàng Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 308 Hoàng Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 254 Hoàng Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 338 Hoàng Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Chu Văn An', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Quốc Toản', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Huỳnh Thúc Kháng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hoàng Văn Thụ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cô Giang', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 191 Đường Đỗ Quang', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bắc Đẩu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 34 Đường Lê Lai', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hàn Mặc Tử', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Châu Văn Liêm', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 16 Ngô Gia Tự', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ba Đình', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đào Tấn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Ngũ Lão', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Triệu Nữ Vương', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Đoàn Thị Điểm', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cao Xuân Dục', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Văn Tố', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Huỳnh Lý', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Đức Cảnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hoàng Tích Trí', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Văn Thủ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Hữu Cảnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đỗ Xuân Cát', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Tử Kính', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG AN HẢI - 5 KHU VỰC
            // ============================================
            
            // 🟩 1. KHU CHỢ AN HẢI - Quanh khu dân cư + chợ An Hải, mật độ kiệt – đường nội bộ cao
            [
                'khu_vuc' => $khuVucChoAnHai,
                'phuong' => $anHai,
                'duong' => [
                    ['ten' => 'Đường An Hải 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Hải 10', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Mỹ 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Mỹ 8', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Đồn 2', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟩 2. KHU TRẦN HƯNG ĐẠO - Trục ven sông Hàn – giao thông chính
            [
                'khu_vuc' => $khuVucTranHungDao,
                'phuong' => $anHai,
                'duong' => [
                    ['ten' => 'Đường Trần Hưng Đạo', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Cầu Sông Hàn', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Nguyễn Bỉnh Khiêm', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Triệu Việt Vương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Công Sáu', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟥 3. KHU NGUYỄN VĂN THOẠI - Trục du lịch – kết nối biển Mỹ Khê
            [
                'khu_vuc' => $khuVucNguyenVanThoai,
                'phuong' => $anHai,
                'duong' => [
                    ['ten' => 'Đường Nguyễn Văn Thoại', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Đường Hồ Nghinh', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Lê Mạnh Trinh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Đình Nghệ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Thiều', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟨 4. KHU VÕ NGUYÊN GIÁP - Trục ven biển – lưu lượng lớn
            [
                'khu_vuc' => $khuVucVoNguyenGiap,
                'phuong' => $anHai,
                'duong' => [
                    ['ten' => 'Đường Võ Nguyên Giáp', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Phạm Văn Đồng', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Đường Phạm Văn Đồng', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Đường Morrison', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Mỹ Khê 7', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Trí Trạch', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟪 5. KHU ĐƯỜNG HOÀNG SA - Gần biển – kết nối bán đảo Sơn Trà
            [
                'khu_vuc' => $khuVucHoangSa,
                'phuong' => $anHai,
                'duong' => [
                    ['ten' => 'Đường Loseby', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lương Thế Vinh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Võ Nghĩa', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Tu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Tự Minh', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG AN HẢI - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $anHai,
                'duong' => [
                    ['ten' => 'Đường Đỗ Anh Hàn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cao Bá Quát', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 925 Ngô Quyền', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đông Giang', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Đình Đàn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Hữu Thông', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Duy Hiệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 122 Lê Hữu Trác', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 54 Lê Hữu Trác', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Văn Quý', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Chính Hữu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phan Bôi', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 38 Lê Hữu Trác', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Hữu Tước', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Vương Thừa Vũ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 34 Lê Hữu Trác', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lưu Hữu Phước', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Thông', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thủ Khoa Huân', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 57 Lê Hữu Trác', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Huy Du', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG AN KHÊ - 5 KHU VỰC
            // ============================================
            
            // 🟩 1. KHU CHỢ AN KHÊ - Khu dân cư + chợ, đường nội bộ dày
            [
                'khu_vuc' => $khuVucChoAnKhe,
                'phuong' => $anKhe,
                'duong' => [
                    ['ten' => 'Đường Nguyễn Công Hoan', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lý Triện', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tú Mỡ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cù Chính Lan', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Như Mai', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟩 2. KHU NGUYỄN HOÀNG - Khu kết nối giao thông
            [
                'khu_vuc' => $khuVucNguyenHoang,
                'phuong' => $anKhe,
                'duong' => [
                    ['ten' => 'Đường Huỳnh Ngọc Huệ', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Hà Huy Tập', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Lê Trọng Tấn', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Nguyễn Đình Tựu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Phước Thái', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟥 3. KHU TRẦN CAO VÂN - Khu dân cư cũ
            [
                'khu_vuc' => $khuVucTranCaoVan,
                'phuong' => $anKhe,
                'duong' => [
                    ['ten' => 'Đường Trần Văn Ơn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngô Thế Lân', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Doãn Địch', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thúc Tề', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Văn Tạo', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟨 4. KHU LÊ DUẨN - Rìa trục lớn
            [
                'khu_vuc' => $khuVucLeDuan,
                'phuong' => $anKhe,
                'duong' => [
                    ['ten' => 'Đường Trần Thái Tông', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Vĩnh Khanh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hồ Tương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lâm Nhĩ', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // 🟪 5. KHU ĐƯỜNG TÔN ĐỨC THẮNG - Gần trục Tôn Đức Thắng
            [
                'khu_vuc' => $khuVucTonDucThang,
                'phuong' => $anKhe,
                'duong' => [
                    ['ten' => 'Kiệt 402 Trường Chinh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Nam 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Nam 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Nam 4', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG AN KHÊ - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $anKhe,
                'duong' => [
                    ['ten' => 'Bùi Vịnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 111 Nguyễn Công Hoan', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Quyền', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Nam 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Công Hãng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa An 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 171 Tôn Đản', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa Phước 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa An 18', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Thị Tính', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa Phước 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Vũ Trọng Hoàng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa An 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa An 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngô Nhân Tịnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 189 Nguyễn Công Hoan', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa An 10', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa Phước 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa An 11', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Giản Thanh', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG CẨM LỆ
            // ============================================
            
            // 🟦 KHU VỰC CẦU VƯỢT CẨM LỆ - Khu vực giao thông chính
            [
                'khu_vuc' => $khuVucCauVuotCamLe,
                'phuong' => $camLe,
                'duong' => [
                    ['ten' => 'Đường Thăng Long', 'loai_duong' => 'Đường phụ'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG CẨM LỆ - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $camLe,
                'duong' => [
                    ['ten' => 'Đường Tố Hữu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Huy Cận', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Đăng Đạo', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thành Thái', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bình Thái 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hà Tông Quyền', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đinh Châu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Hòa 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đông Thạnh 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hà Văn Trí', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phước Hòa 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hoàng Xuân Hãn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đỗ Đăng Tuyển', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Trác', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cẩm Bắc 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 95 Đỗ Thúc Tịnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 63 Đỗ Thúc Tịnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hoàng Ngọc Phách', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hồ Nguyên Trừng', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Bình Hoà 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Hòa 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trịnh Hoài Đức', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Nho Túy', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Hòa 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Duy', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Văn Cận', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lưu Nhân Chú', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bình Hòa 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cẩm Bắc 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Văn Linh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Chu Mạnh Trinh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tôn Thất Thuyết', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Thượng Hiền', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Phước Thành', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trương Quang Giao', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hồ Sĩ Dương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Hòa 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bình Thái 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Huấn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 133 Đỗ Thúc Tịnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Trung Ngạn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Tứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Văn Đang', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bình Hòa 10', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cao Xuân Huy', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Hòa 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Phú Tiết', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Phước Tấn', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Ông Ích Đường', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG HẢI VÂN
            // ============================================
            
            // 🟦 KHU VỰC CẦU HẢI VÂN - Khu vực giao thông chính
            [
                'khu_vuc' => $khuVucCauHaiVan,
                'phuong' => $haiVan,
                'duong' => [
                    ['ten' => 'Đường Nguyễn Tất Thành', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Nguyễn Tất Thành', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Mê Linh', 'loai_duong' => 'Đường phụ'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG HẢI VÂN - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $haiVan,
                'duong' => [
                    // Khu vực Suối Lương
                    ['ten' => 'Đường Suối Lương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Suối Lương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Cầu Suối Lương', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Khu vực Xuân Thiều
                    ['ten' => 'Xuân Thiều 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 8', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 9', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 10', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 14', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 29', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Xuân Thiều 33', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 7', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 10', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 11', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 12', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 18', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 19', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 20', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 21', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Xuân Thiều 24', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Khu vực Hàm Trung
                    ['ten' => 'Hàm Trung 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hàm Trung 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hàm Trung 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hàm Trung 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hàm Trung 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hàm Trung 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hàm Rung 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hàm Trung 8', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hàm Trung 9', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Các đường khác
                    ['ten' => 'Đường Trần Bích San', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Nguyễn Bá Phát', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trịnh Khắc Lập', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Phan Đình Giót', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đào Công Soạn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Tự Nhất Thống', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Nguyễn Hàng Chi', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Chu Sĩ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Vũ Huy Tấn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hồ Sĩ Đống', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Trần Tấn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trương Văn Lĩnh', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG HÒA CƯỜNG
            // ============================================
            
            // 🟧 KHU CHỢ HÒA CƯỜNG - Khu vực chợ và giao thông chính
            [
                'khu_vuc' => $khuVucChoHoaCuong,
                'phuong' => $hoaCuong,
                'duong' => [
                    ['ten' => 'Đường 30 Tháng 4', 'loai_duong' => 'Đường phụ'],
                    ['ten' => '30 Tháng 4', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Núi Thành', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Phan Thành Tài', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Đình Lý', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Nguyễn Tri Phương', 'loai_duong' => 'Đường phụ'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG HÒA CƯỜNG - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $hoaCuong,
                'duong' => [
                    // Đường trục chính
                    ['ten' => 'Đường 2 Tháng 9', 'loai_duong' => 'Đường chính'],
                    
                    // Đường trục phụ
                    ['ten' => 'Đường Hoàng Diệu', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường khu vực
                    ['ten' => 'Đường Lê Đình Thâm', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Hồ Nguyên Trừng', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Quy Mỹ', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường dân sinh (90 đường)
                    ['ten' => 'Kiệt 15 Duy Tân', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Cầm Bá Thước', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hồ Tông Thốc', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Nguyễn Xuân Ôn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Khôi', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Vũ Hữu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ỷ Lan Nguyên Phi', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tố Hữu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Lương Nhữ Hộc', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Quang Bích', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Hữu Thọ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Huy Cận', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Đăng Đạo', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hàn Thuyên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 356 Hoàng Diệu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tuệ Tĩnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Chu Văn An', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Thiện Thuật', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Văn Nghị', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bình An 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Bá Trạc', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Trác', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tiên Sơn 10', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngô Tất Tố', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Phẩm', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hóa Sơn 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngô Thị Liễu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nam Sơn 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bùi Viện', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 06 Nguyễn Xuân Nhĩ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Sơn Trà', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phan Huy Ôn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tân An 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trịnh Công Sơn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Cư Trinh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Châu Thượng Văn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Văn Giáp', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bình An 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Hanh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Quý Đôn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trương Chí Cương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hồ Biểu Chánh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Thường', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Thị Hồng Gấm', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đào Cam Mộc', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Bình', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Hữu Trang', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 383 Núi Thành', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Giang Văn Minh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hóa Quê Trung 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hoàng Xuân Nhị', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngô Thế Vinh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Vũ Trọng Phụng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Đức Thảo', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 21 2 Tháng 9', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hóa Quê Trung 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Lộ Trạch', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Tấn Mới', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hưng Hóa 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 59 Trương Chí Cương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Thành Ý', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Nguyên Cẩn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Khoái', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nơ Trang Lơng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tiên Sơn 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 217 Núi Thành', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tiên Sơn 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hoàng Thúc Trâm', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Khánh Toàn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 195 Núi Thành', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lý Nhân Tông', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 267 Núi Thành', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 318 Núi Thành', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hưng Hóa 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Văn Đức', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Bá Trinh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đoàn Quý Phi', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lưu Trọng Lư', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Xuân Nhĩ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hóa Sơn 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 374 Núi Thành', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 235 Tiểu La', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Trọng Tuệ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 03 Phan Thành Tài', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hóa Sơn 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trưng Nhị', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],

            // ============================================
            // PHƯỜNG THANH KHÊ
            // ============================================

            // 🟨 KHU CHỢ THANH KHÊ - Khu vực chợ và giao thông chính
            [
                'khu_vuc' => $khuVucChoThanhKhe,
                'phuong' => $thanhKhe,
                'duong' => [
                    ['ten' => 'Đường Nguyễn Tất Thành', 'loai_duong' => 'Đường chính'],
                ]
            ],

            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG THANH KHÊ - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $thanhKhe,
                'duong' => [
                    // Đường trục phụ
                    ['ten' => 'Đường Đống Đa', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Hoàng Hoa Thám', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Trần Cao Vân', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Hàm Nghi', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Lê Đình Lý', 'loai_duong' => 'Đường phụ'],

                    // Đường khu vực
                    ['ten' => 'Đường Lý Thái Tổ', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Nguyễn Tri Phương', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Huỳnh Ngọc Huệ', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Lê Độ', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Tôn Thất Đạm', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Thái Thị Bôi', 'loai_duong' => 'Đường phụ'],

                    // Đường dân sinh (38 đường)
                    ['ten' => 'Đường Phan Thanh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Văn Nghị', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đỗ Quang', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Thai Mai', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 8', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 47 Lý Thái Tổ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 7', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tản Đà', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đào Duy Từ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 196 Trần Cao Vân', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 02 Hà Khê', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hùng Vương', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Tống', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 1 Đỗ Quang', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Hạc 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tôn Thất Tùng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 65 Hàm Nghi', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 385 Hải Phòng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Võ Văn Tần', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Đức Trung', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Kỳ Đồng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 23 Tôn Thất Tùng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đào Duy Anh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Gia Thiều', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 191 Đường Đỗ Quang', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bắc Đẩu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'K391 Nguyễn Văn Linh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 385 Nguyễn Văn Linh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 62 Trần Xuân Lê', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Biểu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hẻm 54 Kiệt 123 Cù Chính Lan', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cù Chính Lan', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyên Hồng', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG SƠN TRÀ
            // ============================================
            
            // 🟦 KHU BÃI BIỂN MỸ KHÊ - Khu vực du lịch biển
            [
                'khu_vuc' => $khuVucBaiBienMyKhe,
                'phuong' => $sonTra,
                'duong' => [
                    ['ten' => 'Đường Lê Đức Thọ', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Cầu Thuận Phước', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Trần Hưng Đạo', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Trần Thánh Tông', 'loai_duong' => 'Đường phụ'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG SƠN TRÀ - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $sonTra,
                'duong' => [
                    // Đường khu vực
                    ['ten' => 'Đường Khúc Hạo', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Trần Duy Chiến', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Nguyễn Sáng', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường dân sinh (43 đường)
                    ['ten' => 'Đường Hoàng Quốc Việt', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Trần Côn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Trung Trực', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Thịnh 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Thịnh 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hương Hải Thiền Sư', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tôn Quang Phiệt', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Chân', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Tú 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Thịnh 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Hải 11', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Văn Xảo', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Phụ Trần', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Hải 12', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Tú 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bùi Huy Bích', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tân Thái 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cổ Mân 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Hiên Đông 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt Lộ Giời 3.0M', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bình Than', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Cao Bá Nhạ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Suối Đá 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngô Cao Lãng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Mân Quang 7', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Hiên Đông 7', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Hiên Đông 18', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 331 Đường Ngô Quyền', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Đăng Tuyến', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 142 Lê Văn Thứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Địa Lô', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Trọng Nghĩa', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thành Vinh 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Mân Quang 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Khát Chân', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đinh Công Trứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tân Thái 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hồ Thấu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trương Quyền', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Vũ Tông Phan', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nại Hiên Đông 4', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],

            // ============================================
            // PHƯỜNG NGŨ HÀNH SƠN
            // ============================================

            // 🟩 1. KHU NON NƯỚC - Gần làng đá Non Nước, hay ngập cục bộ
            [
                'khu_vuc' => $khuVucNonNuoc,
                'phuong' => $nguHanhSon,
                'duong' => [
                    ['ten' => 'Đường Huyền Trân Công Chúa', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Trường Sa', 'loai_duong' => 'Đường chính'],
                ]
            ],

            // 🟦 2. KHU MỸ AN - Khu dân cư + du lịch, gần biển
            [
                'khu_vuc' => $khuVucMyAn,
                'phuong' => $nguHanhSon,
                'duong' => [
                    ['ten' => 'Đường Ngũ Hành Sơn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Châu Thị Vĩnh Tế', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],

            // 🟨 3. KHU ĐƯỜNG LÊ VĂN HIẾN - Trục xương sống, hay ùn tắc
            [
                'khu_vuc' => $khuVucLeVanHien,
                'phuong' => $nguHanhSon,
                'duong' => [
                    // Không có đường cụ thể trong dữ liệu được cung cấp
                ]
            ],

            // 🟧 4. KHU KHUÊ MỸ - Dân cư đông, thấp trũng, điểm nóng ngập
            [
                'khu_vuc' => $khuVucKhueMy,
                'phuong' => $nguHanhSon,
                'duong' => [
                    ['ten' => 'Đường Đỗ Bá', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],

            // 🟪 5. KHU BÃI BIỂN MỸ KHÊ - Ven biển, ảnh hưởng triều
            [
                'khu_vuc' => $khuVucBaiBienMyKheNHS,
                'phuong' => $nguHanhSon,
                'duong' => [
                    ['ten' => 'Đường Võ Nguyên Giáp', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Hồ Xuân Hương', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Nguyễn Văn Thoại', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Bà Huyện Thanh Quan', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Đường Hoàng Kế Viêm', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],

            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG NGŨ HÀNH SƠN - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $nguHanhSon,
                'duong' => [
                    // Đường nhánh
                    ['ten' => 'Đường Phan Tứ', 'loai_duong' => 'Đường nhánh'],
                    
                    // Đường dân sinh (40 đường)
                    ['ten' => 'Đường Trần Văn Dư', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Thượng 15', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Thượng 18', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Thượng 14', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tôn Thất Thiệp', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Khánh Dư', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Thượng 23', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Khuê', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Thượng 17', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngô Thì Sĩ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Thượng 12', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 77 Trần Khánh Dư', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường An Thượng 24', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Võ Như Hưng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Mỹ Đa Đông 7', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lưu Quang Thuận', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hàm Tử', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 21 Chế Lan Viên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Quang Đạo', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Chế Lan Viên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thị Xuân Quý', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Mỹ Đa Đông 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 28 Phan Tứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 227 Đường Nguyễn Văn Thoại', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Mỹ Đa Đông 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 211 Đường Nguyễn Văn Thoại', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 117 Trần Văn Dư', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tô Hiến Thành', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Tự Quán', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Bá Lân', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // XÃ BÀ NÀ
            // ============================================
            
            // 🟫 KHU VỰC ĐƯỜNG VÀNH ĐAI PHÍA TÂY - Trục giao thông chính
            [
                'khu_vuc' => $khuVucVanhDaiPhiaTay,
                'phuong' => $baNa,
                'duong' => [
                    ['ten' => 'Đường Vành đai phía tây Đà Nẵng', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Bà Nà - Suối Mơ', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Bà Nà Suối Mơ', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường An Ngãi', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường gom cao tốc', 'loai_duong' => 'Đường chính'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC XÃ BÀ NÀ - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $baNa,
                'duong' => [
                    // Đường nhánh
                    ['ten' => 'Đường Quảng Xương', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Cầu Đỏ - Túy Loan', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Thăng Long', 'loai_duong' => 'Đường nhánh'],
                    
                    // Đường dân sinh
                    ['ten' => 'Kiệt 1 Thôn Thạch Nham Tây', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 10 Thôn Phú Hòa 2', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // XÃ HÒA TIẾN
            // ============================================
            
            // ⚪ CÁC ĐƯỜNG XÃ HÒA TIẾN - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $hoaTien,
                'duong' => [
                    // Đường chính
                    ['ten' => 'Đường tỉnh 605', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Vành đai phía tây Đà Nẵng', 'loai_duong' => 'Đường chính'],
                    
                    // Đường dân sinh - Khu La Bông
                    ['ten' => 'Đường La Bông 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường La Bông 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường La Bông 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường La Bông 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường La Bông 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường La Bông 6', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Đường dân sinh - Khu Phú Sơn Tây
                    ['ten' => 'Đường Phú Sơn Tây 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phú Sơn Tây 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phú Sơn Tây 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phú Sơn Tây 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phú Sơn Tây 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phú Sơn Tây 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hai Bà Trưng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Cách', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 1 Đường ĐH409 - Thôn Lệ Sơn Nam', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],

            // ============================================
            // XÃ HÒA VANG
            // ============================================

            // ⚪ CÁC ĐƯỜNG XÃ HÒA VANG - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $hoaVang,
                'duong' => [
                    // Đường chính
                    ['ten' => 'Quốc lộ 14G', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Cầu Trắng', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Bà Nà - Suối Mơ', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Bà Nà Suối Mơ', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Vành đai phía tây Đà Nẵng', 'loai_duong' => 'Đường chính'],
                    
                    // Đường cấp hai, cấp ba
                    ['ten' => 'Cầu Hội Phước', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Quảng Xương', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường dân sinh - Khu Dương Lâm
                    ['ten' => 'Đường Dương Lâm 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Lâm 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Lâm 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Lâm 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Lâm 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Lâm 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Lâm 7', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Đường dân sinh khác
                    ['ten' => 'Kiệt Số 04 Túy Loan Tây', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt Số 11 Thôn Dương Lâm 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt Số 06 Cẩm Toại Tây', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Strade', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Strada', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG HÒA KHÁNH
            // ============================================
            
            // ⚪ CÁC ĐƯỜNG PHƯỜNG HÒA KHÁNH - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $hoaKhanh,
                'duong' => [
                    // Đường cấp hai
                    ['ten' => 'Đường Nguyễn Sinh Sắc', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường cấp ba
                    ['ten' => 'Đường Nguyễn Huy Tưởng', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Trần Đình Tri', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Hồ Tùng Mậu', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Nguyễn Chánh', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường dân sinh - Khu Hòa Minh
                    ['ten' => 'Đường Hòa Minh 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 6', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 8', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 9', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 21', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Minh 22', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Đường dân sinh - Khu Hòa Nam
                    ['ten' => 'Đường Hòa Nam 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Nam 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Nam 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hòa Nam 6', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Đường dân sinh khác
                    ['ten' => 'Đường Doãn Địch', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Văn Tạo', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Vĩnh Khanh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tân Trào', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Đình Tứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Hồng Thái', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Như Mai', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tú Mỡ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nhơn Hòa 5', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Tất', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Đình Hổ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đồng Bài 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đoàn Phú Tứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Huy Trứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Anh Tông', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phan Thị Nể', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Đức Hiền', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đặng Dung', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Trần Nguyên Đán', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tốt Động', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Quý Khoách', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngọc Hồi', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Huy Tự', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Văn Thịnh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dũng Sĩ Thanh Khê', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Thiệt', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phùng Chí Kiên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Giáp Văn Cương', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG LIÊN CHIỂU
            // ============================================
            
            // ⚪ CÁC ĐƯỜNG PHƯỜNG LIÊN CHIỂU - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $lienChieu,
                'duong' => [
                    // Đường trục chính
                    ['ten' => 'Đường Nguyễn Tất Thành', 'loai_duong' => 'Đường chính'],
                    
                    // Đường cấp hai
                    ['ten' => 'Đường Âu Cơ', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Hưởng Phước', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường cấp ba
                    ['ten' => 'Nguyễn Chánh', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Phan Văn Định', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Lạc Long Quân', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Số 2', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Ninh Tốn', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Ngô Sĩ Liên', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Ngô Văn Sở', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Ngô Thì Nhậm', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Nguyễn Chánh', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường dân sinh - Khu Bàu Mạc
                    ['ten' => 'Đường Bàu Mạc 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Mạc 3', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Mạc 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Mạc 8', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Đường dân sinh - Khu Quang Thành
                    ['ten' => 'Quang Thành 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Quang Thành 2', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Đường dân sinh khác
                    ['ten' => 'Đường Đoàn Phú Tứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn An Ninh', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đặng Dung', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phan Văn Thuật', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đồng Bài 4', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Tôn Đức Thắng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 856 Tôn Đức Thắng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 9 Lạc Long Quân', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 42 Ngô Sĩ Liên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 920 Tôn Đức Thắng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Bùi Chát', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 14 Bùi Chát', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đồng Kè', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hẻm 17 Kiệt 856 Tôn Đức Thắng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Mậu Kiến', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hẻm 4 Kiệt 14 Bùi Chát', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 97 Nguyễn Lương Bằng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hẻm 21 Kiệt 856 Tôn Đức Thắng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 906 Tôn Đức Thắng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Hẻm 21 Kiệt 24 Tôn Đức Thắng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 58 Ngô Sĩ Liên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 24 Ngô Sĩ Liên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Kiệt 34 Ngô Sĩ Liên', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Vũ Ngọc Phan', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bạch Thái Bưởi', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đoàn Trần Nghiệp', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Đình Trọng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phạm Văn Tráng', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Nguyễn Kiều', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phan Văn Trường', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
            
            // ============================================
            // PHƯỜNG HÒA XUÂN - PHƯỜNG CUỐI CÙNG!
            // ============================================
            
            // 🟢 KHU VỰC NGÃ TƯ HÒA XUÂN - Khu vực giao thông chính
            [
                'khu_vuc' => $khuVucNgaTuHoaXuan,
                'phuong' => $hoaXuan,
                'duong' => [
                    ['ten' => 'Đường Nguyễn Phước Lan', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Thăng Long', 'loai_duong' => 'Đường nhánh'],
                    ['ten' => 'Lê Thanh Nghị', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Cách Mạng Tháng 8', 'loai_duong' => 'Đường phụ'],
                ]
            ],
            
            // ⚪ CÁC ĐƯỜNG KHÁC PHƯỜNG HÒA XUÂN - Không thuộc khu vực ưu tiên
            [
                'khu_vuc' => null,
                'phuong' => $hoaXuan,
                'duong' => [
                    // Đường trục chính
                    ['ten' => 'Cầu Cẩm Lệ', 'loai_duong' => 'Đường chính'],
                    ['ten' => 'Đường Phạm Hùng', 'loai_duong' => 'Đường chính'],
                    
                    // Đường cấp hai
                    ['ten' => 'Đường tỉnh 605', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Phạm Như Xương', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Cầu Tứ Câu', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Cầu Nguyễn Tri Phương', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Võ Chí Công', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Cầu Khuê Đông', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Võ Chí Công', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường cấp ba
                    ['ten' => 'Đường Thu Bồn', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Nguyễn Hồng Ánh', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Văn Tiến Dũng', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Âu Dương Lân', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Trần Tử Bình', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Nguyễn Hồng Án', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường Đô Đốc Lộc', 'loai_duong' => 'Đường phụ'],
                    ['ten' => 'Đường 29 Tháng 3', 'loai_duong' => 'Đường phụ'],
                    
                    // Đường dân sinh - Khu Dương Sơn
                    ['ten' => 'Đường Dương Sơn 1', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Sơn 2', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Dương Sơn 10', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Đường dân sinh - Khu Bàu Cầu
                    ['ten' => 'Đường Bàu Cầu 12', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Bàu Cầu 14', 'loai_duong' => 'Đường nội bộ'],
                    
                    // Đường dân sinh khác
                    ['ten' => 'Đường Đỗ Đăng Đệ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Ngô Huy Diễn', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Quang Hòa', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Văn Trà', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Nam Trung', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Mẹ Thứ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Văn Giàu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Phan Thúc Trực', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Trương Vĩnh Ký', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đặng Văn Kiều', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Đô Đốc Tuyết', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Nguyễn Lý', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Trần Lựu', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Thanh Hoá', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Mai Chí Thọ', 'loai_duong' => 'Đường nội bộ'],
                    ['ten' => 'Đường Lê Quảng Ba', 'loai_duong' => 'Đường nội bộ'],
                ]
            ],
        ];

        // Lưu dữ liệu vào database
        $totalCreated = 0;
        foreach ($duongData as $khuVucGroup) {
            $khuVuc = $khuVucGroup['khu_vuc'];
            $khuVucId = $khuVuc ? $khuVuc->id : null;
            
            // Xác định phường (mặc định là Hải Châu nếu không có)
            $phuong = $khuVucGroup['phuong'] ?? $haiChau;

            foreach ($khuVucGroup['duong'] as $duong) {
                // Kiểm tra xem đường đã tồn tại chưa (tránh trùng lặp)
                $existing = Duong::where('ten', $duong['ten'])
                    ->where('phuong_id', $phuong->id)
                    ->first();

                if (!$existing) {
                    Duong::create([
                        'phuong_id' => $phuong->id,
                        'khu_vuc_id' => $khuVucId,
                        'ten' => $duong['ten'],
                        'loai_duong' => $duong['loai_duong'],
                        'kich_hoat' => true,
                    ]);
                    $totalCreated++;
                }
            }
        }

        echo "✅ Đã tạo {$totalCreated} đường cho Đà Nẵng\n";
    }
}
