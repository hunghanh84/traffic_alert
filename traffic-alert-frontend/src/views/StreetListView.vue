<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import StreetDetailInline from '../components/StreetDetailInline.vue'

const router = useRouter()
const streets = ref([])
const isLoading = ref(true)
const searchQuery = ref('')
const onlyWithAlerts = ref(true)
const selectedStreetId = ref(null)

async function fetchStreets() {
  try {
    isLoading.value = true
    let url = 'http://localhost:8000/api/locations/streets'
    if (onlyWithAlerts.value) {
      url += '?has_alerts=1'
    }
    const response = await fetch(url)
    const result = await response.json()
    
    if (result.success) {
      streets.value = result.data
      console.log('🛣️ Streets loaded:', streets.value.length)
    }
  } catch (error) {
    console.error('Error loading streets:', error)
  } finally {
    isLoading.value = false
  }
}

function toggleAlertFilter() {
  onlyWithAlerts.value = !onlyWithAlerts.value
  selectedStreetId.value = null // Reset selection when filter changes
  fetchStreets()
}

const filteredStreets = computed(() => {
  if (!searchQuery.value) return streets.value
  const query = searchQuery.value.toLowerCase()
  return streets.value.filter(s => 
    s.ten.toLowerCase().includes(query) || 
    (s.phuong_ten && s.phuong_ten.toLowerCase().includes(query))
  )
})

function toggleStreetDetail(streetId) {
  if (selectedStreetId.value === streetId) {
    selectedStreetId.value = null
  } else {
    selectedStreetId.value = streetId
  }
}

onMounted(() => {
  fetchStreets()
})
</script>

<template>
  <div class="street-list-page">
    <div class="list-container">
      <header class="page-header">
        <div class="header-content">
          <h1>🛣️ Tuyến đường có cảnh báo</h1>
          <p>Xem nhanh tình trạng giao thông trên các tuyến đường đang có sự cố</p>
        </div>
        
        <div class="filter-actions">
          <div class="search-box">
            <span class="search-icon">🔍</span>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Tìm theo tên đường hoặc phường..."
            />
          </div>
          
          <button @click="toggleAlertFilter" class="filter-toggle" :class="{ 'active': onlyWithAlerts }">
            {{ onlyWithAlerts ? '🔔 Chỉ hiện tuyến có cảnh báo' : '🌐 Hiển thị tất cả tuyến đường' }}
          </button>
        </div>
      </header>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-state">
        <div class="spinner"></div>
        <p>Đang tải danh sách tuyến đường...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredStreets.length === 0" class="empty-state">
        <div class="empty-icon">✅</div>
        <h3>Hiện tại không có cảnh báo nào</h3>
        <p v-if="onlyWithAlerts">Tất cả các tuyến đường đều đang lưu thông bình thường</p>
        <p v-else>Không tìm thấy tuyến đường nào khớp với tìm kiếm của bạn</p>
      </div>

      <!-- Street List -->
      <div v-else class="street-list">
        <div 
          v-for="street in filteredStreets" 
          :key="street.id" 
          class="street-group"
        >
          <div 
            class="street-item"
            :class="{ 'item-active': selectedStreetId === street.id }"
            @click="toggleStreetDetail(street.id)"
          >
            <div class="street-main-info">
              <div class="street-icon">🛣️</div>
              <div class="street-text">
                <h3 class="street-name">{{ street.ten }}</h3>
                <p class="street-location">{{ street.phuong_ten || 'Đà Nẵng' }} • <span class="street-type">{{ street.type || 'Đường phố' }}</span></p>
              </div>
            </div>
            
            <div class="street-meta">
              <div v-if="onlyWithAlerts" class="alert-status">
                <div class="status-badge">
                  <span class="pulse-dot"></span>
                  Đang có sự cố
                </div>
                <span v-if="street.expires_at" class="expiry-time">Hết hạn lúc: {{ street.expires_at }}</span>
              </div>
              <div class="view-detail">
                <span>{{ selectedStreetId === street.id ? 'Thu gọn' : 'Xem chi tiết' }}</span>
                <span class="arrow" :class="{ 'arrow-up': selectedStreetId === street.id }">↓</span>
              </div>
            </div>
          </div>

          <!-- Inline Detail Component -->
          <StreetDetailInline 
            v-if="selectedStreetId === street.id" 
            :street-id="street.id" 
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.street-list-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  padding: 2rem;
}

.list-container {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  animation: fadeInDown 0.6s ease;
}

.header-content h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.5rem;
  text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.header-content p {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.9);
}

.filter-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.filter-toggle {
  padding: 0.75rem 1.5rem;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.filter-toggle:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.filter-toggle.active {
  background: white;
  color: #667eea;
  border-color: white;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Search Box */
.search-box {
  position: relative;
  flex: 1;
  min-width: 300px;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 0.5rem 1rem;
  border: 1px solid rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
}

.search-box:focus-within {
  background: white;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  transform: translateY(-2px);
}

.search-icon {
  font-size: 1.25rem;
  margin-right: 0.75rem;
}

.search-box input {
  background: transparent;
  border: none;
  color: white;
  width: 100%;
  font-size: 1.1rem;
  padding: 0.5rem 0;
  outline: none;
}

.search-box:focus-within input {
  color: #1f2937;
}

.search-box input::placeholder {
  color: rgba(255, 255, 255, 0.7);
}

.search-box:focus-within input::placeholder {
  color: #9ca3af;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 5rem;
  color: white;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1.5rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Street List */
.street-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  animation: fadeInUp 0.8s ease;
}

.street-item {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 1.25rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.street-item:hover {
  transform: translateX(10px);
  background: white;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
}

.street-main-info {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.street-icon {
  font-size: 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.street-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.street-location {
  color: #6b7280;
  font-size: 0.95rem;
}

.street-type {
  color: #667eea;
  font-weight: 600;
}

.street-meta {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.alert-status {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.25rem;
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  padding: 0.4rem 0.8rem;
  border-radius: 100px;
  font-size: 0.8rem;
  font-weight: 700;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.expiry-time {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  background-color: #ef4444;
  border-radius: 50%;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

.view-detail {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #667eea;
  font-weight: 600;
  opacity: 0.7;
  transition: all 0.3s ease;
}

.street-item:hover .view-detail {
  opacity: 1;
}

.arrow {
  font-size: 1.2rem;
  transition: transform 0.3s ease;
}

.arrow-up {
  transform: rotate(180deg);
}

.item-active {
  background: white;
  border-left: 6px solid #667eea;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 5rem;
  color: white;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 24px;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.empty-state p {
  opacity: 0.8;
}

@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 640px) {
  .street-list-page {
    padding: 1rem;
  }
  
  .header-content h1 {
    font-size: 2rem;
  }
  
  .street-grid {
    grid-template-columns: 1fr;
  }
}
</style>
