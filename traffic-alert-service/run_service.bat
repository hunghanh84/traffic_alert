@echo off
echo Starting Traffic Alert Detection Service...
echo.

if not exist "venv" (
    echo ERROR: Virtual environment not found!
    echo Please run install.bat first.
    pause
    exit /b 1
)

call venv\Scripts\activate.bat
python main.py
