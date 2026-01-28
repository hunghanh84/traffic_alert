# Use Case Specification: Đăng nhập

## 1. Thông tin chung

| Thuộc tính           | Mô tả                                                                                                                    |
| -------------------- | ------------------------------------------------------------------------------------------------------------------------ |
| **Use case name**    | Đăng nhập                                                                                                                |
| **Use case ID**      | UC-005                                                                                                                   |
| **Description**      | Người dùng và Admin đăng nhập vào hệ thống để sử dụng các tính năng                                                      |
| **Actors**           | Người dùng đã đăng ký tài khoản, Admin                                                                                   |
| **Preconditions**    | - Người dùng đã đăng ký tài khoản<br>- Tài khoản đã được xác thực email<br>- Tài khoản đang ở trạng thái "active"        |
| **Postconditions**   | - Người dùng đăng nhập thành công<br>- Hệ thống tạo session/token<br>- Chuyển hướng đến trang chủ hoặc Dashboard (Admin) |
| **Priority**         | High                                                                                                                     |
| **Frequency of use** | Very High                                                                                                                |

## 2. Input/Output

### Input:

- **Email:** Địa chỉ email đã đăng ký (bắt buộc)
- **Mật khẩu:** Mật khẩu tài khoản (bắt buộc)

### Output:

- **Thành công:**
  - Thông báo "Đăng nhập thành công"
  - Token xác thực (Laravel Sanctum)
  - Thông tin người dùng (tên, email, vai trò)
- **Thất bại:**
  - Thông báo lỗi cụ thể

## 3. Basic Flow (Luồng chính)

| Bước | Người dùng                         | Hệ thống                                                                                                      |
| ---- | ---------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| 1    | Truy cập trang đăng nhập           | Hiển thị form đăng nhập                                                                                       |
| 2    | Nhập email đã đăng ký              | Validate định dạng email (realtime)                                                                           |
| 3    | Nhập mật khẩu                      | Hiển thị/ẩn mật khẩu khi click icon                                                                           |
| 4    | Nhấn nút "Đăng nhập"               |                                                                                                               |
| 5    |                                    | Kiểm tra email tồn tại trong database                                                                         |
| 6    |                                    | Kiểm tra mật khẩu khớp với hash trong database                                                                |
| 7    |                                    | Kiểm tra trạng thái tài khoản (active/inactive)                                                               |
| 8    |                                    | Kiểm tra email đã xác thực chưa                                                                               |
| 9    |                                    | Tạo token xác thực (Sanctum)                                                                                  |
| 10   |                                    | Lưu thông tin đăng nhập vào session                                                                           |
| 11   |                                    | Hiển thị thông báo "Đăng nhập thành công"                                                                     |
| 12   |                                    | **Nếu vai_tro = 'user':** Chuyển hướng đến trang chủ<br>**Nếu vai_tro = 'admin':** Chuyển hướng đến Dashboard |
| 13   | Sử dụng các tính năng của hệ thống |                                                                                                               |

**=> Kết thúc use case**

## 4. Alternative Flow (Luồng thay thế)

### AF-1: Người dùng chọn "Quên mật khẩu"

| Bước | Người dùng             | Hệ thống                                     |
| ---- | ---------------------- | -------------------------------------------- |
| 2a   | Click "Quên mật khẩu?" |                                              |
| 2b   |                        | Chuyển hướng đến trang "Quên mật khẩu"       |
| 2c   | Nhập email             |                                              |
| 2d   |                        | Gửi link reset mật khẩu qua email            |
| 2e   |                        | Hiển thị thông báo "Vui lòng kiểm tra email" |

**=> Kết thúc use case**

### AF-2: Người dùng chưa có tài khoản

| Bước | Người dùng           | Hệ thống                       |
| ---- | -------------------- | ------------------------------ |
| 1a   | Click "Đăng ký ngay" |                                |
| 1b   |                      | Chuyển hướng đến trang Đăng ký |

**=> Chuyển sang use case "Đăng ký tài khoản"**

## 5. Exception Flow (Luồng ngoại lệ)

### EF-1: Email không đúng định dạng

| Bước | Người dùng                            | Hệ thống                                            |
| ---- | ------------------------------------- | --------------------------------------------------- |
| 2a   | Nhập email sai định dạng (vd: "abc@") |                                                     |
| 2b   |                                       | Hiển thị lỗi realtime: "Email không đúng định dạng" |
| 2c   |                                       | Disable nút "Đăng nhập"                             |
| 2d   | Sửa lại email đúng định dạng          | Bỏ thông báo lỗi, enable nút "Đăng nhập"            |

**=> Quay lại bước 3 của Basic Flow**

### EF-2: Email không tồn tại

| Bước | Người dùng                                   | Hệ thống                                            |
| ---- | -------------------------------------------- | --------------------------------------------------- |
| 5a   |                                              | Không tìm thấy email trong database                 |
| 5b   |                                              | Hiển thị lỗi: "Email hoặc mật khẩu không chính xác" |
| 5c   | Nhập lại thông tin hoặc click "Đăng ký ngay" |                                                     |

**=> Quay lại bước 2 của Basic Flow hoặc chuyển sang AF-2**

### EF-3: Mật khẩu không chính xác

| Bước | Người dùng                                    | Hệ thống                                            |
| ---- | --------------------------------------------- | --------------------------------------------------- |
| 6a   |                                               | Mật khẩu không khớp với hash trong database         |
| 6b   |                                               | Hiển thị lỗi: "Email hoặc mật khẩu không chính xác" |
| 6c   | Nhập lại mật khẩu hoặc click "Quên mật khẩu?" |                                                     |

**=> Quay lại bước 3 của Basic Flow hoặc chuyển sang AF-1**

### EF-4: Tài khoản chưa xác thực email

| Bước | Người dùng                  | Hệ thống                                                    |
| ---- | --------------------------- | ----------------------------------------------------------- |
| 8a   |                             | Phát hiện email_verified_at = null                          |
| 8b   |                             | Hiển thị lỗi: "Vui lòng xác thực email trước khi đăng nhập" |
| 8c   |                             | Hiển thị nút "Gửi lại mã xác thực"                          |
| 8d   | Click "Gửi lại mã xác thực" | Gửi lại email xác thực                                      |

**=> Chuyển sang use case "Xác thực email"**

### EF-5: Tài khoản bị vô hiệu hóa

| Bước | Người dùng | Hệ thống                                                                    |
| ---- | ---------- | --------------------------------------------------------------------------- |
| 7a   |            | Phát hiện trang_thai = 'inactive'                                           |
| 7b   |            | Hiển thị lỗi: "Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ Admin" |

**=> Kết thúc use case**

### EF-6: Người dùng thoát trang

| Bước | Người dùng             | Hệ thống |
| ---- | ---------------------- | -------- |
| \*a  | Đóng tab/thoát website |          |

**=> Kết thúc use case**

### EF-7: Lỗi kết nối server

| Bước | Người dùng     | Hệ thống                                                           |
| ---- | -------------- | ------------------------------------------------------------------ |
| 5a   |                | Lỗi kết nối database hoặc server                                   |
| 5b   |                | Hiển thị lỗi: "Không thể kết nối đến server. Vui lòng thử lại sau" |
| 5c   | Nhấn "Thử lại" | Gửi lại request                                                    |

**=> Quay lại bước 5 của Basic Flow**

## 6. Business Rules

1. **Email:**

   - Phải đúng định dạng email (regex validation)
   - Không phân biệt hoa thường
   - Tối đa 255 ký tự

2. **Mật khẩu:**

   - Tối thiểu 8 ký tự
   - Không hiển thị plaintext (dùng bcrypt hash)

3. **Session/Token:**

   - Token có thời hạn (Laravel Sanctum)
   - Tự động đăng xuất khi token hết hạn

4. **Bảo mật:**

   - Không hiển thị thông báo cụ thể "Email không tồn tại" hay "Mật khẩu sai" (để tránh brute force)
   - Giới hạn số lần đăng nhập sai (rate limiting)

5. **Vai trò:**
   - User: Chuyển đến trang chủ
   - Admin: Chuyển đến Dashboard

## 7. UI/UX Requirements

1. **Form đăng nhập:**

   - Input email với placeholder "Email"
   - Input password với icon show/hide
   - Checkbox "Ghi nhớ đăng nhập" (optional)
   - Nút "Đăng nhập" (primary button)
   - Link "Quên mật khẩu?"
   - Link "Đăng ký ngay"

2. **Validation:**

   - Realtime validation cho email
   - Hiển thị lỗi dưới input field
   - Disable nút "Đăng nhập" khi có lỗi

3. **Loading state:**

   - Hiển thị spinner khi đang xử lý
   - Disable form khi đang submit

4. **Thông báo:**
   - Toast notification cho thành công/lỗi
   - Auto dismiss sau 3 giây

## 8. Technical Notes

### Backend (Laravel):

```php
// Route: POST /api/auth/login
// Controller: AuthController@login
// Middleware: throttle:5,1 (5 requests per minute)
```

### Frontend (Vue.js):

```javascript
// Component: LoginView.vue
// Store: auth.js (Pinia)
// API: authService.login(credentials)
```

### Database:

```sql
-- Table: nguoi_dung
-- Fields: email, mat_khau (bcrypt), email_verified_at, trang_thai, vai_tro
```

## 9. Related Use Cases

- **UC-003:** Đăng ký tài khoản
- **UC-004:** Xác thực email
- **UC-006:** Quên mật khẩu
- **UC-007:** Đăng xuất
- **UC-008:** Cập nhật thông tin cá nhân
