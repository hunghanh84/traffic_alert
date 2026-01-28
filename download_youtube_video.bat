@echo off
echo ========================================
echo   DOWNLOAD VIDEO FROM YOUTUBE
echo ========================================
echo.

set /p URL="Enter YouTube URL: "
set /p DURATION="Duration in minutes (default 2): "

if "%DURATION%"=="" set DURATION=2

echo.
echo Downloading %DURATION% minutes from: %URL%
echo.

cd ai-detection
if not exist videos mkdir videos

yt-dlp -f "best[height<=720]" -o "videos/downloaded_video.mp4" "%URL%" --download-sections "*0:00-%DURATION%:00"

echo.
echo ========================================
echo   DOWNLOAD COMPLETE!
echo ========================================
echo.
echo Video saved to: ai-detection\videos\downloaded_video.mp4
echo.
pause
