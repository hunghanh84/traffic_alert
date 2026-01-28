@echo off
echo ========================================
echo   Traffic Alert - AI Detection Setup
echo ========================================
echo.

echo [1/3] Installing Python dependencies...
cd ai-detection
pip install -r requirements.txt

echo.
echo [2/3] Installing yt-dlp...
pip install yt-dlp

echo.
echo [3/3] Setup complete!
echo.
echo ========================================
echo   Ready to run!
echo ========================================
echo.
echo To start detection:
echo   cd ai-detection
echo   python detect_stream.py
echo.
pause
