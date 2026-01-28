# Traffic Alert - AI Detection Service

## 🚀 Hướng dẫn chạy

### 1. Cài đặt Python dependencies

```bash
cd ai-detection
pip install -r requirements.txt
```

### 2. Cài đặt yt-dlp (để đọc YouTube stream)

```bash
pip install yt-dlp
```

### 3. Cấu hình

Mở file `.env` và chỉnh sửa:

- `YOUTUBE_URL`: Link YouTube livestream
- `API_URL`: URL API Laravel (mặc định: http://127.0.0.1:8000/api/ai/detect)
- `CONFIDENCE_THRESHOLD`: Ngưỡng tin cậy (0.0-1.0, mặc định: 0.7)

### 4. Chạy detection service

```bash
python detect_stream.py
```

### 5. Dừng service

Nhấn `Q` hoặc `Ctrl+C`

---

## 📋 Tính năng

- ✅ Đọc livestream từ YouTube
- ✅ Detect với YOLO11 (model đã train)
- ✅ Gửi kết quả lên Laravel API
- ✅ Hiển thị real-time detection
- ✅ Auto-reconnect khi stream bị ngắt
- ✅ Skip frames để tăng tốc độ
- ✅ Chống spam detection (interval 5s)

---

## ⚙️ Cấu hình nâng cao

### `.env` file:

```env
# API Configuration
API_URL=http://127.0.0.1:8000/api/ai/detect
API_TOKEN=your_api_token_here

# YouTube Stream
YOUTUBE_URL=https://www.youtube.com/watch?v=WUFEgl1Hyb8

# Model Configuration
MODEL_PATH=../MODEL/best.pt
CONFIDENCE_THRESHOLD=0.7

# Detection Settings
FRAME_SKIP=30              # Xử lý 1 frame/30 frames (tăng tốc độ)
DETECTION_INTERVAL=5       # Gửi API mỗi 5s/loại detection

# Display Settings
SHOW_WINDOW=true           # Hiển thị cửa sổ detection
WINDOW_WIDTH=1280
WINDOW_HEIGHT=720
```

---

## 🔧 Troubleshooting

### Lỗi: "Cannot get stream URL"

- Kiểm tra link YouTube có đúng không
- Kiểm tra internet connection
- Thử cài lại yt-dlp: `pip install --upgrade yt-dlp`

### Lỗi: "Cannot open video stream"

- Stream có thể đang offline
- Thử link YouTube khác
- Kiểm tra firewall

### Lỗi: "Model not found"

- Kiểm tra đường dẫn MODEL_PATH trong .env
- Đảm bảo file best.pt tồn tại trong thư mục MODEL/

---

## 📊 Output

Service sẽ:

1. Hiển thị cửa sổ với detection boxes
2. In ra console mỗi khi detect và gửi API
3. Tự động gửi kết quả lên Laravel API

Ví dụ output:

```
✅ Sent to API: traffic_jam (0.85)
✅ Sent to API: accident (0.92)
```

---

## 🎯 Demo cho thầy

1. Mở Laravel server: `php artisan serve`
2. Chạy detection: `python detect_stream.py`
3. Mở browser: http://localhost:8000/admin/ai-results
4. Xem kết quả real-time! 🎉
