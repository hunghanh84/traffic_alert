# Tài liệu Sơ đồ Use Case - Hệ thống Cảnh báo Giao thông Thông minh

## 📊 Tổng quan

Sơ đồ Use Case mô tả các chức năng chính của hệ thống và mối quan hệ giữa các actor (người dùng) với các chức năng đó.

## 👥 Các Actor (Tác nhân)

### 1. **Khách vãng lai** (Guest)

- Người dùng chưa đăng ký/đăng nhập
- Quyền hạn hạn chế, chỉ xem thông tin công khai
- Có thể xem bản đồ, tin tức và đăng ký tài khoản

### 2. **Khách hàng** (Registered User)

- Người dùng đã đăng ký và đăng nhập
- Có thể tạo cảnh báo, chỉnh sửa, xóa cảnh báo của mình
- Thiết lập nhận thông báo tự động

### 3. **Admin** (Administrator)

- Quyền cao nhất trong hệ thống
- Quản lý toàn bộ hệ thống: duyệt cảnh báo, quản lý người dùng, sự kiện
- Xem dashboard và thống kê báo cáo

## 🎯 Các Use Case (Chức năng)

### A. Chức năng công khai (Khách vãng lai + Khách hàng)

| STT | Use Case               | Mô tả                                           | Actor           |
| --- | ---------------------- | ----------------------------------------------- | --------------- |
| UC1 | Xem bản đồ cảnh báo    | Xem bản đồ với các cảnh báo giao thông realtime | Guest, Customer |
| UC2 | Xem danh sách cảnh báo | Xem danh sách tất cả cảnh báo đã duyệt          | Customer        |
| UC3 | Xem chi tiết cảnh báo  | Xem thông tin chi tiết một cảnh báo             | Customer        |
| UC4 | Lọc - Tìm kiếm         | Tìm kiếm và lọc cảnh báo theo tiêu chí          | Customer        |
| UC5 | Xem tin tức giao thông | Xem tin tức giao thông từ RSS feed              | Customer        |

### B. Quản lý tài khoản

| STT  | Use Case                   | Mô tả                        | Actor                  |
| ---- | -------------------------- | ---------------------------- | ---------------------- |
| UC6  | Đăng ký                    | Đăng ký tài khoản mới        | Guest                  |
| UC7  | Xác thực email             | Xác thực email bằng mã OTP   | Guest                  |
| UC8  | Đăng nhập                  | Đăng nhập vào hệ thống       | Customer, Staff, Admin |
| UC13 | Cập nhật thông tin cá nhân | Cập nhật thông tin tài khoản | Customer               |

### C. Quản lý cảnh báo - Khách hàng

| STT  | Use Case                   | Mô tả                             | Actor    |
| ---- | -------------------------- | --------------------------------- | -------- |
| UC9  | Tạo cảnh báo mới           | Tạo cảnh báo giao thông mới       | Customer |
| UC10 | Upload media               | Upload ảnh/video cho cảnh báo     | Customer |
| UC11 | Thiết lập cảnh báo tự động | Thiết lập nhận thông báo tự động  | Customer |
| UC12 | Nhận thông báo             | Nhận thông báo qua email/Telegram | Customer |

### D. Quản lý cảnh báo - Nhân viên

| STT  | Use Case                     | Mô tả                                 | Actor        |
| ---- | ---------------------------- | ------------------------------------- | ------------ |
| UC14 | Xem cảnh báo                 | Xem tất cả cảnh báo (kể cả chờ duyệt) | Staff, Admin |
| UC15 | Duyệt cảnh báo               | Phê duyệt cảnh báo từ người dùng      | Staff, Admin |
| UC16 | Cập nhật trạng thái cảnh báo | Thay đổi trạng thái cảnh báo          | Staff, Admin |
| UC17 | Tạo báo cáo                  | Tạo báo cáo thống kê cảnh báo         | Staff, Admin |

### E. Quản lý hệ thống - Nhân viên

| STT  | Use Case                   | Mô tả                             | Actor        |
| ---- | -------------------------- | --------------------------------- | ------------ |
| UC18 | Quản lý sự kiện giao thông | CRUD sự kiện giao thông           | Staff, Admin |
| UC19 | Cập nhật tọa độ đường      | Cập nhật tọa độ GeoJSON cho đường | Staff, Admin |
| UC20 | Cập nhật banner            | Cập nhật banner trên trang chủ    | Staff, Admin |

### F. Quản trị hệ thống - Admin

| STT  | Use Case                   | Mô tả                                     | Actor |
| ---- | -------------------------- | ----------------------------------------- | ----- |
| UC21 | Xóa cảnh báo               | Xóa cảnh báo khỏi hệ thống                | Admin |
| UC22 | Quản lý người dùng         | CRUD người dùng                           | Admin |
| UC23 | Phân quyền người dùng      | Thay đổi vai trò người dùng               | Admin |
| UC24 | Xem danh sách khách hàng   | Xem thông tin tất cả khách hàng           | Admin |
| UC25 | Cập nhật danh mục          | Quản lý danh mục (phường, đường, khu vực) | Admin |
| UC26 | Cập nhật mức độ cảnh báo   | Quản lý mức độ sự kiện                    | Admin |
| UC27 | Cập nhật kích thước bản đồ | Cấu hình hiển thị bản đồ                  | Admin |
| UC28 | Thống kê báo cáo           | Xem dashboard và thống kê tổng quan       | Admin |

## 🔗 Mối quan hệ giữa các Use Case

### Include Relationships (<<include>>)

1. **Đăng ký → Xác thực email**

   - Khi đăng ký, bắt buộc phải xác thực email
   - Không thể hoàn thành đăng ký nếu chưa xác thực

2. **Tạo cảnh báo mới → Upload media**
   - Khi tạo cảnh báo, có thể upload ảnh/video
   - Upload media là một phần của quy trình tạo cảnh báo

## 📋 Ma trận phân quyền

| Use Case                     | Guest | Customer | Staff | Admin |
| ---------------------------- | :---: | :------: | :---: | :---: |
| Xem bản đồ cảnh báo          |  ✅   |    ✅    |  ✅   |  ✅   |
| Xem danh sách cảnh báo       |  ❌   |    ✅    |  ✅   |  ✅   |
| Xem chi tiết cảnh báo        |  ❌   |    ✅    |  ✅   |  ✅   |
| Lọc - Tìm kiếm               |  ❌   |    ✅    |  ✅   |  ✅   |
| Xem tin tức giao thông       |  ❌   |    ✅    |  ✅   |  ✅   |
| Đăng ký                      |  ✅   |    ❌    |  ❌   |  ❌   |
| Xác thực email               |  ✅   |    ❌    |  ❌   |  ❌   |
| Đăng nhập                    |  ❌   |    ✅    |  ✅   |  ✅   |
| Tạo cảnh báo mới             |  ❌   |    ✅    |  ❌   |  ❌   |
| Upload media                 |  ❌   |    ✅    |  ❌   |  ❌   |
| Thiết lập cảnh báo tự động   |  ❌   |    ✅    |  ❌   |  ❌   |
| Nhận thông báo               |  ❌   |    ✅    |  ❌   |  ❌   |
| Cập nhật thông tin cá nhân   |  ❌   |    ✅    |  ✅   |  ✅   |
| Xem cảnh báo (tất cả)        |  ❌   |    ❌    |  ✅   |  ✅   |
| Duyệt cảnh báo               |  ❌   |    ❌    |  ✅   |  ✅   |
| Cập nhật trạng thái cảnh báo |  ❌   |    ❌    |  ✅   |  ✅   |
| Tạo báo cáo                  |  ❌   |    ❌    |  ✅   |  ✅   |
| Quản lý sự kiện giao thông   |  ❌   |    ❌    |  ✅   |  ✅   |
| Cập nhật tọa độ đường        |  ❌   |    ❌    |  ✅   |  ✅   |
| Cập nhật banner              |  ❌   |    ❌    |  ✅   |  ✅   |
| Xóa cảnh báo                 |  ❌   |    ❌    |  ❌   |  ✅   |
| Quản lý người dùng           |  ❌   |    ❌    |  ❌   |  ✅   |
| Phân quyền người dùng        |  ❌   |    ❌    |  ❌   |  ✅   |
| Xem danh sách khách hàng     |  ❌   |    ❌    |  ❌   |  ✅   |
| Cập nhật danh mục            |  ❌   |    ❌    |  ❌   |  ✅   |
| Cập nhật mức độ cảnh báo     |  ❌   |    ❌    |  ❌   |  ✅   |
| Cập nhật kích thước bản đồ   |  ❌   |    ❌    |  ❌   |  ✅   |
| Thống kê báo cáo             |  ❌   |    ❌    |  ❌   |  ✅   |

## 🎨 Cách sử dụng

### Xem sơ đồ PNG:

```
d:/php/DATT_AI/docs/diagrams/use_case_diagram.png
```

### Chỉnh sửa PlantUML:

```
d:/php/DATT_AI/docs/diagrams/USE_CASE_DIAGRAM.puml
```

### Render lại sơ đồ:

1. Mở file `.puml` trong VS Code
2. Cài extension PlantUML
3. Nhấn `Alt+D` để preview
4. Export sang PNG/SVG

## 📝 Ghi chú

- Sơ đồ này phản ánh đầy đủ các chức năng của hệ thống
- Các actor có mối quan hệ kế thừa (Admin kế thừa quyền của Staff)
- Mỗi use case tương ứng với một hoặc nhiều API endpoint
- Sơ đồ được thiết kế theo chuẩn UML 2.0

---

**Phiên bản:** 1.0  
**Ngày tạo:** 09/01/2026  
**Dự án:** Hệ thống Cảnh báo Giao thông Thông minh - Đà Nẵng
