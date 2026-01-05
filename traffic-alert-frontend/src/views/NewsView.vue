<script setup>
import { ref, onMounted, computed } from 'vue'

const trafficNews = ref([])
const isLoadingNews = ref(true)
const newsError = ref(null)

// Real alerts data
const recentAlerts = ref([])
const allAlerts = ref([])
const isLoadingAlerts = ref(true)

const weatherInfo = ref({
  temperature: '28°C',
  condition: 'Mây rải rác',
  rainfall: '20%'
})

const fetchRecentAlerts = async () => {
  try {
    isLoadingAlerts.value = true
    const response = await fetch('http://127.0.0.1:8000/api/alerts')
    const result = await response.json()
    
    if (result.success) {
      // Store ALL alerts for statistics (including pending)
      allAlerts.value = result.data
      
      // Filter only approved alerts for display
      const approvedAlerts = result.data.filter(alert => alert.trang_thai === 'da_duyet')
      recentAlerts.value = approvedAlerts.slice(0, 2) // Show only 2 most recent
      console.log('🚨 Loaded alerts:', result.data.length, 'total,', approvedAlerts.length, 'approved')
    }
  } catch (error) {
    console.error('Lỗi khi tải cảnh báo:', error)
  } finally {
    isLoadingAlerts.value = false
  }
}

const fetchTrafficNews = async () => {
  try {
    isLoadingNews.value = true
    newsError.value = null
    
    const response = await fetch('http://127.0.0.1:8000/api/news/traffic')
    const result = await response.json()
    
    if (result.success) {
      trafficNews.value = result.data
      console.log('📰 Loaded news:', result.data.length, 'items')
    } else {
      newsError.value = 'Không thể tải tin tức'
    }
  } catch (error) {
    console.error('Lỗi khi tải tin tức:', error)
    newsError.value = 'Lỗi kết nối'
  } finally {
    isLoadingNews.value = false
  }
}

const openNews = (url) => {
  window.open(url, '_blank')
}

const formatTime = (dateString) => {
  // Parse "DD-MM-YYYY HH:mm:ss" format from backend
  const parts = dateString.match(/(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2}):(\d{2})/)
  if (!parts) {
    console.error('Invalid date format:', dateString)
    return dateString
  }
  
  const [, day, month, year, hour, minute, second] = parts
  // Create date object (month is 0-indexed in JavaScript)
  const date = new Date(year, month - 1, day, hour, minute, second)
  
  const now = new Date()
  const diffMs = now - date
  const diffMins = Math.floor(diffMs / 60000)
  const diffHours = Math.floor(diffMins / 60)
  const diffDays = Math.floor(diffHours / 24)
  
  if (diffMins < 1) return 'Vừa xong'
  if (diffMins < 60) return `${diffMins} phút trước`
  if (diffHours < 24) return `${diffHours} giờ trước`
  if (diffDays < 7) return `${diffDays} ngày trước`
  
  // Fallback to date time
  return date.toLocaleString('vi-VN', { 
    day: '2-digit', 
    month: '2-digit', 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

// Computed statistics
const todayIncidents = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  return allAlerts.value.filter(alert => {
    const parts = alert.created_at.match(/(\d{2})-(\d{2})-(\d{4})/)
    if (!parts) return false
    const [, day, month, year] = parts
    const alertDate = new Date(year, month - 1, day)
    alertDate.setHours(0, 0, 0, 0)
    return alertDate.getTime() === today.getTime()
  }).length
})

const pendingIncidents = computed(() => {
  // Count all pending alerts (not just approved ones)
  return allAlerts.value.filter(alert => alert.trang_thai === 'cho_duyet').length
})

const affectedRoads = computed(() => {
  // Count unique roads from all alerts
  const uniqueRoads = new Set(allAlerts.value.map(alert => alert.duong))
  return uniqueRoads.size
})

onMounted(() => {
  fetchTrafficNews()
  fetchRecentAlerts()
  // Auto refresh every 30 minutes
  setInterval(fetchTrafficNews, 1800000)
  setInterval(fetchRecentAlerts, 1800000)
})
</script>

<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>Bảng Tin</h1>
      <div class="weather-info">
        <span class="temp">{{ weatherInfo.temperature }}</span>
        <span class="condition">{{ weatherInfo.condition }}</span>
        <span class="rainfall">Khả năng mưa: {{ weatherInfo.rainfall }}</span>
      </div>
    </header>

    <div class="dashboard-grid">
      <!-- Traffic News Feed Section -->
      <section class="news-feed-section">
        <h2>📰 Tin tức giao thông</h2>
        
        <!-- Loading State -->
        <div v-if="isLoadingNews" class="news-loading">
          <div class="spinner"></div>
          <p>Đang tải tin tức...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="newsError" class="news-error">
          <p>{{ newsError }}</p>
          <button @click="fetchTrafficNews" class="retry-btn">Thử lại</button>
        </div>

        <!-- News List -->
        <div v-else class="news-list">
          <article
            v-for="news in trafficNews"
            :key="news.link"
            class="news-item"
            @click="openNews(news.link)"
          >
            <div class="news-header">
              <span class="news-source" :style="{ backgroundColor: news.source_color }">
                {{ news.source }}
              </span>
              <span class="news-date">{{ news.published_at }}</span>
            </div>
            <h3 class="news-title">{{ news.title }}</h3>
            <p class="news-description">{{ news.description }}</p>
            <div v-if="news.image_url" class="news-image">
              <img :src="news.image_url" :alt="news.title" />
            </div>
          </article>
        </div>
      </section>

      <!-- Right Sidebar -->
      <div class="right-sidebar">
        <!-- Recent Incidents -->
        <section class="incidents-section">
          <h2>Sự cố gần đây</h2>
          
          <div v-if="isLoadingAlerts" class="loading-small">
            <div class="spinner-small"></div>
            <p>Đang tải...</p>
          </div>
          
          <div v-else-if="recentAlerts.length > 0" class="incidents-list">
            <div v-for="alert in recentAlerts" :key="alert.id" 
                 class="incident-card" :class="alert.muc_do">
              <div class="incident-icon">
                <span v-if="alert.loai_canh_bao === 'traffic'">🚦</span>
                <span v-else>🌊</span>
              </div>
              <div class="incident-details">
                <h3>{{ alert.loai_canh_bao === 'traffic' ? 'Tắc đường' : 'Ngập đường' }}</h3>
                <p>{{ alert.dia_chi }}</p>
                <time>{{ formatTime(alert.created_at) }}</time>
              </div>
            </div>
          </div>
          
          <div v-else class="empty-incidents">
            <p>Chưa có sự cố nào</p>
          </div>
        </section>

        <!-- Quick Stats -->
        <section class="stats-section">
          <h2>Thống kê nhanh</h2>
          <div class="stats-grid">
            <div class="stat-card">
              <h3>Tổng số sự cố hôm nay</h3>
              <div class="stat-value">{{ todayIncidents }}</div>
            </div>
            <div class="stat-card">
              <h3>Đang xử lý</h3>
              <div class="stat-value">{{ pendingIncidents }}</div>
            </div>
            <div class="stat-card">
              <h3>Tuyến đường ảnh hưởng</h3>
              <div class="stat-value">{{ affectedRoads }}</div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  padding: 1.5rem;
  background: #f3f4f6;
  height: 100%;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  font-size: 1.875rem;
  font-weight: 600;
  color: #111827;
}

.weather-info {
  background: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 1rem;
  align-items: center;
}

.weather-info .temp {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a1c23;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
  flex: 1;
  min-height: 0; /* Important for nested scrolling */
}

.news-feed-section {
  grid-column: 1;
  grid-row: 1 / 3;
  display: flex;
  flex-direction: column;
  /* Section styling */
  padding: 1.5rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.news-feed-section h2 {
  flex-shrink: 0;
  margin-bottom: 1rem;
}

/* Loading and Error States */
.news-loading,
.news-error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.retry-btn {
  margin-top: 1rem;
  padding: 0.5rem 1.5rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  background: #5568d3;
  transform: translateY(-2px);
}

/* News List */
.news-list {
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding-right: 0.5rem;
  max-height: 700px;
}

.news-list::-webkit-scrollbar {
  width: 6px;
}

.news-list::-webkit-scrollbar-track {
  background: #f3f4f6;
  border-radius: 3px;
}

.news-list::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.news-list::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* News Item */
.news-item {
  padding: 1.25rem;
  background: #f9fafb;
  border-radius: 12px;
  border-left: 4px solid #667eea;
  cursor: pointer;
  transition: all 0.3s ease;
}

.news-item:hover {
  background: #f3f4f6;
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.news-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.news-source {
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  color: white;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.news-date {
  font-size: 0.75rem;
  color: #9ca3af;
}

.news-title {
  font-size: 1rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.5rem;
  line-height: 1.4;
}

.news-description {
  font-size: 0.875rem;
  color: #4b5563;
  line-height: 1.6;
  margin-bottom: 0.75rem;
}

.news-image {
  margin-top: 0.75rem;
  border-radius: 8px;
  overflow: hidden;
}

.news-image img {
  width: 100%;
  height: auto;
  max-height: 200px;
  object-fit: cover;
  display: block;
}

/* Right Sidebar */
.right-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  grid-column: 2;
  grid-row: 1 / 3;
}

section {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

section h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1rem;
}

.incidents-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.incident-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 8px;
  background: #f9fafb;
  border-left: 4px solid;
}

.incident-card.high { border-color: #ef4444; }
.incident-card.medium { border-color: #f59e0b; }
.incident-card.low { border-color: #10b981; }

.incident-icon {
  font-size: 1.5rem;
}

.incident-details h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
}

.incident-details p {
  color: #4b5563;
  font-size: 0.875rem;
}

.incident-details time {
  color: #6b7280;
  font-size: 0.75rem;
}

.loading-small {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.spinner-small {
  width: 30px;
  height: 30px;
  border: 3px solid #f3f4f6;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 0.5rem;
}

.empty-incidents {
  text-align: center;
  padding: 2rem;
  color: #9ca3af;
  font-size: 0.875rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.stat-card {
  background: #f9fafb;
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
}

.stat-card h3 {
  font-size: 0.875rem;
  color: #4b5563;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
}

@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .news-feed-section,
  .right-sidebar {
    grid-column: auto;
    grid-row: auto;
    max-height: 500px;
  }
}
</style>