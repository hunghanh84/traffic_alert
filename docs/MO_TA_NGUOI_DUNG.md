# 2.1.3. Mô tả Người dùng

## 2.1.3.1. Người dùng (Registered User)

**Người dùng** là người đã đăng ký tài khoản và sử dụng hệ thống để xem thông tin cảnh báo giao thông, tạo cảnh báo mới, và thiết lập nhận thông báo tự động về tình hình giao thông.

### Các yêu cầu của Người dùng:

#### Quản lý tài khoản:

- Đăng ký tài khoản
- Xác thực email
- Đăng nhập
- Đổi mật khẩu
- Cập nhật thông tin cá nhân
- Vô hiệu hóa tài khoản
- Đăng xuất

#### Xem thông tin cảnh báo:

- Xem bản đồ cảnh báo giao thông
- Xem danh sách cảnh báo
- Xem chi tiết cảnh báo
- Lọc và tìm kiếm cảnh báo
- Xem tin tức giao thông
- Xem chi tiết đường

#### Quản lý cảnh báo:

- Tạo cảnh báo mới
- Upload media (ảnh/video) cho cảnh báo
- Chỉnh sửa cảnh báo của tôi
- Xóa cảnh báo của tôi

#### Thiết lập thông báo:

- Thiết lập cảnh báo tự động
- Cấu hình đường theo dõi
- Chọn mức độ cảnh báo tối thiểu
- Thiết lập thời gian nhận thông báo
- Chọn ngày trong tuần
- Chọn kênh nhận thông báo (Email/Telegram)
- Nhận thông báo tự động

---

## 2.1.3.2. Người dùng là Admin (Administrator)

**Admin** là người quản trị hệ thống có quyền cao nhất, có thể quản lý toàn bộ hệ thống bao gồm người dùng, cảnh báo, sự kiện giao thông và các dữ liệu hệ thống.

### Các yêu cầu của Admin:

#### Xác thực:

- Đăng nhập
- Đăng xuất

#### Dashboard & Thống kê:

- Xem Dashboard
- Thống kê báo cáo
- Xem hoạt động gần đây

#### Quản lý cảnh báo:

- Xem tất cả cảnh báo (kể cả chờ duyệt)
- Duyệt cảnh báo
- Từ chối cảnh báo
- Xóa cảnh báo
- Cập nhật trạng thái cảnh báo

#### Quản lý người dùng:

- Xem danh sách người dùng
- Xem chi tiết người dùng
- Tạo người dùng mới
- Cập nhật thông tin người dùng
- Xóa người dùng
- Bật/Tắt trạng thái người dùng
- Phân quyền người dùng (user/admin)

#### Quản lý sự kiện giao thông:

- Xem danh sách sự kiện
- Xem chi tiết sự kiện
- Tạo sự kiện mới
- Cập nhật sự kiện
- Xóa sự kiện
- Bật/Tắt trạng thái sự kiện

#### Quản lý dữ liệu địa lý:

- Cập nhật tọa độ đường
- Quản lý danh mục (phường/xã, khu vực, đường)
- Cập nhật mức độ cảnh báo

---

## 2.1.3.3. Người dùng là Khách vãng lai (Guest)

**Khách vãng lai** là người sử dụng hệ thống mà chưa thực hiện đăng ký, đăng nhập. Họ chỉ có quyền xem thông tin công khai.

### Các yêu cầu của Khách vãng lai:

#### Xem thông tin công khai:

- Xem bản đồ cảnh báo giao thông
- Xem tin tức giao thông

#### Đăng ký:

- Đăng ký tài khoản mới
- Xác thực email

---

## 2.1.4. So sánh quyền hạn giữa các loại người dùng

| Chức năng                        | Khách vãng lai |  Người dùng   |    Admin    |
| -------------------------------- | :------------: | :-----------: | :---------: |
| **Xem bản đồ cảnh báo**          |       ✅       |      ✅       |     ✅      |
| **Xem tin tức giao thông**       |       ✅       |      ✅       |     ✅      |
| **Đăng ký tài khoản**            |       ✅       |      ❌       |     ❌      |
| **Xác thực email**               |       ✅       |      ❌       |     ❌      |
| **Đăng nhập**                    |       ❌       |      ✅       |     ✅      |
| **Xem danh sách cảnh báo**       |       ❌       |      ✅       |     ✅      |
| **Xem chi tiết cảnh báo**        |       ❌       |      ✅       |     ✅      |
| **Lọc - Tìm kiếm**               |       ❌       |      ✅       |     ✅      |
| **Tạo cảnh báo mới**             |       ❌       |      ✅       |     ❌      |
| **Upload media**                 |       ❌       |      ✅       |     ❌      |
| **Chỉnh sửa cảnh báo**           |       ❌       | ✅ (của mình) |     ❌      |
| **Xóa cảnh báo**                 |       ❌       | ✅ (của mình) | ✅ (tất cả) |
| **Thiết lập cảnh báo tự động**   |       ❌       |      ✅       |     ❌      |
| **Nhận thông báo**               |       ❌       |      ✅       |     ❌      |
| **Cập nhật thông tin cá nhân**   |       ❌       |      ✅       |     ✅      |
| **Xem Dashboard**                |       ❌       |      ❌       |     ✅      |
| **Duyệt cảnh báo**               |       ❌       |      ❌       |     ✅      |
| **Từ chối cảnh báo**             |       ❌       |      ❌       |     ✅      |
| **Cập nhật trạng thái cảnh báo** |       ❌       |      ❌       |     ✅      |
| **Quản lý người dùng**           |       ❌       |      ❌       |     ✅      |
| **Quản lý sự kiện giao thông**   |       ❌       |      ❌       |     ✅      |
| **Cập nhật tọa độ đường**        |       ❌       |      ❌       |     ✅      |
| **Thống kê báo cáo**             |       ❌       |      ❌       |     ✅      |

---

## 2.1.5. Lưu ý về phân quyền

### Người dùng:

- Chỉ có thể chỉnh sửa/xóa cảnh báo do chính mình tạo
- Không thể duyệt hoặc xóa cảnh báo của người khác
- Cảnh báo mới tạo sẽ ở trạng thái "chờ duyệt" cho đến khi Admin phê duyệt

### Admin:

- Có toàn quyền quản lý hệ thống
- Có thể xóa bất kỳ cảnh báo nào
- Có thể thay đổi vai trò người dùng (user ↔ admin)
- Không tạo cảnh báo mới (chỉ duyệt cảnh báo từ người dùng)

### Khách vãng lai:

- Chỉ có quyền xem thông tin công khai
- Cần đăng ký và xác thực email để trở thành Người dùng
- Không thể tạo cảnh báo hoặc thiết lập thông báo

---

**Ghi chú:**

- Tất cả các chức năng đều được bảo vệ bằng Laravel Sanctum authentication
- Admin được xác định bằng trường `vai_tro = 'admin'` trong bảng `nguoi_dung`
- Middleware `CheckAdminRole` được sử dụng để bảo vệ các route admin
