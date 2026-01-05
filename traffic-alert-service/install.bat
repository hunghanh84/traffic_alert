@echo off
echo ========================================
echo Installing Traffic Alert Detection Service
echo ========================================
echo.

REM Check if virtual environment exists
if not exist "venv" (
    echo Creating virtual environment...
    python -m venv venv
    echo.
)

echo Activating virtual environment...
call venv\Scripts\activate.bat
echo.

echo Installing dependencies...
python -m pip install --upgrade pip
pip install -r requirements.txt
echo.

echo ========================================
echo Installation complete!
echo ========================================
echo.
echo To run the service:
echo   1. venv\Scripts\activate
echo   2. python main.py
echo.
echo Or simply run: run_service.bat
echo.
pause
