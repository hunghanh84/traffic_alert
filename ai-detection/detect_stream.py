"""
Traffic Alert AI Detection Service
Đọc livestream từ YouTube và detect giao thông với YOLO11
"""

import cv2
import time
import requests
import subprocess
from ultralytics import YOLO
from datetime import datetime
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

class TrafficDetectionService:
    def __init__(self):
        # Load config
        self.api_url = os.getenv('API_URL', 'http://127.0.0.1:8000/api/ai/detect')
        self.youtube_url = os.getenv('YOUTUBE_URL')
        self.model_path = os.getenv('MODEL_PATH', '../MODEL/best.pt')
        self.confidence_threshold = float(os.getenv('CONFIDENCE_THRESHOLD', 0.7))
        self.frame_skip = int(os.getenv('FRAME_SKIP', 30))
        self.detection_interval = int(os.getenv('DETECTION_INTERVAL', 5))
        self.show_window = os.getenv('SHOW_WINDOW', 'true').lower() == 'true'
        
        # Load YOLO model
        print(f"📦 Loading YOLO11 model from {self.model_path}...")
        self.model = YOLO(self.model_path)
        print("✅ Model loaded successfully!")
        
        # Detection state
        self.last_detection_time = {}
        self.frame_count = 0
        
    def get_stream_url(self):
        """Lấy URL stream từ YouTube"""
        print(f"🔗 Getting stream URL from YouTube...")
        try:
            # Dùng yt-dlp để lấy URL stream
            cmd = [
                'yt-dlp',
                '-f', 'best[height<=720]',  # Chọn quality 720p
                '-g',  # Chỉ lấy URL
                self.youtube_url
            ]
            
            result = subprocess.run(cmd, capture_output=True, text=True)
            stream_url = result.stdout.strip()
            
            if stream_url:
                print(f"✅ Stream URL obtained!")
                return stream_url
            else:
                print(f"❌ Failed to get stream URL")
                return None
                
        except Exception as e:
            print(f"❌ Error: {e}")
            return None
    
    def send_to_api(self, label, confidence, frame):
        """Gửi kết quả detection lên Laravel API"""
        try:
            # Tạo unique key cho mỗi loại detection
            current_time = time.time()
            
            # Chỉ gửi nếu đã qua interval time
            if label in self.last_detection_time:
                if current_time - self.last_detection_time[label] < self.detection_interval:
                    return
            
            # Update last detection time
            self.last_detection_time[label] = current_time
            
            # Prepare data
            data = {
                'nhan': label,
                'do_tin_cay': float(confidence),
                'timestamp': datetime.now().isoformat(),
                'source': 'youtube_livestream'
            }
            
            # Send to API
            response = requests.post(self.api_url, json=data, timeout=5)
            
            if response.status_code == 200:
                print(f"✅ Sent to API: {label} ({confidence:.2%})")
            else:
                print(f"⚠️  API Error: {response.status_code}")
                
        except Exception as e:
            print(f"❌ Error sending to API: {e}")
    
    def process_frame(self, frame):
        """Xử lý frame với YOLO11"""
        # Run detection
        results = self.model(frame, conf=self.confidence_threshold, verbose=False)
        
        # Process results
        for result in results:
            boxes = result.boxes
            
            for box in boxes:
                # Get detection info
                cls_id = int(box.cls[0])
                confidence = float(box.conf[0])
                label = result.names[cls_id]
                
                # Send to API
                self.send_to_api(label, confidence, frame)
        
        # Return annotated frame
        if len(results) > 0:
            return results[0].plot()
        return frame
    
    def run(self):
        """Chạy detection service"""
        print("🚀 Starting Traffic Detection Service...")
        print(f"📺 YouTube URL: {self.youtube_url}")
        print(f"🎯 Confidence Threshold: {self.confidence_threshold}")
        print(f"⏱️  Detection Interval: {self.detection_interval}s")
        print("-" * 60)
        
        # Get stream URL
        stream_url = self.get_stream_url()
        if not stream_url:
            print("❌ Cannot get stream URL. Exiting...")
            return
        
        # Open video stream
        print("📹 Opening video stream...")
        cap = cv2.VideoCapture(stream_url)
        
        if not cap.isOpened():
            print("❌ Cannot open video stream. Exiting...")
            return
        
        print("✅ Stream opened successfully!")
        print("🎬 Starting detection... (Press 'q' to quit)")
        print("-" * 60)
        
        try:
            while True:
                ret, frame = cap.read()
                
                if not ret:
                    print("⚠️  Stream ended or error. Reconnecting...")
                    time.sleep(5)
                    stream_url = self.get_stream_url()
                    cap = cv2.VideoCapture(stream_url)
                    continue
                
                self.frame_count += 1
                
                # Skip frames để tăng tốc độ
                if self.frame_count % self.frame_skip != 0:
                    continue
                
                # Process frame
                annotated_frame = self.process_frame(frame)
                
                # Show window
                if self.show_window:
                    # Resize for display
                    display_frame = cv2.resize(annotated_frame, (1280, 720))
                    
                    # Add info text
                    cv2.putText(display_frame, f"Frame: {self.frame_count}", 
                               (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
                    cv2.putText(display_frame, f"Detections: {len(self.last_detection_time)}", 
                               (10, 70), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
                    
                    cv2.imshow('Traffic Detection - Press Q to quit', display_frame)
                
                # Check for quit
                if cv2.waitKey(1) & 0xFF == ord('q'):
                    print("\n👋 Stopping detection service...")
                    break
                    
        except KeyboardInterrupt:
            print("\n👋 Stopped by user")
        except Exception as e:
            print(f"\n❌ Error: {e}")
        finally:
            cap.release()
            cv2.destroyAllWindows()
            print("✅ Service stopped")

if __name__ == "__main__":
    service = TrafficDetectionService()
    service.run()
