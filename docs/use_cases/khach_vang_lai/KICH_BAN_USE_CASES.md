# BẢNG KỊCH BẢN USE CASES - KHÁCH VÃNG LAI

## 1. XEM BẢN ĐỒ CẢNH BÁO

| Use case name        | Xem bản đồ cảnh báo                                                                                                                                                                                                                                                     |
| -------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Description**      | Khách vãng lai xem bản đồ với các cảnh báo giao thông                                                                                                                                                                                                                   |
| **Actors**           | Khách vãng lai                                                                                                                                                                                                                                                          |
| **Input**            | Lọc theo loại, mức độ (tùy chọn)                                                                                                                                                                                                                                        |
| **Output**           | Hiển thị bản đồ với các marker cảnh báo                                                                                                                                                                                                                                 |
| **Basic Flow**       | 1. Khách vãng lai truy cập trang web<br>2. Khách vãng lai chọn "Bản đồ cảnh báo"<br>3. Hệ thống hiển thị bản đồ với các cảnh báo đã duyệt<br>4. Khách vãng lai xem các marker trên bản đồ<br>5. Khách vãng lai click vào marker để xem chi tiết<br>=> Kết thúc use case |
| **Alternative Flow** | **4a.** Khách vãng lai lọc theo loại cảnh báo (traffic/flood)<br>**4b.** Khách vãng lai lọc theo mức độ (low/medium/high/critical)<br>**4c.** Khách vãng lai zoom in/out bản đồ                                                                                         |
| **Exception Flow**   | **3a.** Không có cảnh báo => Hiển thị bản đồ trống<br>**3b.** Lỗi tải bản đồ => Hiển thị "Không thể tải bản đồ"                                                                                                                                                         |

---

## 2. XEM TIN TỨC GIAO THÔNG

| Use case name        | Xem tin tức giao thông                                                                                                                                                                                                                                                      |
| -------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Description**      | Khách vãng lai xem tin tức giao thông từ RSS                                                                                                                                                                                                                                |
| **Actors**           | Khách vãng lai                                                                                                                                                                                                                                                              |
| **Input**            | Không                                                                                                                                                                                                                                                                       |
| **Output**           | Danh sách tin tức giao thông                                                                                                                                                                                                                                                |
| **Basic Flow**       | 1. Khách vãng lai truy cập trang web<br>2. Khách vãng lai chọn "Tin tức giao thông"<br>3. Hệ thống lấy tin tức từ RSS feed<br>4. Hệ thống hiển thị danh sách tin tức<br>5. Khách vãng lai chọn một tin tức<br>6. Hệ thống hiển thị chi tiết tin tức<br>=> Kết thúc use case |
| **Alternative Flow** | **5a.** Khách vãng lai cuộn xem danh sách mà không click vào tin nào                                                                                                                                                                                                        |
| **Exception Flow**   | **3a.** Lỗi lấy RSS => Hiển thị "Không thể tải tin tức"<br>**4a.** Không có tin tức mới => Hiển thị "Chưa có tin tức"                                                                                                                                                       |

---

## 3. ĐĂNG KÝ TÀI KHOẢN

| Use case name        | Đăng ký tài khoản                                                                                                                                                                                                                                                                                                                                                            |
| -------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Description**      | Khách vãng lai đăng ký tài khoản mới để sử dụng hệ thống                                                                                                                                                                                                                                                                                                                     |
| **Actors**           | Khách vãng lai                                                                                                                                                                                                                                                                                                                                                               |
| **Input**            | Tên đăng nhập, email, mật khẩu, số điện thoại, phường/xã                                                                                                                                                                                                                                                                                                                     |
| **Output**           | Thông báo "Đăng ký thành công. Vui lòng kiểm tra email"                                                                                                                                                                                                                                                                                                                      |
| **Basic Flow**       | 1. Khách vãng lai chọn "Đăng ký"<br>2. Khách vãng lai nhập thông tin đăng ký<br>3. Khách vãng lai nhấn nút "Đăng ký"<br>4. Hệ thống kiểm tra và lưu tài khoản<br>5. Hệ thống gửi mã OTP qua email<br>6. Chuyển sang trang xác thực email<br>=> Kết thúc use case                                                                                                             |
| **Alternative Flow** | **2a.** Khách vãng lai nhập thông tin theo thứ tự bất kỳ miễn đầy đủ<br>**3a.** Khách vãng lai nhấn "Hủy" => Kết thúc use case<br>**1a.** Khách vãng lai đã có tài khoản, click "Đăng nhập ngay" => Chuyển sang use case "Đăng nhập"                                                                                                                                         |
| **Exception Flow**   | **2a.** Khách vãng lai thoát trang => Kết thúc use case<br>**2b.** Email sai định dạng => Hiển thị lỗi "Email không đúng định dạng"<br>**2c.** Mật khẩu < 8 ký tự => Hiển thị lỗi "Mật khẩu phải có ít nhất 8 ký tự"<br>**4a.** Email đã tồn tại => Hiển thị lỗi "Email đã được sử dụng"<br>**4b.** Tên đăng nhập đã tồn tại => Hiển thị lỗi "Tên đăng nhập đã được sử dụng" |

---

## TỔNG KẾT

- **Tổng số use cases:** 3
- **Actors:** Khách vãng lai
- **Thư mục:** `docs/use_cases/khach_vang_lai/`
- **File PlantUML:** 2 file Activity Diagram (Đăng ký dùng chung với Người dùng)

**Lưu ý:** Use case "Đăng ký tài khoản" đã có PlantUML trong thư mục `nguoi_dung`, không cần tạo lại.
