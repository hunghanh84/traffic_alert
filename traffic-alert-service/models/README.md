# Models Directory

Place your trained Keras model here:

- `train_detection_model.keras` - Main detection model

## Model Info

- Input: 224x224 RGB images
- Preprocessing: MobileNet preprocessing
- Output: 3 classes
  - 0: Flooding
  - 1: TrafficJam
  - 2: Normal

## Training

Use the provided training script to train your model on your dataset.

## If model is missing

The service will use mock predictions for demo purposes.
