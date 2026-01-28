# 🔐 HỆ THỐNG PHÂN QUYỀN - TRAFFIC ALERT

## 📊 TỔNG QUAN 3 LOẠI NGƯỜI DÙNG:

| Loại      | Mô tả                 | Đăng nhập |
| --------- | --------------------- | --------- |
| **Guest** | Khách vãng lai        | ❌ Không  |
| **User**  | Người dùng đã đăng ký | ✅ Có     |
| **Admin** | Quản trị viên         | ✅ Có     |

---

## 🎯 PHÂN QUYỀN THEO TÍNH NĂNG:

### **1. XEM THÔNG TIN CÔNG KHAI:**

| Tính năng                       | Guest | User | Admin |
| ------------------------------- | ----- | ---- | ----- |
| Xem tin tức                     | ✅    | ✅   | ✅    |
| Xem bản đồ real-time            | ✅    | ✅   | ✅    |
| Xem danh sách cảnh báo đã duyệt | ✅    | ✅   | ✅    |
| Xem chi tiết cảnh báo đã duyệt  | ✅    | ✅   | ✅    |

**API:**

- `GET /api/news` - Không cần auth
- `GET /api/alerts/map` - Không cần auth
- `GET /api/alerts/public` - Không cần auth (chỉ `da_duyet`)

---

### **2. QUẢN LÝ CẢNH BÁO CÁ NHÂN:**

| Tính năng                         | Guest | User | Admin |
| --------------------------------- | ----- | ---- | ----- |
| Gửi cảnh báo mới                  | ❌    | ✅   | ✅    |
| Xem cảnh báo của mình             | ❌    | ✅   | ✅    |
| Sửa cảnh báo của mình (chờ duyệt) | ❌    | ✅   | ✅    |
| Xóa cảnh báo của mình             | ❌    | ✅   | ✅    |

**API:**

- `POST /api/alerts` - Cần auth (User/Admin)
- `GET /api/alerts?mine=1` - Cần auth (User/Admin)
- `PUT /api/alerts/{id}` - Cần auth + owner
- `DELETE /api/alerts/{id}` - Cần auth + owner

---

### **3. QUẢN TRỊ HỆ THỐNG:**

| Tính năng                             | Guest | User | Admin |
| ------------------------------------- | ----- | ---- | ----- |
| Xem tất cả cảnh báo (kể cả chờ duyệt) | ❌    | ❌   | ✅    |
| Duyệt cảnh báo                        | ❌    | ❌   | ✅    |
| Từ chối cảnh báo                      | ❌    | ❌   | ✅    |
| Xóa bất kỳ cảnh báo nào               | ❌    | ❌   | ✅    |
| Quản lý người dùng                    | ❌    | ❌   | ✅    |
| Xem thống kê                          | ❌    | ❌   | ✅    |

**API:**

- `GET /api/admin/alerts` - Cần auth Admin
- `POST /api/admin/alerts/{id}/approve` - Cần auth Admin
- `POST /api/admin/alerts/{id}/reject` - Cần auth Admin
- `DELETE /api/admin/alerts/{id}` - Cần auth Admin
- `GET /api/admin/users` - Cần auth Admin
- `GET /api/admin/statistics` - Cần auth Admin

---

## 🛣️ PHÂN QUYỀN THEO ROUTE (FRONTEND):

### **PUBLIC ROUTES (Guest có thể truy cập):**

```
/                    - Trang chủ
/news                - Tin tức
/map                 - Bản đồ
/routes              - Danh sách cảnh báo công khai
/login               - Đăng nhập
/register            - Đăng ký
```

### **USER ROUTES (Cần đăng nhập):**

```
/alerts              - Cảnh báo của tôi
/alerts/create       - Tạo cảnh báo mới
/alerts/{id}         - Chi tiết cảnh báo của tôi
/alerts/{id}/edit    - Sửa cảnh báo của tôi
/profile             - Thông tin cá nhân
```

### **ADMIN ROUTES (Chỉ Admin):**

```
/admin               - Dashboard admin
/admin/alerts        - Quản lý tất cả cảnh báo
/admin/users         - Quản lý người dùng
/admin/statistics    - Thống kê
```

---

## 🔧 CẦN SỬA:

### **1. Backend - AlertController:**

```php
// GET /api/alerts - Cảnh báo của user đó
public function index(Request $request)
{
    if (!Auth::guard('sanctum')->check()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $query = BaiDang::where('nguoi_dung_id', Auth::id());
    // ...
}

// GET /api/alerts/public - Cảnh báo công khai
public function publicIndex(Request $request)
{
    $query = BaiDang::where('trang_thai', 'da_duyet');
    // ...
}
```

### **2. Frontend - Route Guards:**

```javascript
// router/index.js
{
  path: '/alerts',
  component: MyAlerts,
  meta: { requiresAuth: true }  // Cần đăng nhập
},
{
  path: '/routes',
  component: PublicAlerts,
  meta: { requiresAuth: false }  // Không cần đăng nhập
},
{
  path: '/admin',
  component: AdminDashboard,
  meta: { requiresAuth: true, requiresAdmin: true }
}
```

### **3. Middleware:**

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/alerts', [AlertController::class, 'index']);
    Route::post('/alerts', [AlertController::class, 'store']);
});

Route::get('/alerts/public', [AlertController::class, 'publicIndex']);
Route::get('/alerts/map', [AlertController::class, 'getApprovedAlertsForMap']);

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/alerts', [AdminAlertController::class, 'index']);
    Route::post('/alerts/{id}/approve', [AdminAlertController::class, 'approve']);
});
```

---

## ✅ CHECKLIST PHÂN QUYỀN:

- [ ] Guest chỉ xem được `/routes` (cảnh báo đã duyệt)
- [ ] Guest không vào được `/alerts` (redirect login)
- [ ] User xem được `/alerts` (cảnh báo của mình)
- [ ] User không xem được cảnh báo của người khác
- [ ] Admin xem được tất cả cảnh báo
- [ ] Admin duyệt/từ chối được
- [ ] API có middleware auth đúng
- [ ] Frontend có route guard đúng

---

**BẠN MUỐN TÔI SỬA THEO DOCUMENT NÀY KHÔNG?**
