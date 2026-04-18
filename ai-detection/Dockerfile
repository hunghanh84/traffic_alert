FROM python:3.10-slim

# Set working directory
WORKDIR /app

# Install system dependencies for OpenCV + YOLO + yt-dlp
RUN apt-get update && apt-get install -y --no-install-recommends \
    libglib2.0-0 \
    libsm6 \
    libxext6 \
    libxrender-dev \
    libgomp1 \
    ffmpeg \
    wget \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements first (layer cache)
COPY requirements.txt requirements_api.txt ./

# Install Python dependencies
RUN pip install --no-cache-dir --upgrade pip \
    && pip install --no-cache-dir -r requirements_api.txt \
    && pip install --no-cache-dir flask flask-cors python-dotenv \
    && pip install --no-cache-dir -r requirements.txt

# Copy application source
COPY . .

# Models directory (will be mounted via volume)
RUN mkdir -p /app/MODEL

EXPOSE 5000

CMD ["python", "yolo_api.py"]
