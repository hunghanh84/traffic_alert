"""
MOCK YOLO Verification API - For Demo/Testing
Luôn trả về kết quả match để test flow
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import random

app = Flask(__name__)
CORS(app)

@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status': 'ok',
        'mode': 'MOCK - Always returns match'
    })

@app.route('/verify', methods=['POST'])
def verify():
    """
    Mock verify - Luôn trả về match với confidence ngẫu nhiên
    """
    try:
        data = request.json
        expected_label = data.get('expected_label')
        
        if not expected_label:
            return jsonify({
                'success': False,
                'error': 'Missing expected_label'
            }), 400
        
        # Mock result - Luôn match với confidence 75-95%
        confidence = random.uniform(0.75, 0.95)
        
        result = {
            'detected_label': expected_label,
            'confidence': round(confidence, 2),
            'match': True,
            'detections': random.randint(3, 8),
            'mode': 'MOCK'
        }
        
        print(f"[MOCK] Expected: {expected_label}")
        print(f"[MOCK] Returned: {result}")
        
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
    print("🎭 MOCK YOLO Verification API")
    print("=" * 60)
    print("⚠️  WARNING: This is MOCK mode!")
    print("📝 Always returns match=true for testing")
    print("🌐 Server: http://localhost:5000")
    print("=" * 60)
    
    app.run(host='0.0.0.0', port=5000, debug=True)
