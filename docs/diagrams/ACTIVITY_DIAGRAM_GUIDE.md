# Hướng dẫn sử dụng Activity Diagram

## 📋 File PlantUML

**File:** `ACTIVITY_DIAGRAM.puml`

## 🎨 Cấu trúc sơ đồ

### 4 Swim Lanes (màu sắc):

1. **Khách vãng lai** (#E0F7FA - xanh cyan nhạt)

   - Xem bản đồ, tin tức
   - Đăng ký tài khoản

2. **Người dùng** (#BBDEFB - xanh dương nhạt)

   - Đăng nhập
   - Tạo cảnh báo mới
   - Thiết lập cảnh báo tự động
   - Quản lý tài khoản

3. **Admin** (#C8E6C9 - xanh lá nhạt)

   - Quản lý cảnh báo (duyệt/từ chối/xóa)
   - Quản lý người dùng
   - Quản lý sự kiện giao thông
   - Quản lý kết quả AI
   - Quản lý dữ liệu địa lý
   - Thống kê báo cáo

4. **Hệ thống AI** (#E1BEE7 - tím nhạt)
   - Phát hiện sự cố tự động
   - Tạo cảnh báo tự động
   - Cập nhật bản đồ realtime

## 🔧 Cách render sơ đồ

### Cách 1: VS Code (Khuyến nghị)

1. Cài extension **PlantUML**
2. Mở file `ACTIVITY_DIAGRAM.puml`
3. Nhấn `Alt+D` để xem preview
4. Export: Click chuột phải → "Export Current Diagram"

### Cách 2: Online

1. Truy cập: https://www.plantuml.com/plantuml/uml/
2. Copy nội dung file `.puml`
3. Paste vào editor
4. Download ảnh

### Cách 3: Command Line

```bash
# Sử dụng PlantUML JAR
java -jar plantuml.jar ACTIVITY_DIAGRAM.puml

# Hoặc sử dụng Docker
docker run -v $(pwd):/data plantuml/plantuml ACTIVITY_DIAGRAM.puml
```

## 📊 Các thành phần trong sơ đồ

### Ký hiệu:

- **Hình tròn đầy:** Start/Stop
- **Hình chữ nhật:** Activity/Action
- **Hình thoi:** Decision (if/else)
- **Thanh ngang:** Fork/Join (parallel)
- **Partition:** Nhóm activities liên quan
- **Note:** Ghi chú bổ sung

### Quy trình chính:

#### 1. Khách vãng lai:

```
Truy cập → Xem bản đồ → Xem tin tức
         ↓
    Đăng ký → Xác thực email
```

#### 2. Người dùng:

```
Đăng nhập → Xem thông tin
         ↓
    Tạo cảnh báo → Upload media → Gửi (chờ duyệt)
         ↓
    Thiết lập cảnh báo tự động → Nhận thông báo
```

#### 3. Admin:

```
Dashboard → Quản lý cảnh báo (Duyệt/Từ chối/Xóa)
         ↓
    Quản lý người dùng (CRUD + phân quyền)
         ↓
    Quản lý sự kiện giao thông
         ↓
    Quản lý kết quả AI (Xác minh)
         ↓
    Quản lý dữ liệu địa lý
         ↓
    Thống kê báo cáo
```

#### 4. Hệ thống AI:

```
Nhận dữ liệu camera → Phân tích
         ↓
    Phát hiện sự cố? → Lưu kết quả AI
         ↓
    Độ tin cậy > 80%? → Tạo cảnh báo tự động
         ↓
    Cập nhật bản đồ (30s)
```

## ✨ Điểm nổi bật

### 1. Quy trình đăng ký & xác thực:

- Đăng ký → Nhận OTP qua email → Xác thực → Kích hoạt tài khoản

### 2. Quy trình tạo cảnh báo:

- Chọn loại (traffic/flood)
- Chọn mức độ (low/medium/high/critical)
- Chọn đường
- Upload media (tùy chọn)
- Trạng thái: chờ duyệt

### 3. Quy trình duyệt cảnh báo:

- Admin xem danh sách chờ duyệt
- Kiểm tra thông tin
- Duyệt → Hiển thị công khai + Gửi thông báo
- Từ chối → Không hiển thị

### 4. Quy trình AI tự động:

- Camera → Phát hiện sự cố
- Độ tin cậy > 80% → Tạo cảnh báo tự động
- Admin xác minh

### 5. Cảnh báo tự động:

- Người dùng thiết lập: đường, mức độ, thời gian, kênh
- Khi có cảnh báo mới phù hợp → Gửi thông báo

## 🎯 Sử dụng trong báo cáo

### Đưa vào phần:

- **Chương 3:** Phân tích và thiết kế hệ thống
- **Mục 3.2:** Sơ đồ quy trình nghiệp vụ
- **Mục 3.3:** Mô tả luồng hoạt động

### Mô tả kèm theo:

> "Sơ đồ Activity mô tả quy trình hoạt động của hệ thống theo 4 vai trò chính:
> Khách vãng lai, Người dùng, Admin và Hệ thống AI. Sơ đồ thể hiện rõ luồng
> xử lý từ khi người dùng truy cập hệ thống, tạo cảnh báo, đến khi Admin
> duyệt và hệ thống gửi thông báo tự động."

## 📝 Ghi chú

- Sơ đồ được thiết kế theo chuẩn UML 2.0
- Màu sắc giúp phân biệt rõ ràng các swim lane
- Các partition giúp nhóm các activities liên quan
- Notes cung cấp thông tin bổ sung quan trọng

---

**Phiên bản:** 1.0  
**Ngày tạo:** 10/01/2026  
**Dự án:** Hệ thống Cảnh báo Giao thông Thông minh - Đà Nẵng
