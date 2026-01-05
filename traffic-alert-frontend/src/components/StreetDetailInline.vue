<script setup>
import { ref, onMounted, nextTick, watch, computed } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
  streetId: {
    type: [String, Number],
    required: true
  }
})

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

const mapId = computed(() => `map-${props.streetId}`)

async function initMiniMap() {
  if (!streetData.value?.street?.coordinates) return
  
  await nextTick()
  await new Promise(resolve => setTimeout(resolve, 100)) // Give DOM time to settle

  // Ensure we cleanup previous map instance if any
  if (map.value) {
    try {
      map.value.remove()
    } catch (e) { console.warn('Map cleanup error:', e) }
    map.value = null
  }

  try {
    let rawCoords = streetData.value.street.coordinates
    const coords = typeof rawCoords === 'string' ? JSON.parse(rawCoords) : rawCoords
    
    if (!coords || !Array.isArray(coords)) return

    // Find center of coordinates
    let center = [16.0544, 108.2022] // Default Đà Nẵng
    if (coords.length > 0) {
      if (typeof coords[0][0] === 'number') {
        center = coords[Math.floor(coords.length / 2)]
      } else if (Array.isArray(coords[0]) && typeof coords[0][0][0] === 'number') {
        center = coords[0][Math.floor(coords[0].length / 2)]
      }
    }

    const mapElement = document.getElementById(mapId.value)
    if (!mapElement) return

    map.value = L.map(mapId.value).setView(center, 15)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap'
    }).addTo(map.value)

    const color = '#667eea'
    if (typeof coords[0][0] === 'number') {
      L.polyline(coords, { color, weight: 6, opacity: 0.8 }).addTo(map.value)
    } else if (Array.isArray(coords[0])) {
      coords.forEach(line => {
        L.polyline(line, { color, weight: 6, opacity: 0.8 }).addTo(map.value)
      })
    }
    
    // Crucial for Leaflet in dynamic/hidden containers
    setTimeout(() => {
      if (map.value) map.value.invalidateSize()
    }, 200)

  } catch (error) {
    console.error('Error initializing map:', error)
  }
}

async function fetchStreetDetail() {
  if (!props.streetId) return
  
  try {
    isLoading.value = true
    const response = await fetch(`http://localhost:8000/api/locations/streets/${props.streetId}`)
    const result = await response.json()
    
    if (result.success) {
      streetData.value = result.data
      console.log('📍 Street detail loaded:', streetData.value)
      console.log('📍 Street name:', streetData.value?.street?.ten)
      initMiniMap()
    }
  } catch (error) {
    console.error('Error loading street detail:', error)
  } finally {
    isLoading.value = false
  }
}

watch(() => props.streetId, () => {
  fetchStreetDetail()
}, { immediate: true })

onMounted(() => {
  fetchStreetDetail()
})
</script>

<template>
  <div class="street-detail-inline">
    <!-- Loading -->
    <div v-if="isLoading" class="loading-inline">
      <div class="spinner-small"></div>
      <p>Đang tải...</p>
    </div>

    <!-- Street Detail -->
    <div v-else-if="streetData" class="inline-content">
      <div class="inline-grid">
        <!-- Left Side: Basic Info & Map -->
        <div class="info-side">
          <div class="location-box">
            <h4>📍 {{ streetData.street.ten }}</h4>
            <div class="location-text">
               <p v-if="streetData.street.phuong_xa">
                <strong>Phường:</strong> {{ streetData.street.phuong_xa.ten }}
              </p>
              <p v-if="streetData.street.khu_vuc">
                <strong>Khu vực:</strong> {{ streetData.street.khu_vuc.ten }}
              </p>
            </div>
          </div>
          
          <div :id="mapId" class="inner-map-container" v-show="streetData.street.coordinates"></div>
        </div>

        <!-- Right Side: Alerts -->
        <div class="alerts-side">
          <div class="side-header">
            <h4>⚠️ Cảnh báo đã duyệt ({{ streetData.alerts.length }})</h4>
            <span class="view-all-badge" v-if="streetData.alerts.length > 0">Trực tiếp</span>
          </div>
          
          <div v-if="streetData.alerts.length > 0" class="mini-alerts-list">
            <div 
              v-for="alert in streetData.alerts" 
              :key="alert.id"
              class="mini-alert-item"
            >
              <div class="alert-status-ribbon" :style="{ background: severityColors[alert.muc_do] }"></div>
              
              <div class="mini-alert-header">
                <div class="type-badge">
                  <span class="type-icon">{{ typeIcons[alert.loai_canh_bao] }}</span>
                  <span class="type-label">{{ typeLabels[alert.loai_canh_bao] }}</span>
                </div>
                <span 
                  class="mini-alert-severity" 
                  :style="{ color: severityColors[alert.muc_do] }"
                >
                  ● {{ severityLabels[alert.muc_do] }}
                </span>
              </div>

              <p class="mini-alert-desc">{{ alert.mo_ta }}</p>

              <!-- Alert Images -->
              <div v-if="alert.media && alert.media.length > 0" class="alert-images-grid">
                <div 
                  v-for="m in alert.media" 
                  :key="m.id" 
                  class="alert-img-wrapper"
                >
                  <img :src="m.url" alt="Alert image" loading="lazy" />
                </div>
              </div>

              <div class="mini-alert-footer">
                <div class="sender-info" v-if="alert.nguoi_dung">
                  <span class="user-icon">👤</span>
                  <span class="user-name">{{ alert.nguoi_dung.ho_ten || alert.nguoi_dung.ten }}</span>
                </div>
                <div class="time-stamp">
                  <span class="clock-icon">🕒</span>
                  <span class="created-at">{{ alert.created_at }}</span>
                  <span v-if="alert.expires_at" class="expiry-stamp" title="Hết hạn"> • {{ alert.expires_at }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="mini-empty">
            <div class="empty-icon-wrapper">🚀</div>
            <p>Tuyến đường lưu thông bình thường</p>
            <span>Không có sự cố nào được ghi nhận</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.street-detail-inline {
  margin: 0 1px 15px 1px;
  padding: 1.5rem;
  background: rgba(255, 255, 255, 0.4);
  backdrop-filter: blur(20px);
  border-radius: 0 0 20px 20px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-top: none;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.loading-inline {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.5rem;
  color: #4b5563;
}

.spinner-small {
  width: 32px;
  height: 32px;
  border: 3px solid rgba(102, 126, 234, 0.1);
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 0.5rem;
}

.inline-grid {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 1.5rem;
}

.side-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.view-all-badge {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  padding: 2px 10px;
  border-radius: 100px;
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
}

.info-side h4, .alerts-side h4 {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 800;
  color: #1f2937;
}

.location-box {
  background: rgba(255, 255, 255, 0.5);
  padding: 1rem;
  border-radius: 16px;
  margin-bottom: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.location-text p {
  margin: 0.3rem 0;
  font-size: 0.9rem;
  color: #4b5563;
  display: flex;
  justify-content: space-between;
}

.inner-map-container {
  height: 250px;
  width: 100%;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.05);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.mini-alerts-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  max-height: 450px;
  overflow-y: auto;
  padding-right: 0.5rem;
}

.mini-alerts-list::-webkit-scrollbar {
  width: 4px;
}

.mini-alerts-list::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}

.mini-alert-item {
  position: relative;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.5);
  transition: all 0.3s ease;
}

.mini-alert-item:hover {
  background: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}

.alert-status-ribbon {
  position: absolute;
  left: 0;
  top: 15px;
  bottom: 15px;
  width: 3px;
  border-radius: 0 4px 4px 0;
}

.mini-alert-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.type-badge {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: rgba(0, 0, 0, 0.03);
  padding: 4px 8px;
  border-radius: 8px;
}

.type-icon { font-size: 1rem; }
.type-label { font-weight: 700; font-size: 0.85rem; color: #1f2937; }

.mini-alert-severity {
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
}

.mini-alert-desc {
  font-size: 0.9rem;
  color: #374151;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.alert-images-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.alert-img-wrapper {
  aspect-ratio: 1;
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid rgba(0,0,0,0.05);
}

.alert-img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.mini-alert-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 0.75rem;
  border-top: 1px solid rgba(0, 0, 0, 0.03);
  color: #9ca3af;
  font-size: 0.8rem;
}

.sender-info {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  color: #667eea;
  font-weight: 600;
}

.user-icon {
  font-size: 0.9rem;
}

.time-stamp {
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.expiry-stamp {
  color: #ef4444;
  font-weight: 700;
}

.mini-empty {
  text-align: center;
  padding: 2.5rem 1rem;
  color: #059669;
  background: rgba(209, 250, 229, 0.3);
  border-radius: 20px;
  border: 1.5px dashed rgba(16, 185, 129, 0.3);
}

.empty-icon-wrapper { font-size: 2.2rem; margin-bottom: 0.5rem; }
.mini-empty p { margin: 0; font-size: 1rem; font-weight: 800; }
.mini-empty span { display: block; margin-top: 0.3rem; font-size: 0.85rem; opacity: 0.7; }

@keyframes spin { to { transform: rotate(360deg); } }
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 900px) {
  .inline-grid { grid-template-columns: 1fr; gap: 1.5rem; }
}
</style>
