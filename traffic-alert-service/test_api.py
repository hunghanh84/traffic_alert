"""
Test script for FastAPI detection service
"""

import requests
import json
from pathlib import Path

# Service URL
BASE_URL = "http://localhost:8001"

def test_health_check():
    """Test health check endpoint"""
    print("Testing health check...")
    response = requests.get(f"{BASE_URL}/health")
    print(f"Status: {response.status_code}")
    print(f"Response: {response.json()}")
    print()

def test_detect_image(image_path: str):
    """Test image detection"""
    print(f"Testing detection with image: {image_path}")
    
    if not Path(image_path).exists():
        print(f"Error: Image file not found: {image_path}")
        return
    
    with open(image_path, "rb") as f:
        files = {"file": (Path(image_path).name, f, "image/jpeg")}
        response = requests.post(f"{BASE_URL}/detect", files=files)
    
    print(f"Status: {response.status_code}")
    print(f"Response: {json.dumps(response.json(), indent=2)}")
    print()

def test_batch_detect(image_paths: list):
    """Test batch detection"""
    print(f"Testing batch detection with {len(image_paths)} images...")
    
    files = []
    for path in image_paths:
        if Path(path).exists():
            files.append(("files", (Path(path).name, open(path, "rb"), "image/jpeg")))
    
    if not files:
        print("Error: No valid image files found")
        return
    
    response = requests.post(f"{BASE_URL}/batch-detect", files=files)
    
    # Close file handles
    for _, (_, f, _) in files:
        f.close()
    
    print(f"Status: {response.status_code}")
    print(f"Response: {json.dumps(response.json(), indent=2)}")
    print()

if __name__ == "__main__":
    # Test health check
    test_health_check()
    
    # Test single image detection
    # test_detect_image("test_images/traffic_jam.jpg")
    # test_detect_image("test_images/flood.jpg")
    
    # Test batch detection
    # test_batch_detect([
    #     "test_images/traffic_jam.jpg",
    #     "test_images/flood.jpg",
    #     "test_images/normal.jpg"
    # ])
    
    print("Tests completed!")
