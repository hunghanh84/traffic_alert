<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã xác thực</title>
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
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .code-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            letter-spacing: 8px;
            margin: 30px 0;
        }
        .message {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚦 Traffic Alert System</h1>
        </div>
        <div class="content">
            <p class="greeting">Xin chào <strong>{{ $userName }}</strong>,</p>
            
            <p class="message">
                Cảm ơn bạn đã đăng ký tài khoản tại <strong>Traffic Alert System</strong>. 
                Để hoàn tất quá trình đăng ký, vui lòng sử dụng mã xác thực bên dưới:
            </p>
            
            <div class="code-box">
                {{ $code }}
            </div>
            
            <p class="message">
                Mã xác thực này có hiệu lực trong <strong>10 phút</strong>.
            </p>
            
            <div class="warning">
                ⚠️ <strong>Lưu ý:</strong> Nếu bạn không yêu cầu đăng ký tài khoản, 
                vui lòng bỏ qua email này.
            </div>
        </div>
        <div class="footer">
            <p>© 2026 Traffic Alert System. All rights reserved.</p>
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>
