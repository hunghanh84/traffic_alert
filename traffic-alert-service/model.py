"""
Simple CNN model for traffic condition classification
Categories: traffic (jam), flood, normal
"""

import torch
import torch.nn as nn
import torch.nn.functional as F

class TrafficDetector(nn.Module):
    def __init__(self, num_classes=3):
        super(TrafficDetector, self).__init__()
        
        # Convolutional layers
        self.conv1 = nn.Conv2d(3, 32, kernel_size=3, padding=1)
        self.bn1 = nn.BatchNorm2d(32)
        self.conv2 = nn.Conv2d(32, 64, kernel_size=3, padding=1)
        self.bn2 = nn.BatchNorm2d(64)
        self.conv3 = nn.Conv2d(64, 128, kernel_size=3, padding=1)
        self.bn3 = nn.BatchNorm2d(128)
        self.conv4 = nn.Conv2d(128, 256, kernel_size=3, padding=1)
        self.bn4 = nn.BatchNorm2d(256)
        
        # Pooling
        self.pool = nn.MaxPool2d(2, 2)
        
        # Fully connected layers
        self.fc1 = nn.Linear(256 * 14 * 14, 512)
        self.fc2 = nn.Linear(512, 128)
        self.fc3 = nn.Linear(128, num_classes)
        
        # Dropout
        self.dropout = nn.Dropout(0.5)
        
    def forward(self, x):
        # Conv block 1
        x = self.pool(F.relu(self.bn1(self.conv1(x))))
        
        # Conv block 2
        x = self.pool(F.relu(self.bn2(self.conv2(x))))
        
        # Conv block 3
        x = self.pool(F.relu(self.bn3(self.conv3(x))))
        
        # Conv block 4
        x = self.pool(F.relu(self.bn4(self.conv4(x))))
        
        # Flatten
        x = x.view(-1, 256 * 14 * 14)
        
        # FC layers
        x = F.relu(self.fc1(x))
        x = self.dropout(x)
        x = F.relu(self.fc2(x))
        x = self.dropout(x)
        x = self.fc3(x)
        
        return x

def create_model(num_classes=3, pretrained=False):
    """
    Create traffic detector model
    
    Args:
        num_classes: Number of classes (default: 3 - traffic, flood, normal)
        pretrained: Load pretrained weights
    
    Returns:
        TrafficDetector model
    """
    model = TrafficDetector(num_classes=num_classes)
    
    if pretrained:
        # TODO: Load pretrained weights
        pass
    
    return model

# Class labels
CLASS_LABELS = {
    0: "traffic",
    1: "flood", 
    2: "normal"
}

LABEL_TO_INDEX = {v: k for k, v in CLASS_LABELS.items()}
