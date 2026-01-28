# Database ERD - Hệ thống Cảnh báo Giao thông Đà Nẵng

## 📊 Sơ đồ Cơ sở Dữ liệu

File `DATABASE_ERD.dbml` chứa định nghĩa đầy đủ của database schema theo định dạng DBML (Database Markup Language).

## 🎨 Cách xem ERD

### 1. **dbdiagram.io** (Khuyên dùng) ⭐

1. Truy cập: https://dbdiagram.io/
2. Click "Go to App"
3. Copy toàn bộ nội dung file `DATABASE_ERD.dbml`
4. Paste vào editor
5. Diagram sẽ tự động render

**Tính năng:**

- ✅ Miễn phí
- ✅ Tự động layout
- ✅ Export PNG, PDF, SQL
- ✅ Chia sẻ link
- ✅ Dark mode

### 2. **VS Code Extension**

1. Cài extension: **DBML Viewer**
2. Mở file `DATABASE_ERD.dbml`
3. Click icon "Preview" ở góc phải trên

### 3. **CLI Tool**

```bash
npm install -g @dbml/cli
dbml2sql DATABASE_ERD.dbml --mysql
```

## 📋 Cấu trúc Database

### 🗺️ **Địa lý & Tuyến đường** (4 tables)

- `thanh_pho` - Thành phố
- `phuong_xa` - Phường/Xã
- `khu_vuc` - Khu vực
- `duong` - Đường phố

### 👥 **Người dùng** (1 table)

- `nguoi_dung` - Người dùng (admin, người dùng, điều hành)

### ⚙️ **Thiết lập** (2 tables)

- `muc_do_su_kien` - Mức độ sự kiện
- `thiet_lap_canh_bao` - Thiết lập cảnh báo tự động

### 📝 **Bài đăng & Media** (2 tables)

- `bai_dang` - Bài đăng cảnh báo
- `media` - Ảnh/Video đính kèm

### 🤖 **AI** (1 table)

- `ket_qua_ai` - Kết quả phân tích AI

### 📅 **Sự kiện** (3 tables)

- `loai_su_kien` - Loại sự kiện (tắc đường, ngập, tai nạn)
- `trang_thai_su_kien` - Trạng thái sự kiện
- `su_kien_giao_thong` - Sự kiện giao thông

### 📹 **Camera** (1 table)

- `camera` - Camera giám sát

### 🔔 **Thông báo** (2 tables)

- `thong_bao` - Thông báo
- `gui_thong_bao` - Lịch sử gửi thông báo

### 📊 **Thống kê** (1 table)

- `thong_ke_giao_thong` - Thống kê giao thông

### 🔐 **System** (1 table)

- `personal_access_tokens` - Laravel Sanctum tokens

## 🔗 Quan hệ chính

```
thanh_pho (1) ──< (n) phuong_xa (1) ──< (n) khu_vuc (1) ──< (n) duong

nguoi_dung (1) ──< (n) bai_dang (1) ──< (n) media (1) ──< (1) ket_qua_ai

ket_qua_ai (1) ──< (1) su_kien_giao_thong (1) ──< (n) thong_bao (1) ──< (n) gui_thong_bao
```

## 📈 Tổng số tables: **18 tables**

- Địa lý: 4
- Người dùng: 1
- Thiết lập: 2
- Bài đăng: 2
- AI: 1
- Sự kiện: 3
- Camera: 1
- Thông báo: 2
- Thống kê: 1
- System: 1

## 🛠️ Export Options

### Export SQL

```bash
# MySQL
dbml2sql DATABASE_ERD.dbml --mysql -o schema.sql

# PostgreSQL
dbml2sql DATABASE_ERD.dbml --postgres -o schema.sql
```

### Export PNG/PDF

Sử dụng dbdiagram.io:

1. Render diagram
2. Click "Export" ở góc phải trên
3. Chọn PNG hoặc PDF

## 📝 Notes

- Tất cả tables đều có `created_at` và `updated_at` (timestamps)
- Một số tables có soft delete (`deleted_at`)
- Foreign keys được thiết lập với `onDelete` actions phù hợp
- Indexes được tạo cho các cột thường xuyên query

## 🔄 Cập nhật

File này được tạo tự động từ Laravel migrations. Khi có migration mới, cần cập nhật lại file DBML.

---

**Tạo ngày:** 2026-01-10  
**Phiên bản:** 1.0  
**Dự án:** Traffic Alert System - Đà Nẵng
