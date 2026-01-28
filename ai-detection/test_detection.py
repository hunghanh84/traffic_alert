"""
Traffic Alert AI Detection Service - Lightweight Version
Test 1 phút, in kết quả ra console
"""

import cv2
import time
import subprocess
from ultralytics import YOLO
from datetime import datetime
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

class TrafficDetectionService:
    def __init__(self):
        # Config
        self.youtube_url = os.getenv('YOUTUBE_URL')
        self.model_path = os.getenv('MODEL_PATH', '../MODEL/best.pt')
        self.confidence_threshold = float(os.getenv('CONFIDENCE_THRESHOLD', 0.7))
        
        # Label mapping
        self.label_map = {
            0: 'Heavy Traffic',
            1: 'Light Traffic',
            2: 'Flooding'
        }
        
        # Load YOLO model
        print("=" * 60)
        print("🚀 TRAFFIC ALERT - AI DETECTION SERVICE")
        print("=" * 60)
        print(f"📦 Loading YOLO11 model: {self.model_path}")
        self.model = YOLO(self.model_path)
        print("✅ Model loaded!")
        print()
        
        # Stats
        self.detections = {
            'Heavy Traffic': 0,
            'Light Traffic': 0,
            'Flooding': 0
        }
        self.total_frames = 0
        self.start_time = None
        
    def get_stream_url(self):
        """Lấy URL stream từ YouTube"""
        print(f"🔗 Getting stream from: {self.youtube_url}")
        try:
            cmd = [
                'yt-dlp',
                '-f', 'best[height<=480]',  # 480p cho nhẹ
                '-g',
                self.youtube_url
            ]
            
            result = subprocess.run(cmd, capture_output=True, text=True)
            stream_url = result.stdout.strip()
            
            if stream_url:
                print("✅ Stream URL obtained!")
                return stream_url
            else:
                print("❌ Failed to get stream URL")
                return None
                
        except Exception as e:
            print(f"❌ Error: {e}")
            return None
    
    def print_detection(self, label, confidence):
        """In kết quả detection"""
        timestamp = datetime.now().strftime("%H:%M:%S")
        bar = "█" * int(confidence * 20)
        
        # Icon cho mỗi loại
        icons = {
            'Heavy Traffic': '🚗🚗🚗',
            'Light Traffic': '🚗',
            'Flooding': '🌊'
        }
        
        icon = icons.get(label, '⚠️')
        
        print(f"[{timestamp}] {icon} {label:15} | Confidence: {bar} {confidence:.1%}")
        
        # Update stats
        self.detections[label] += 1
    
    def print_summary(self):
        """In tổng kết"""
        elapsed = time.time() - self.start_time
        
        print()
        print("=" * 60)
        print("📊 DETECTION SUMMARY")
        print("=" * 60)
        print(f"⏱️  Duration: {elapsed:.1f}s")
        print(f"🎬 Total Frames Processed: {self.total_frames}")
        print(f"⚡ FPS: {self.total_frames / elapsed:.1f}")
        print()
        print("🎯 Detections:")
        
        total = sum(self.detections.values())
        for label, count in self.detections.items():
            if total > 0:
                percentage = (count / total) * 100
                bar = "█" * int(percentage / 5)
                print(f"  {label:15}: {count:3} ({percentage:5.1f}%) {bar}")
            else:
                print(f"  {label:15}: {count:3}")
        
        print("=" * 60)
    
    def run(self, duration_seconds=60):
        """Chạy detection trong thời gian giới hạn"""
        print()
        print("🎯 Configuration:")
        print(f"  - Duration: {duration_seconds}s")
        print(f"  - Confidence: {self.confidence_threshold}")
        print(f"  - Labels: {', '.join(self.label_map.values())}")
        print()
        print("-" * 60)
        
        # Get stream URL
        stream_url = self.get_stream_url()
        if not stream_url:
            print("❌ Cannot get stream. Exiting...")
            return
        
        # Open stream
        print("📹 Opening stream...")
        cap = cv2.VideoCapture(stream_url)
        
        if not cap.isOpened():
            print("❌ Cannot open stream. Exiting...")
            return
        
        print("✅ Stream opened!")
        print()
        print("🎬 Starting detection...")
        print("-" * 60)
        
        self.start_time = time.time()
        
        try:
            while True:
                # Check time limit
                elapsed = time.time() - self.start_time
                if elapsed >= duration_seconds:
                    print()
                    print(f"⏰ Time's up! ({duration_seconds}s)")
                    break
                
                # Read frame
                ret, frame = cap.read()
                if not ret:
                    print("⚠️  Stream error. Stopping...")
                    break
                
                self.total_frames += 1
                
                # Skip frames (process every 30th frame)
                if self.total_frames % 30 != 0:
                    continue
                
                # Run detection
                results = self.model(frame, conf=self.confidence_threshold, verbose=False)
                
                # Process results
                for result in results:
                    boxes = result.boxes
                    
                    for box in boxes:
                        cls_id = int(box.cls[0])
                        confidence = float(box.conf[0])
                        
                        # Get label
                        label = self.label_map.get(cls_id, f'Unknown_{cls_id}')
                        
                        # Print detection
                        self.print_detection(label, confidence)
                        
        except KeyboardInterrupt:
            print("\n⚠️  Stopped by user")
        except Exception as e:
            print(f"\n❌ Error: {e}")
        finally:
            cap.release()
            self.print_summary()

if __name__ == "__main__":
    service = TrafficDetectionService()
    service.run(duration_seconds=60)  # Test 1 phút
