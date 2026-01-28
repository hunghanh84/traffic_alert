"""
Test YOLO detection với video local
"""

import cv2
from ultralytics import YOLO
import os

# Load model
MODEL_PATH = '../MODEL/best.pt'
VIDEO_PATH = 'videos/camera6.mp4'

print("=" * 60)
print("🎥 YOLO Detection - Local Video")
print("=" * 60)
print(f"📦 Model: {MODEL_PATH}")
print(f"🎬 Video: {VIDEO_PATH}")
print("=" * 60)

if not os.path.exists(VIDEO_PATH):
    print(f"❌ Video not found: {VIDEO_PATH}")
    print("Please download a video first using download_youtube_video.bat")
    exit(1)

model = YOLO(MODEL_PATH)
print("✅ Model loaded!")

# Label mapping
LABEL_MAP = {
    0: 'Heavy Traffic',
    1: 'Light Traffic',
    2: 'Flooding'
}

# Open video
cap = cv2.VideoCapture(VIDEO_PATH)

if not cap.isOpened():
    print("❌ Cannot open video!")
    exit(1)

print("✅ Video opened!")
print("\n🎬 Starting detection...\n")

detections = {}
frame_count = 0
processed = 0

while True:
    ret, frame = cap.read()
    if not ret:
        break
    
    frame_count += 1
    
    # Process every 30th frame
    if frame_count % 30 != 0:
        continue
    
    processed += 1
    
    # YOLO detect
    results = model(frame, conf=0.5, verbose=False)
    
    for result in results:
        for box in result.boxes:
            cls_id = int(box.cls[0])
            confidence = float(box.conf[0])
            label = LABEL_MAP.get(cls_id, f'Unknown_{cls_id}')
            
            print(f"[Frame {frame_count}] {label} - {confidence:.1%}")
            
            if label not in detections:
                detections[label] = {'count': 0, 'max_conf': 0}
            
            detections[label]['count'] += 1
            detections[label]['max_conf'] = max(detections[label]['max_conf'], confidence)

cap.release()

# Summary
print("\n" + "=" * 60)
print("📊 DETECTION SUMMARY")
print("=" * 60)
print(f"🎬 Total Frames: {frame_count}")
print(f"⚡ Processed: {processed}")
print("\n🎯 Detections:")

if detections:
    total = sum(d['count'] for d in detections.values())
    for label, data in detections.items():
        percentage = (data['count'] / total) * 100
        print(f"  {label:15}: {data['count']:3} ({percentage:5.1f}%) - Max conf: {data['max_conf']:.1%}")
else:
    print("  No detections found")

print("=" * 60)
