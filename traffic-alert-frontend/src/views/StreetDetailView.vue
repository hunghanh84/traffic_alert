<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const route = useRoute()
const router = useRouter()
const streetData = ref(null)
const isLoading = ref(true)
const map = ref(null)

const severityColors = {
  low: '#10b981',
  medium: '#f59e0b',
  high: '#ef4444',
  critical: '#dc2626'
}

const severityLabels = {
  low: 'Thấp',
  medium: 'Trung bình',
  high: 'Cao',
  critical: 'Nghiêm trọng'
}

const typeLabels = {
  traffic: 'Tắc đường',
  flood: 'Ngập đường'
}

const typeIcons = {
  traffic: '🚗',
  flood: '🌊'
}

async function initMiniMap() {
  if (!streetData.value?.street?.coordinates) return
  
  await nextTick()
  let coords = streetData.value.street.coordinates
  if (typeof coords === 'string') {
    try {
      coords = JSON.parse(coords)
    } catch (e) {
      console.error('Error parsing coordinates:', e)
      return
    }
  }
  
  // Find center of coordinates
  let center = [16.0544, 108.2022] // Default
  if (Array.isArray(coords) && coords.length > 0) {
    if (Array.isArray(coords[0]) && typeof coords[0][0] === 'number') {
      // It's a LineString
      center = coords[Math.floor(coords.length / 2)]
    } else if (Array.isArray(coords[0]) && Array.isArray(coords[0][0])) {
      // It's a MultiLineString
      center = coords[0][Math.floor(coords[0].length / 2)]
    }
  }

  map.value = L.map('mini-map').setView(center, 15)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map.value)

  const color = '#667eea'
  if (Array.isArray(coords[0]) && typeof coords[0][0] === 'number') {
    L.polyline(coords, { color, weight: 6, opacity: 0.8 }).addTo(map.value)
  } else if (Array.isArray(coords[0]) && Array.isArray(coords[0][0])) {
    coords.forEach(line => {
      L.polyline(line, { color, weight: 6, opacity: 0.8 }).addTo(map.value)
    })
  }
}

async function fetchStreetDetail() {
  try {
    isLoading.value = true
    const response = await fetch(`http://localhost:8000/api/locations/streets/${route.params.id}`)
    const result = await response.json()
    
    if (result.success) {
      streetData.value = result.data
      console.log('📍 Street data loaded:', streetData.value)
      initMiniMap()
    }
  } catch (error) {
    console.error('Error loading street detail:', error)
  } finally {
    isLoading.value = false
  }
}

function goBack() {
  router.back()
}

function viewAlertDetail(alertId) {
  router.push(`/alerts/${alertId}`)
}

onMounted(() => {
  fetchStreetDetail()
})
</script>

<template>
  <div class="street-detail-page">
    <div class="detail-container">
      <!-- Loading -->
      <div v-if="isLoading" class="loading-state">
        <div class="spinner"></div>
        <p>Đang tải thông tin tuyến đường...</p>
      </div>

      <!-- Street Detail -->
      <div v-else-if="streetData" class="detail-content">
        <!-- Header -->
        <header class="detail-header">
          <button @click="goBack" class="back-btn">
            <span>←</span>
            <span>Quay lại</span>
          </button>
          <h1>Chi tiết tuyến đường</h1>
        </header>

        <!-- Street Info Card -->
        <div class="info-card">
          <div class="card-header">
            <div class="street-icon">🛣️</div>
            <div class="street-title">
              <h2>{{ streetData.street.ten }}</h2>
              <p class="street-type">{{ streetData.street.loai_duong }}</p>
            </div>
          </div>

          <!-- Location Info -->
          <div class="info-section">
            <h3 class="section-title">
              <span class="section-icon">📍</span>
              Vị trí
            </h3>
            <div class="location-detail">
              <p v-if="streetData.street.khu_vuc">
                <strong>Khu vực:</strong> {{ streetData.street.khu_vuc.ten }}
              </p>
              <p v-if="streetData.street.phuong_xa">
                <strong>Phường/Xã:</strong> {{ streetData.street.phuong_xa.ten }}
              </p>
              <p v-if="streetData.street.phuong_xa?.thanh_pho">
                <strong>Thành phố:</strong> {{ streetData.street.phuong_xa.thanh_pho.ten }}
              </p>
            </div>
            
            <!-- Mini Map -->
            <div id="mini-map" class="mini-map-container" v-show="streetData.street.coordinates"></div>
          </div>



          <!-- Alerts List -->
          <div class="info-section">
            <h3 class="section-title">
              <span class="section-icon">⚠️</span>
              Danh sách cảnh báo ({{ streetData.alerts.length }})
            </h3>
            
            <div v-if="streetData.alerts.length > 0" class="alerts-list">
              <div 
                v-for="alert in streetData.alerts" 
                :key="alert.id"
                class="alert-item"
                @click="viewAlertDetail(alert.id)"
              >
                <div class="alert-icon">{{ typeIcons[alert.loai_canh_bao] }}</div>
                <div class="alert-content">
                  <div class="alert-header">
                    <span class="alert-type">{{ typeLabels[alert.loai_canh_bao] }}</span>
                    <span 
                      class="alert-severity" 
                      :style="{ backgroundColor: severityColors[alert.muc_do] }"
                    >
                      {{ severityLabels[alert.muc_do] }}
                    </span>
                  </div>
                  <p class="alert-description">{{ alert.mo_ta }}</p>
                    <div class="alert-footer">
                      <div class="time-info">
                        <span class="alert-time">{{ alert.created_at }}</span>
                        <span v-if="alert.expires_at" class="alert-expiry"> • Hết hạn: {{ alert.expires_at }}</span>
                      </div>
                      <span v-if="alert.media && alert.media.length > 0" class="alert-media">
                        📷 {{ alert.media.length }} ảnh
                      </span>
                    </div>
                </div>
              </div>
            </div>
            
            <div v-else class="empty-alerts">
              <p>✅ Hiện tại không có cảnh báo nào trên tuyến đường này</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.street-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  padding: 2rem;
}

.detail-container {
  max-width: 1000px;
  margin: 0 auto;
}

/* Loading */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  color: white;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Header */
.detail-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.detail-header h1 {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  backdrop-filter: blur(10px);
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateX(-4px);
}

/* Info Card */
.info-card {
  background: white;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.card-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.street-icon {
  font-size: 4rem;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.street-title h2 {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.5rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.street-type {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1.1rem;
  font-weight: 500;
}

/* Info Section */
.info-section {
  padding: 2rem 2.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.info-section:last-child {
  border-bottom: none;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.section-icon {
  font-size: 1.5rem;
}

.location-detail {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  padding: 1.5rem;
  border-radius: 16px;
  border-left: 4px solid #667eea;
}

.location-detail p {
  font-size: 1.05rem;
  color: #374151;
  margin-bottom: 0.75rem;
}

.location-detail p:last-child {
  margin-bottom: 0;
}

.mini-map-container {
  height: 300px;
  width: 100%;
  margin-top: 1.5rem;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
  padding: 1.5rem;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.stat-card.total {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.stat-card.traffic {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.stat-card.flood {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.stat-icon {
  font-size: 2.5rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: 800;
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.9;
}

/* Severity Stats */
.severity-stats {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.severity-bar {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.severity-label {
  display: flex;
  justify-content: space-between;
  font-size: 0.95rem;
  font-weight: 600;
  color: #4b5563;
}

.severity-count {
  color: #1f2937;
}

.severity-progress {
  height: 12px;
  background: #e5e7eb;
  border-radius: 6px;
  overflow: hidden;
}

.severity-fill {
  height: 100%;
  transition: width 0.5s ease;
  border-radius: 6px;
}

/* Alerts List */
.alerts-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.alert-item {
  display: flex;
  gap: 1rem;
  padding: 1.25rem;
  background: #f9fafb;
  border-radius: 16px;
  border-left: 4px solid #667eea;
  cursor: pointer;
  transition: all 0.3s ease;
}

.alert-item:hover {
  background: #f3f4f6;
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.alert-icon {
  font-size: 2rem;
  flex-shrink: 0;
}

.alert-content {
  flex: 1;
}

.alert-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.5rem;
}

.alert-type {
  font-weight: 700;
  color: #1f2937;
  font-size: 1.05rem;
}

.alert-severity {
  padding: 0.25rem 0.75rem;
  border-radius: 8px;
  color: white;
  font-size: 0.85rem;
  font-weight: 600;
}

.alert-description {
  color: #4b5563;
  font-size: 0.95rem;
  margin-bottom: 0.75rem;
  line-height: 1.5;
}

.alert-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.85rem;
  color: #6b7280;
}

.time-info {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.alert-expiry {
  color: #ef4444;
  font-weight: 600;
}

.alert-media {
  font-weight: 600;
}

.empty-alerts {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.1rem;
}

@media (max-width: 768px) {
  .street-detail-page {
    padding: 1rem;
  }

  .detail-header h1 {
    font-size: 1.5rem;
  }

  .card-header {
    padding: 1.5rem;
  }

  .street-title h2 {
    font-size: 1.5rem;
  }

  .info-section {
    padding: 1.5rem;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
