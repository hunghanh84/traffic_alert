<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header .emoji {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .feature-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .feature-box h3 {
            margin: 0 0 15px 0;
            color: #667eea;
            font-size: 18px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .feature-list li {
            padding: 8px 0;
            color: #555;
        }
        .feature-list li:before {
            content: "✓ ";
            color: #10b981;
            font-weight: bold;
            margin-right: 8px;
        }
        .telegram-box {
            background: linear-gradient(135deg, #0088cc 0%, #00a0e9 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
        }
        .telegram-box h3 {
            margin: 0 0 15px 0;
            font-size: 22px;
        }
        .telegram-box p {
            margin: 0 0 20px 0;
            opacity: 0.95;
            font-size: 15px;
        }
        .telegram-btn {
            display: inline-block;
            background: white;
            color: #0088cc;
            padding: 15px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .telegram-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        .telegram-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="emoji">🎉</div>
            <h1>Chào mừng bạn đến với Traffic Alert!</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Xin chào <strong>{{ $userName }}</strong>,</p>
            
            <p class="message">
                Cảm ơn bạn đã hoàn tất quá trình đăng ký! Tài khoản của bạn đã được kích hoạt thành công. 
                Chúng tôi rất vui mừng được đồng hành cùng bạn trong việc cập nhật thông tin giao thông.
            </p>

            <div class="feature-box">
                <h3>🚀 Bạn có thể làm gì với Traffic Alert?</h3>
                <ul class="feature-list">
                    <li>Nhận cảnh báo giao thông theo thời gian thực</li>
                    <li>Xem bản đồ tình trạng giao thông trực quan</li>
                    <li>Báo cáo sự cố giao thông trong khu vực</li>
                    <li>Theo dõi tin tức giao thông mới nhất</li>
                    <li>Lên kế hoạch di chuyển thông minh hơn</li>
                </ul>
            </div>

            <div class="telegram-box">
                <div class="telegram-icon">📱</div>
                <h3>Nhận thông báo qua Telegram</h3>
                <p>
                    Đăng ký nhận thông báo cảnh báo giao thông ngay trên Telegram để không bỏ lỡ 
                    bất kỳ thông tin quan trọng nào!
                </p>
                <a href="{{ $telegramLink }}" class="telegram-btn">
                    🔔 Kết nối Telegram
                </a>
            </div>

            <p class="message">
                Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi. 
                Chúc bạn có những trải nghiệm tuyệt vời!
            </p>
        </div>
        
        <div class="footer">
            <p><strong>Traffic Alert System</strong></p>
            <p>© 2026 Traffic Alert. All rights reserved.</p>
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>
