"""
Flask API for YOLO Verification
Nhận request từ Laravel, verify với camera stream
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import cv2
import subprocess
from ultralytics import YOLO
import os
from dotenv import load_dotenv

load_dotenv()

app = Flask(__name__)
CORS(app)

# Load YOLO model
MODEL_PATH = os.getenv('MODEL_PATH', '../MODEL/best.pt')
model = YOLO(MODEL_PATH)

# Label mapping
LABEL_MAP = {
    0: 'Heavy Traffic',
    1: 'Light Traffic',
    2: 'Flooding'
}

def get_stream_url(youtube_url):
    """Lấy URL stream từ YouTube"""
    try:
        cmd = ['yt-dlp', '-f', 'best[height<=480]', '-g', youtube_url]
        result = subprocess.run(cmd, capture_output=True, text=True)
        return result.stdout.strip()
    except Exception as e:
        print(f"Error getting stream URL: {e}")
        return None

def verify_with_stream(stream_url, expected_label, duration=30):
    """
    Verify alert với camera stream
    
    Args:
        stream_url: URL của stream (YouTube hoặc local video)
        expected_label: Label mong đợi từ MobileNet
        duration: Thời gian quét (giây)
    
    Returns:
        {
            'detected_label': str,
            'confidence': float,
            'match': bool,
            'detections': int
        }
    """
    print(f"[DEBUG] Opening stream: {stream_url[:50]}...")
    
    # Normalize path for Windows
    if stream_url.startswith('D:/') or stream_url.startswith('D:\\'):
        # Convert to relative path from ai-detection folder
        stream_url = stream_url.replace('D:/php/DATT_AI/ai-detection/', '')
        stream_url = stream_url.replace('D:\\php\\DATT_AI\\ai-detection\\', '')
        print(f"[DEBUG] Normalized to relative path: {stream_url}")
    
    cap = cv2.VideoCapture(stream_url)
    
    if not cap.isOpened():
        print(f"[ERROR] Failed to open stream: {stream_url}")
        return None
    
    print(f"[DEBUG] Stream opened successfully")
    print(f"[DEBUG] Expected label: {expected_label}")
    print(f"[DEBUG] Duration: {duration}s")
    
    detections = {}
    frame_count = 0
    processed_frames = 0
    max_frames = duration * 30  # Giả sử 30 FPS
    
    try:
        while frame_count < max_frames:
            ret, frame = cap.read()
            if not ret:
                print(f"[DEBUG] End of stream at frame {frame_count}")
                break
            
            frame_count += 1
            
            # Skip frames (process every 30th frame)
            if frame_count % 30 != 0:
                continue
            
            processed_frames += 1
            print(f"[DEBUG] Processing frame {frame_count} ({processed_frames} processed)...")
            
            # YOLO detect - Giảm confidence xuống 0.5 để dễ detect hơn
            results = model(frame, conf=0.5, verbose=False)
            
            for result in results:
                for box in result.boxes:
                    cls_id = int(box.cls[0])
                    confidence = float(box.conf[0])
                    label = LABEL_MAP.get(cls_id, f'Unknown_{cls_id}')
                    
                    print(f"[DETECT] {label} ({confidence:.2%})")
                    
                    # Count detections
                    if label not in detections:
                        detections[label] = {'count': 0, 'max_conf': 0}
                    
                    detections[label]['count'] += 1
                    detections[label]['max_conf'] = max(
                        detections[label]['max_conf'], 
                        confidence
                    )
        
        print(f"[DEBUG] Processed {processed_frames} frames total")
        print(f"[DEBUG] Detections: {detections}")
        
    finally:
        cap.release()
    
    # Determine result
    if not detections:
        print("[RESULT] No detections found")
        return {
            'detected_label': None,
            'confidence': 0,
            'match': False,
            'detections': 0
        }
    
    # Get most detected label
    most_detected = max(detections.items(), key=lambda x: x[1]['count'])
    detected_label = most_detected[0]
    max_confidence = most_detected[1]['max_conf']
    
    print(f"[RESULT] Most detected: {detected_label} ({max_confidence:.2%})")
    print(f"[RESULT] Match: {detected_label == expected_label}")
    
    return {
        'detected_label': detected_label,
        'confidence': max_confidence,
        'match': detected_label == expected_label,
        'detections': most_detected[1]['count'],
        'all_detections': detections
    }

@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint"""
    return jsonify({
        'status': 'ok',
        'model_loaded': model is not None
    })

@app.route('/verify', methods=['POST'])
def verify():
    """
    Verify alert với YOLO
    
    Request:
    {
        "stream_url": "https://youtube.com/...",
        "expected_label": "Heavy Traffic",
        "duration": 30
    }
    
    Response:
    {
        "success": true,
        "result": {
            "detected_label": "Heavy Traffic",
            "confidence": 0.85,
            "match": true,
            "detections": 5
        }
    }
    """
    try:
        data = request.json
        
        stream_url = data.get('stream_url')
        expected_label = data.get('expected_label')
        duration = data.get('duration', 30)
        
        if not stream_url or not expected_label:
            return jsonify({
                'success': False,
                'error': 'Missing stream_url or expected_label'
            }), 400
        
        # Get actual stream URL if YouTube
        if 'youtube.com' in stream_url or 'youtu.be' in stream_url:
            actual_url = get_stream_url(stream_url)
            if not actual_url:
                return jsonify({
                    'success': False,
                    'error': 'Failed to get stream URL'
                }), 500
        else:
            actual_url = stream_url
        
        # Verify
        result = verify_with_stream(actual_url, expected_label, duration)
        
        if result is None:
            return jsonify({
                'success': False,
                'error': 'Failed to open stream'
            }), 500
        
        return jsonify({
            'success': True,
            'result': result
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

if __name__ == '__main__':
    print("=" * 60)
    print("🚀 YOLO Verification API")
    print("=" * 60)
    print(f"📦 Model: {MODEL_PATH}")
    print(f"🎯 Labels: {list(LABEL_MAP.values())}")
    print("🌐 Server: http://localhost:5000")
    print("=" * 60)
    
    app.run(host='0.0.0.0', port=5000, debug=True)
