@echo off
echo ========================================
echo   DOWNLOAD SHORT CAMERA CLIPS (2 MIN)
echo ========================================
echo.

cd ai-detection
if not exist videos mkdir videos

echo [1/5] Downloading Camera 1 (2 minutes)...
yt-dlp -f "best[height<=480]" -o "videos/cam001.mp4" "https://www.youtube.com/watch?v=WUFEgl1Hyb8" --download-sections "*0:00-2:00"

echo.
echo [2/5] Downloading Camera 2 (2 minutes)...
yt-dlp -f "best[height<=480]" -o "videos/cam002.mp4" "https://www.youtube.com/watch?v=sECNGJvGpwA" --download-sections "*0:00-2:00"

echo.
echo [3/5] Downloading Camera 3 (2 minutes)...
yt-dlp -f "best[height<=480]" -o "videos/cam003.mp4" "https://www.youtube.com/watch?v=oNbdqkEozX0" --download-sections "*0:00-2:00"

echo.
echo [4/5] Downloading Camera 4 (2 minutes)...
yt-dlp -f "best[height<=480]" -o "videos/cam004.mp4" "https://www.youtube.com/watch?v=muijHPW82vI" --download-sections "*0:00-2:00"

echo.
echo [5/6] Downloading Camera 5 (2 minutes)...
yt-dlp -f "best[height<=480]" -o "videos/cam005.mp4" "https://www.youtube.com/watch?v=xCNRP131kNY" --download-sections "*0:00-2:00"

echo.
echo [6/7] Downloading Camera 6 (2 minutes)...
yt-dlp -f "best[height<=480]" -o "videos/cam006.mp4" "https://www.youtube.com/watch?v=GNUDXF-f5QU" --download-sections "*15:00-17:00"

echo.
echo [7/7] Downloading Camera 7 (YouTube Shorts - full)...
yt-dlp -f "best[height<=480]" -o "videos/cam007.mp4" "https://www.youtube.com/shorts/W0ZxyHbd9Nc"

echo.
echo ========================================
echo   COMPLETE! Total size: ~250MB
echo ========================================
echo.
echo Videos: ai-detection\videos\
echo Duration: 2 minutes each (cam007: full short)
echo Quality: 480p (enough for detection)
echo Total: 7 cameras
echo.
pause
