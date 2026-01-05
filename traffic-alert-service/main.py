from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional
import uvicorn
import io
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

app = FastAPI(
    title="Traffic Alert Detection Service",
    description="AI service for detecting traffic conditions and flooding from images using TensorFlow/Keras",
    version="1.0.0"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Response models
class DetectionResult(BaseModel):
    success: bool
    prediction: str
    confidence: float
    all_probabilities: Optional[dict] = None

class HealthResponse(BaseModel):
    status: str
    model_loaded: bool
    tensorflow_available: bool

# Global model variable
model = None
# Model output order: [TrafficJam, Flooding, Normal]
CLASS_LABELS = ['TrafficJam', 'Flooding', 'Normal']

# Mapping to our alert types
LABEL_MAPPING = {
    'TrafficJam': 'traffic',
    'Flooding': 'flood',
    'Normal': 'normal'
}

def preprocess_image(image: Image.Image, target_size=(224, 224)) -> np.ndarray:
    """
    Preprocess image for MobileNet model
    """
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

def load_detection_model():
    """Load the trained Keras model"""
    global model
    
    if not TF_AVAILABLE:
        print("TensorFlow is not available. Model cannot be loaded.")
        return False
    
    try:
        model_path = Path("models/train_detection_model.keras")
        
        if not model_path.exists():
            print(f"Model file not found: {model_path}")
            print("Using mock predictions for demo")
            return False
        
        print(f"Loading model from {model_path}...")
        model = load_model(str(model_path))
        print("Model loaded successfully!")
        return True
        
    except Exception as e:
        print(f"Error loading model: {e}")
        print("Using mock predictions for demo")
        return False

def predict_with_model(image: Image.Image) -> dict:
    """
    Predict traffic condition using loaded model
    """
    if model is None:
        # Mock prediction for demo
        print("⚠️  Model is None, using mock prediction")
        return mock_prediction(image)
    
    try:
        print(f"🤖 Using real model for prediction")
        print(f"   Image size: {image.size}, mode: {image.mode}")
        
        # Preprocess (same as inference script)
        x = preprocess_image(image)
        print(f"   Preprocessed shape: {x.shape}")
        
        # Predict
        preds = model.predict(x, verbose=0)
        probs = preds[0]
        
        print(f"   Raw probabilities: {probs}")
        
        # Get prediction
        idx = int(np.argmax(probs))
        label = CLASS_LABELS[idx] if idx < len(CLASS_LABELS) else str(idx)
        confidence = float(probs[idx])
        
        print(f"   Predicted index: {idx}")
        print(f"   Predicted label: {label}")
        print(f"   Confidence: {confidence:.4f}")
        
        # Map to our alert types
        alert_type = LABEL_MAPPING.get(label, 'normal')
        
        print(f"   Alert type: {alert_type}")
        
        # All probabilities
        all_probs = {
            CLASS_LABELS[i]: float(probs[i]) 
            for i in range(len(CLASS_LABELS))
        }
        
        return {
            "prediction": alert_type,
            "confidence": confidence,
            "original_label": label,
            "all_probabilities": all_probs
        }
        
    except Exception as e:
        print(f"❌ Prediction error: {str(e)}")
        import traceback
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=f"Prediction error: {str(e)}")

def mock_prediction(image: Image.Image) -> dict:
    """
    Mock prediction when model is not available
    """
    img_array = np.array(image)
    
    # Simple heuristic for demo
    avg_brightness = np.mean(img_array)
    blue_channel = np.mean(img_array[:, :, 2]) if len(img_array.shape) == 3 else avg_brightness
    
    if blue_channel > 150:
        prediction = "flood"
        confidence = 0.85
    elif avg_brightness < 100:
        prediction = "traffic"
        confidence = 0.78
    else:
        prediction = "normal"
        confidence = 0.65
    
    return {
        "prediction": prediction,
        "confidence": float(confidence),
        "original_label": prediction.capitalize(),
        "all_probabilities": {
            "Flooding": 0.85 if prediction == "flood" else 0.05,
            "TrafficJam": 0.78 if prediction == "traffic" else 0.10,
            "Normal": 0.65 if prediction == "normal" else 0.15
        }
    }

@app.on_event("startup")
async def startup_event():
    """Initialize model on startup"""
    load_detection_model()

@app.get("/", response_model=HealthResponse)
async def root():
    """Root endpoint"""
    return {
        "status": "running",
        "model_loaded": model is not None,
        "tensorflow_available": TF_AVAILABLE
    }

@app.get("/health", response_model=HealthResponse)
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "model_loaded": model is not None,
        "tensorflow_available": TF_AVAILABLE
    }

@app.post("/detect", response_model=DetectionResult)
async def detect_traffic_condition(file: UploadFile = File(...)):
    """
    Detect traffic condition from uploaded image
    
    Args:
        file: Image file (JPEG, PNG)
    
    Returns:
        DetectionResult with prediction and confidence
        - prediction: 'traffic' | 'flood' | 'normal'
        - confidence: 0.0 to 1.0
    """
    # Validate file type
    if not file.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="File must be an image")
    
    try:
        # Read image
        contents = await file.read()
        image = Image.open(io.BytesIO(contents))
        
        # Convert to RGB if necessary
        if image.mode != "RGB":
            image = image.convert("RGB")
        
        # Get prediction
        result = predict_with_model(image)
        
        return DetectionResult(
            success=True,
            prediction=result["prediction"],
            confidence=result["confidence"],
            all_probabilities=result.get("all_probabilities")
        )
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Detection failed: {str(e)}")

@app.post("/batch-detect")
async def batch_detect(files: list[UploadFile] = File(...)):
    """
    Detect traffic conditions from multiple images
    
    Args:
        files: List of image files
    
    Returns:
        List of detection results
    """
    results = []
    
    for file in files:
        try:
            contents = await file.read()
            image = Image.open(io.BytesIO(contents))
            
            if image.mode != "RGB":
                image = image.convert("RGB")
            
            result = predict_with_model(image)
            results.append({
                "filename": file.filename,
                "success": True,
                "prediction": result["prediction"],
                "confidence": result["confidence"],
                "all_probabilities": result.get("all_probabilities")
            })
        except Exception as e:
            results.append({
                "filename": file.filename,
                "success": False,
                "error": str(e)
            })
    
    return {"results": results}

if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8001,
        reload=True
    )
