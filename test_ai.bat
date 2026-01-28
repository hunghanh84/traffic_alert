@echo off
echo ========================================
echo   TRAFFIC ALERT - TEST DETECTION
echo ========================================
echo.
echo Running 1-minute detection test...
echo.

cd ai-detection
python test_detection.py

echo.
echo Test complete!
pause
