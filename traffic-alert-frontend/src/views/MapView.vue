<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const router = useRouter()

const map = ref(null)
const alerts = ref([])
const isLoading = ref(true)
const polylines = ref([])
let refreshInterval = null

// Màu sắc theo mức độ
const severityColors = {
  low: '#10b981',      // Green
  medium: '#f59e0b',   // Orange
  high: '#ef4444',     // Red
  critical: '#dc2626'  // Dark Red
}

// Màu sắc theo loại cảnh báo
const alertTypeColors = {
  traffic: '#ef4444',  // Red for traffic
  flood: '#3b82f6'     // Blue for flood
}

const fetchAlertsForMap = async () => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/alerts/map')
    const result = await response.json()
    
    if (result.success) {
      alerts.value = result.data
      console.log(`📍 Tìm thấy ${alerts.value.length} cảnh báo đã duyệt`)
      
      // Đếm số đường cần geocode
      const needGeocoding = alerts.value.filter(a => !a.duong.coordinates || a.duong.coordinates.length === 0)
      console.log(`🔍 Cần lấy tọa độ cho ${needGeocoding.length} đường từ OSM`)
      
      // Nếu đường chưa có tọa độ, tự động lấy từ OSM
      let geocoded = 0
      for (const alert of alerts.value) {
        if (!alert.duong.coordinates || (Array.isArray(alert.duong.coordinates) && alert.duong.coordinates.length === 0)) {
          geocoded++
          console.log(`⏳ Đang geocode ${geocoded}/${needGeocoding.length}: ${alert.duong.ten}...`)
          await geocodeStreet(alert)
        }
      }
      
      console.log(`✅ Hoàn thành! Hiển thị ${alerts.value.length} cảnh báo trên bản đồ`)
      updateMapPolylines()
    }
  } catch (error) {
    console.error('❌ Lỗi khi tải dữ liệu bản đồ:', error)
  } finally {
    isLoading.value = false
  }
}

// Tự động lấy tọa độ từ OSM Nominatim API
const geocodeStreet = async (alert) => {
  try {
    // Tạo query: "Tên đường, Phường, Thành phố, Vietnam"
    const query = `${alert.duong.ten}, ${alert.phuong_ten}, ${alert.thanh_pho_ten}, Vietnam`
    console.log(`🌐 Geocoding query: "${query}"`)
    const encodedQuery = encodeURIComponent(query)
    
    // Gọi Nominatim API (Thử lần 1: Có Phường)
    let url = `https://nominatim.openstreetmap.org/search?q=${encodedQuery}&format=json&limit=1&polygon_geojson=1`
    
    let response = await fetch(url, {
      headers: { 'User-Agent': 'TrafficAlertApp/1.0' }
    })
    
    let data = await response.json()
    
    // Nếu không tìm thấy, thử bỏ tên phường (Thử lần 2: Chỉ Tên đường + Thành phố)
    if (!data || data.length === 0) {
      console.warn(`⚠️ Không tìm thấy với phường, thử lại với thành phố...`)
      const cityQuery = `${alert.duong.ten}, ${alert.thanh_pho_ten || 'Đà Nẵng'}, Vietnam`
      const cityUrl = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(cityQuery)}&format=json&limit=1&polygon_geojson=1`
      response = await fetch(cityUrl, { headers: { 'User-Agent': 'TrafficAlertApp/1.0' } })
      data = await response.json()
    }
    
    if (data && data.length > 0 && data[0].geojson) {
      const geojson = data[0].geojson
      console.log(`📍 GeoJSON Type: ${geojson.type} for ${alert.duong.ten}`)
      
      // Chuyển đổi GeoJSON thành cấu trúc tọa độ Leaflet [lat, lng]
      let finalCoordinates = null
      
      if (geojson.type === 'LineString') {
        finalCoordinates = geojson.coordinates.map(coord => [coord[1], coord[0]])
      } else if (geojson.type === 'MultiLineString') {
        finalCoordinates = geojson.coordinates.map(line => 
          line.map(coord => [coord[1], coord[0]])
        )
      } else if (geojson.type === 'Point') {
        console.warn(`⛔ Bỏ qua Point cho ${alert.duong.ten}`)
      }
      
      if (finalCoordinates) {
        alert.duong.coordinates = finalCoordinates
        console.log(`✓ Đã lấy tọa độ cho: ${alert.duong.ten}`)
        // Lưu vào DB
        await saveCoordinates(alert.duong.id, finalCoordinates)
      }
    } else {
      console.warn(`✗ Không tìm thấy tọa độ cho: ${alert.duong.ten}`)
    }
    
    // Delay để tránh rate limit
    await new Promise(resolve => setTimeout(resolve, 1000))
  } catch (error) {
    console.error(`Lỗi khi geocode ${alert.duong.ten}:`, error)
  }
}

// Hàm lưu tọa độ vào Database
const saveCoordinates = async (streetId, coordinates) => {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/alerts/street/${streetId}/coordinates`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ coordinates })
    })
    const result = await response.json()
    if (result.success) {
      console.log(`💾 Đã lưu tọa độ vào DB cho đường ID: ${streetId}`)
    }
  } catch (err) {
    console.error(`Lỗi khi lưu tọa độ:`, err)
  }
}

const initMap = () => {
  map.value = L.map('map').setView([16.0544, 108.2022], 13)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map.value)

  // Xử lý click vào nút "Xem chi tiết" trong popup
  document.getElementById('map').addEventListener('click', (e) => {
    if (e.target.classList.contains('view-detail-btn')) {
      const streetId = e.target.getAttribute('data-id')
      if (streetId) {
        router.push(`/streets/${streetId}`)
      }
    }
  })
}

const updateMapPolylines = () => {
  polylines.value.forEach(layer => map.value.removeLayer(layer))
  polylines.value = []
  
  alerts.value.forEach(alert => {
    if (alert.duong && alert.duong.coordinates && Array.isArray(alert.duong.coordinates) && alert.duong.coordinates.length > 0) {
      const color = alert.loai_canh_bao === 'traffic' ? '#ef4444' : '#3b82f6'
      const weight = alert.muc_do === 'critical' ? 8 : alert.muc_do === 'high' ? 6 : 4
      
      const popupContent = `
        <div style="min-width: 200px;">
          <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: bold; color: ${color};">
            ${alert.loai_canh_bao === 'traffic' ? '🚗 Tắc đường' : '🌊 Ngập đường'}
          </h3>
          <p style="margin: 4px 0;"><strong>Đường:</strong> ${alert.duong.ten}</p>
          <p style="margin: 4px 0;"><strong>Địa chỉ:</strong> ${alert.dia_chi}</p>
          <p style="margin: 4px 0;"><strong>Mức độ:</strong> 
            <span style="color: ${color}; font-weight: bold;">
              ${alert.muc_do === 'low' ? 'Thấp' : alert.muc_do === 'medium' ? 'Trung bình' : alert.muc_do === 'high' ? 'Cao' : 'Nghiêm trọng'}
            </span>
          </p>
          ${alert.mo_ta ? `<p style="margin: 4px 0;"><strong>Mô tả:</strong> ${alert.mo_ta}</p>` : ''}
          ${alert.media && alert.media.length > 0 ? `
            <img src="${alert.media[0].url}" alt="Alert image" style="width: 100%; margin-top: 8px; border-radius: 4px;" />
          ` : ''}
          <p style="margin: 8px 0 0 0; font-size: 12px; color: #666;">
            <strong>Thời gian:</strong> ${alert.created_at}
          </p>
          <div style="margin-top: 12px; text-align: center;">
            <button 
              class="view-detail-btn" 
              data-id="${alert.duong.id}"
              style="background: ${color}; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%;"
            >
              Xem chi tiết tuyến đường
            </button>
          </div>
        </div>
      `
      
      const layer = L.polyline(alert.duong.coordinates, {
        color: color,
        weight: weight,
        opacity: 0.8,
        smoothFactor: 1
      }).addTo(map.value)
      
      layer.bindPopup(popupContent)
      polylines.value.push(layer)
    }
  })
}

onMounted(() => {
  initMap()
  fetchAlertsForMap()
  refreshInterval = setInterval(fetchAlertsForMap, 30000)
})

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval)
  if (map.value) map.value.remove()
})
</script>

<template>
  <div class="map-view">
    <div class="map-header">
      <div class="header-content">
        <h1>🗺️ Bản Đồ Cảnh Báo Giao Thông</h1>
        <p>Hiển thị các cảnh báo đã được AI duyệt trên bản đồ thời gian thực</p>
      </div>
      <div class="stats">
        <div class="stat-item">
          <span class="stat-number">{{ alerts.length }}</span>
          <span class="stat-label">Cảnh báo đang hoạt động</span>
        </div>
      </div>
    </div>

    <div class="legend">
      <h3>Chú thích:</h3>
      <div class="legend-items">
        <div class="legend-item">
          <div class="legend-color" style="background: #ef4444;"></div>
          <span>🚗 Tắc đường</span>
        </div>
        <div class="legend-item">
          <div class="legend-color" style="background: #3b82f6;"></div>
          <span>🌊 Ngập đường</span>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="loading-overlay">
      <div class="spinner"></div>
      <p>Đang tải bản đồ...</p>
    </div>

    <div id="map" class="map-container"></div>
  </div>
</template>

<style scoped>
.map-view {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  padding: 2rem;
  position: relative;
}

.map-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  animation: slideDown 0.6s ease;
}

.header-content h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.5rem;
  text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.header-content p {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.9);
}

.stats {
  display: flex;
  gap: 1.5rem;
}

.stat-item {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  padding: 1rem 2rem;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.stat-number {
  display: block;
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.stat-label {
  display: block;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.9);
  margin-top: 0.25rem;
}

.legend {
  background: white;
  padding: 1.5rem;
  border-radius: 16px;
  margin-bottom: 1.5rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.legend h3 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1rem;
}

.legend-items {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.legend-color {
  width: 40px;
  height: 8px;
  border-radius: 4px;
}

.legend-item span {
  font-size: 0.95rem;
  color: #4b5563;
  font-weight: 500;
}

.map-container {
  width: 100%;
  height: calc(100vh - 280px);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: fadeIn 0.8s ease;
}

.loading-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  z-index: 1000;
  background: rgba(255, 255, 255, 0.95);
  padding: 3rem;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.spinner {
  width: 60px;
  height: 60px;
  border: 4px solid #f3f4f6;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1.5rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-overlay p {
  font-size: 1.2rem;
  color: #6b7280;
  font-weight: 600;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

:deep(.leaflet-popup-content-wrapper) {
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

:deep(.leaflet-popup-content) {
  margin: 16px;
  font-family: inherit;
}

@media (max-width: 768px) {
  .map-view { padding: 1rem; }
  .map-header { flex-direction: column; gap: 1.5rem; align-items: flex-start; }
  .header-content h1 { font-size: 2rem; }
  .map-container { height: calc(100vh - 350px); }
  .legend-items { gap: 1rem; }
}
</style>
