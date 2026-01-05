# Traffic Alert Detection Service

FastAPI service for detecting traffic conditions and flooding from images.

## Features

- Image classification (traffic jam / flooding detection)
- Integration with Laravel backend
- Model: YOLOv8 or custom CNN model

## Setup

```bash
pip install -r requirements.txt
python main.py
```

## API Endpoints

- `POST /detect` - Detect traffic condition from image
- `GET /health` - Health check
