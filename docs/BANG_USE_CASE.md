# Bảng Use Case - Hệ thống Cảnh báo Giao thông Thông minh

## Bảng phân bổ Use Case theo Actor

| Actor              | Use case                   |
| ------------------ | -------------------------- |
| **Khách vãng lai** | Xem bản đồ cảnh báo        |
|                    | Xem tin tức giao thông     |
|                    | Đăng ký tài khoản          |
| **Người dùng**     | Đăng nhập                  |
|                    | Quản lý tài khoản          |
|                    | Xem bản đồ cảnh báo        |
|                    | Xem danh sách cảnh báo     |
|                    | Xem chi tiết cảnh báo      |
|                    | Lọc và tìm kiếm            |
|                    | Xem tin tức giao thông     |
|                    | Tạo cảnh báo mới           |
|                    | Chỉnh sửa cảnh báo của tôi |
|                    | Xóa cảnh báo của tôi       |
|                    | Thiết lập cảnh báo tự động |
| **Admin**          | Đăng nhập                  |
|                    | Xem Dashboard              |
|                    | Xem danh sách cảnh báo     |
|                    | Xem chi tiết cảnh báo      |
|                    | Duyệt cảnh báo             |
|                    | Từ chối cảnh báo           |
|                    | Xóa cảnh báo               |
|                    | Quản lý người dùng         |
|                    | Quản lý sự kiện giao thông |
|                    | Quản lý kết quả AI         |
|                    | Quản lý dữ liệu địa lý     |
|                    | Thống kê báo cáo           |

---

## Bảng chi tiết Use Case

### 1. Quản lý tài khoản

| STT | Use Case          | Actor             | Mô tả                     | Bao gồm                                                            |
| --- | ----------------- | ----------------- | ------------------------- | ------------------------------------------------------------------ |
| 1.1 | Đăng ký tài khoản | Khách vãng lai    | Tạo tài khoản mới         | Nhập thông tin, xác thực email                                     |
| 1.2 | Đăng nhập         | Người dùng, Admin | Đăng nhập vào hệ thống    | Xác thực thông tin, tạo phiên đăng nhập                            |
| 1.3 | Quản lý tài khoản | Người dùng        | Quản lý thông tin cá nhân | Cập nhật thông tin, đổi mật khẩu, vô hiệu hóa tài khoản, đăng xuất |

### 2. Xem thông tin cảnh báo

| STT | Use Case               | Actor                      | Mô tả                          | Bao gồm                                            |
| --- | ---------------------- | -------------------------- | ------------------------------ | -------------------------------------------------- |
| 2.1 | Xem bản đồ cảnh báo    | Khách vãng lai, Người dùng | Xem bản đồ giao thông realtime | Hiển thị cảnh báo theo vị trí, màu sắc theo mức độ |
| 2.2 | Xem danh sách cảnh báo | Người dùng                 | Xem danh sách cảnh báo         | Danh sách cảnh báo đã duyệt                        |
| 2.3 | Xem chi tiết cảnh báo  | Người dùng                 | Xem chi tiết một cảnh báo      | Thông tin, media, vị trí, người tạo                |
| 2.4 | Xem tin tức giao thông | Khách vãng lai, Người dùng | Xem tin tức từ RSS             | Tin tức giao thông cập nhật                        |
| 2.5 | Lọc và tìm kiếm        | Người dùng                 | Tìm kiếm cảnh báo              | Lọc theo loại, mức độ, vị trí, thời gian           |

### 3. Quản lý cảnh báo - Người dùng

| STT | Use Case                   | Actor      | Mô tả                   | Bao gồm                                        |
| --- | -------------------------- | ---------- | ----------------------- | ---------------------------------------------- |
| 3.1 | Tạo cảnh báo mới           | Người dùng | Tạo cảnh báo mới        | Chọn loại, mức độ, đường, mô tả, upload media  |
| 3.2 | Chỉnh sửa cảnh báo của tôi | Người dùng | Chỉnh sửa cảnh báo      | Cập nhật thông tin cảnh báo đã tạo             |
| 3.3 | Xóa cảnh báo của tôi       | Người dùng | Xóa cảnh báo của mình   | Xóa cảnh báo không còn cần thiết               |
| 3.4 | Thiết lập cảnh báo tự động | Người dùng | Cấu hình nhận thông báo | Chọn đường, mức độ, thời gian, ngày, kênh nhận |

### 4. Quản lý hệ thống - Admin

| STT  | Use Case                   | Actor | Mô tả                        | Bao gồm                                 |
| ---- | -------------------------- | ----- | ---------------------------- | --------------------------------------- |
| 4.1  | Xem Dashboard              | Admin | Xem tổng quan hệ thống       | Thống kê, hoạt động gần đây             |
| 4.2  | Xem danh sách cảnh báo     | Admin | Xem tất cả cảnh báo          | Danh sách cảnh báo (kể cả chờ duyệt)    |
| 4.3  | Xem chi tiết cảnh báo      | Admin | Xem chi tiết một cảnh báo    | Thông tin đầy đủ, media, người tạo      |
| 4.4  | Duyệt cảnh báo             | Admin | Phê duyệt cảnh báo           | Duyệt cảnh báo → hiển thị công khai     |
| 4.5  | Từ chối cảnh báo           | Admin | Từ chối cảnh báo             | Từ chối cảnh báo không phù hợp          |
| 4.6  | Xóa cảnh báo               | Admin | Xóa cảnh báo                 | Xóa cảnh báo vi phạm                    |
| 4.7  | Quản lý người dùng         | Admin | Quản lý người dùng hệ thống  | CRUD người dùng, bật/tắt, phân quyền    |
| 4.8  | Quản lý sự kiện giao thông | Admin | Quản lý sự kiện              | CRUD sự kiện, bật/tắt trạng thái        |
| 4.9  | Quản lý kết quả AI         | Admin | Quản lý kết quả phát hiện AI | Xem, xác minh, xóa kết quả AI           |
| 4.10 | Quản lý dữ liệu địa lý     | Admin | Quản lý dữ liệu vị trí       | Cập nhật tọa độ đường, danh mục, mức độ |
| 4.11 | Thống kê báo cáo           | Admin | Xem báo cáo chi tiết         | Báo cáo theo thời gian, vị trí, loại    |

---

## Mô tả chi tiết Use Case chính

### UC-01: Đăng ký tài khoản

- **Actor:** Khách vãng lai
- **Mô tả:** Người dùng tạo tài khoản mới để sử dụng hệ thống
- **Luồng chính:**
  1. Nhập thông tin: tên đăng nhập, email, mật khẩu, số điện thoại, phường/xã
  2. Hệ thống gửi mã OTP qua email
  3. Nhập mã OTP để xác thực
  4. Tài khoản được kích hoạt
- **Kết quả:** Tài khoản mới được tạo và kích hoạt

### UC-02: Tạo cảnh báo mới

- **Actor:** Người dùng
- **Mô tả:** Người dùng tạo cảnh báo giao thông mới
- **Luồng chính:**
  1. Chọn loại cảnh báo (traffic/flood)
  2. Chọn mức độ (low/medium/high/critical)
  3. Chọn đường
  4. Nhập mô tả
  5. Upload ảnh/video (tùy chọn)
  6. Gửi cảnh báo
- **Kết quả:** Cảnh báo được tạo với trạng thái "chờ duyệt"

### UC-03: Chỉnh sửa cảnh báo của tôi

- **Actor:** Người dùng
- **Mô tả:** Người dùng chỉnh sửa cảnh báo do mình tạo
- **Luồng chính:**
  1. Xem danh sách cảnh báo của tôi
  2. Chọn cảnh báo cần chỉnh sửa
  3. Cập nhật thông tin (loại, mức độ, đường, mô tả, media)
  4. Lưu thay đổi
- **Ràng buộc:** Chỉ chỉnh sửa được cảnh báo của chính mình
- **Kết quả:** Cảnh báo được cập nhật

### UC-04: Xóa cảnh báo của tôi

- **Actor:** Người dùng
- **Mô tả:** Người dùng xóa cảnh báo do mình tạo
- **Luồng chính:**
  1. Xem danh sách cảnh báo của tôi
  2. Chọn cảnh báo cần xóa
  3. Xác nhận xóa
- **Ràng buộc:** Chỉ xóa được cảnh báo của chính mình
- **Kết quả:** Cảnh báo bị xóa (soft delete)

### UC-05: Thiết lập cảnh báo tự động

- **Actor:** Người dùng
- **Mô tả:** Cấu hình nhận thông báo tự động khi có cảnh báo mới
- **Luồng chính:**
  1. Chọn đường muốn theo dõi
  2. Chọn mức độ cảnh báo tối thiểu (low/medium/high/critical)
  3. Thiết lập khung giờ nhận thông báo
  4. Chọn ngày trong tuần
  5. Chọn kênh nhận (Email/Telegram)
  6. Kích hoạt/tắt cảnh báo
- **Kết quả:** Nhận thông báo tự động khi có cảnh báo phù hợp

### UC-06: Xem danh sách cảnh báo (Admin)

- **Actor:** Admin
- **Mô tả:** Admin xem tất cả cảnh báo trong hệ thống
- **Luồng chính:**
  1. Truy cập trang quản lý cảnh báo
  2. Xem danh sách tất cả cảnh báo (kể cả chờ duyệt, đã duyệt, từ chối)
  3. Lọc theo trạng thái, loại, mức độ, thời gian
- **Kết quả:** Hiển thị danh sách cảnh báo đầy đủ

### UC-07: Xem chi tiết cảnh báo (Admin)

- **Actor:** Admin
- **Mô tả:** Admin xem chi tiết một cảnh báo cụ thể
- **Luồng chính:**
  1. Chọn một cảnh báo từ danh sách
  2. Xem thông tin đầy đủ: loại, mức độ, vị trí, mô tả, media, người tạo, thời gian
  3. Xem lịch sử thay đổi trạng thái
- **Kết quả:** Hiển thị thông tin chi tiết cảnh báo

### UC-08: Duyệt cảnh báo

- **Actor:** Admin
- **Mô tả:** Admin phê duyệt cảnh báo từ người dùng
- **Luồng chính:**
  1. Xem cảnh báo chờ duyệt
  2. Kiểm tra thông tin cảnh báo
  3. Click "Duyệt"
  4. Hệ thống cập nhật trạng thái → "đã duyệt"
  5. Gửi thông báo cho người dùng đã đăng ký
- **Kết quả:** Cảnh báo được hiển thị công khai, người dùng nhận thông báo

### UC-09: Từ chối cảnh báo

- **Actor:** Admin
- **Mô tả:** Admin từ chối cảnh báo không phù hợp
- **Luồng chính:**
  1. Xem cảnh báo chờ duyệt
  2. Kiểm tra thông tin cảnh báo
  3. Click "Từ chối"
  4. Nhập lý do từ chối (tùy chọn)
  5. Hệ thống cập nhật trạng thái → "từ chối"
- **Kết quả:** Cảnh báo bị từ chối, không hiển thị công khai

### UC-10: Xóa cảnh báo (Admin)

- **Actor:** Admin
- **Mô tả:** Admin xóa cảnh báo vi phạm hoặc không phù hợp
- **Luồng chính:**
  1. Xem danh sách cảnh báo
  2. Chọn cảnh báo cần xóa
  3. Click "Xóa"
  4. Xác nhận xóa
  5. Hệ thống xóa cảnh báo (soft delete)
- **Kết quả:** Cảnh báo bị xóa khỏi hệ thống

### UC-11: Quản lý người dùng

- **Actor:** Admin
- **Mô tả:** Admin quản lý tất cả người dùng
- **Luồng chính:**
  1. **Xem:** Danh sách và chi tiết người dùng
  2. **Tạo:** Tạo tài khoản mới
  3. **Cập nhật:** Cập nhật thông tin người dùng
  4. **Xóa:** Xóa người dùng
  5. **Bật/Tắt:** Kích hoạt/vô hiệu hóa tài khoản
  6. **Phân quyền:** Thay đổi vai trò (user ↔ admin)
- **Kết quả:** Người dùng được quản lý

### UC-12: Quản lý kết quả AI

- **Actor:** Admin
- **Mô tả:** Admin quản lý kết quả phát hiện từ AI detection service
- **Luồng chính:**
  1. **Xem:** Xem danh sách kết quả AI (nhãn, độ tin cậy, trạng thái xác minh)
  2. **Lọc:** Lọc theo nhãn phát hiện, độ tin cậy, trạng thái xác minh
  3. **Xem chi tiết:** Xem thông tin chi tiết kết quả AI (raw JSON, media)
  4. **Xác minh:** Xác nhận kết quả AI chính xác
  5. **Xóa:** Xóa kết quả AI sai
- **Kết quả:** Kết quả AI được quản lý, dữ liệu chính xác hơn

### UC-13: Quản lý sự kiện giao thông

- **Actor:** Admin
- **Mô tả:** Admin quản lý các sự kiện giao thông trong hệ thống
- **Luồng chính:**
  1. **Xem:** Danh sách và chi tiết sự kiện
  2. **Tạo:** Tạo sự kiện giao thông mới (tên, loại, thời gian, mô tả)
  3. **Cập nhật:** Cập nhật thông tin sự kiện
  4. **Xóa:** Xóa sự kiện
  5. **Bật/Tắt:** Kích hoạt/vô hiệu hóa sự kiện
- **Kết quả:** Sự kiện giao thông được quản lý

### UC-14: Quản lý dữ liệu địa lý

- **Actor:** Admin
- **Mô tả:** Admin quản lý dữ liệu địa lý của hệ thống
- **Luồng chính:**
  1. **Cập nhật tọa độ đường:** Cập nhật tọa độ GeoJSON cho các đường
  2. **Quản lý danh mục:** Quản lý phường/xã, khu vực, đường
  3. **Quản lý mức độ cảnh báo:** Quản lý các mức độ sự kiện (low, medium, high, critical)
- **Kết quả:** Dữ liệu địa lý được cập nhật, bản đồ hiển thị chính xác

---

## Tổng kết

### Số lượng Use Case:

- **Use Case chính:** 23 use cases
- **Khách vãng lai:** 3 use cases
- **Người dùng:** 11 use cases
- **Admin:** 12 use cases

### Ưu điểm của cách phân chia này:

✅ **Gọn gàng:** Giảm từ 57 xuống 23 use cases chính  
✅ **Dễ hiểu:** Mỗi use case đại diện cho một nhóm chức năng  
✅ **Rõ ràng:** Các chức năng chi tiết được mô tả trong cột "Bao gồm"  
✅ **Chuyên nghiệp:** Phù hợp với chuẩn UML và báo cáo đồ án

### Lưu ý:

- Mỗi use case chính có thể bao gồm nhiều chức năng con
- Các chức năng con được liệt kê trong phần "Bao gồm" hoặc "Mô tả chi tiết"
- Giữ nguyên logic nghiệp vụ nhưng trình bày gọn gàng hơn
