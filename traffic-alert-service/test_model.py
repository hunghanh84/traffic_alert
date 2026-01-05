"""
Test script to debug model predictions
Run: python test_model.py
"""

from PIL import Image
import numpy as np
from pathlib import Path

# TensorFlow imports
try:
    import tensorflow as tf
    from tensorflow.keras.models import load_model
    TF_AVAILABLE = True
except Exception as e:
    print(f"TensorFlow not available: {e}")
    TF_AVAILABLE = False
    exit(1)

# Model configuration
CLASS_LABELS = ['TrafficJam', 'Flooding', 'Normal']
LABEL_MAPPING = {
    'TrafficJam': 'traffic',
    'Flooding': 'flood',
    'Normal': 'normal'
}

def preprocess_image(image: Image.Image, target_size=(224, 224)) -> np.ndarray:
    """Preprocess image for MobileNet model"""
    # Convert to RGB
    image = image.convert('RGB')
    
    # Resize
    image = image.resize(target_size)
    
    # Convert to array
    arr = np.array(image, dtype=np.float32)
    
    # Add batch dimension
    arr = np.expand_dims(arr, axis=0)
    
    # MobileNet preprocessing
    arr = tf.keras.applications.mobilenet.preprocess_input(arr)
    
    return arr

def test_image(image_path: str):
    """Test single image"""
    print(f"\n{'='*60}")
    print(f"Testing: {image_path}")
    print(f"{'='*60}")
    
    # Load model
    model_path = Path("models/train_detection_model.keras")
    if not model_path.exists():
        print(f"❌ Model not found: {model_path}")
        return
    
    print(f"📂 Loading model from {model_path}...")
    model = load_model(str(model_path))
    print(f"✅ Model loaded successfully!")
    print()
    
    # Load image
    if not Path(image_path).exists():
        print(f"❌ Image not found: {image_path}")
        return
    
    image = Image.open(image_path)
    print(f"📷 Image loaded: {image.size}, mode: {image.mode}")
    
    # Preprocess
    x = preprocess_image(image)
    print(f"🔧 Preprocessed shape: {x.shape}")
    print()
    
    # Predict
    print("🤖 Running prediction...")
    preds = model.predict(x, verbose=0)
    probs = preds[0]
    
    print(f"📊 Raw probabilities: {probs}")
    print()
    
    # Get prediction
    idx = int(np.argmax(probs))
    label = CLASS_LABELS[idx] if idx < len(CLASS_LABELS) else str(idx)
    confidence = float(probs[idx])
    
    print(f"🎯 Results:")
    print(f"   Predicted index: {idx}")
    print(f"   Predicted label: {label}")
    print(f"   Confidence: {confidence:.4f}")
    print()
    
    # Map to alert type
    alert_type = LABEL_MAPPING.get(label, 'normal')
    print(f"   Alert type: {alert_type}")
    print()
    
    # All probabilities
    print(f"📈 All probabilities:")
    for i, class_label in enumerate(CLASS_LABELS):
        print(f"   {class_label:15s}: {probs[i]:.6f} ({probs[i]*100:.2f}%)")
    
    print(f"{'='*60}\n")

if __name__ == "__main__":
    import sys
    
    # Test images
    test_images = [
        "evaluate/91.jpg",      # Should be Flooding
        "evaluate/1406.jpg",    # Should be TrafficJam
    ]
    
    # If image path provided as argument
    if len(sys.argv) > 1:
        test_images = [sys.argv[1]]
    
    for img_path in test_images:
        test_image(img_path)
