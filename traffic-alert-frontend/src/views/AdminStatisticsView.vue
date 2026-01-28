<template>
  <div class="admin-statistics">
    <div class="header-section">
      <div class="header-content">
        <h1>📊 Thống kê & Báo cáo</h1>
        <p class="subtitle">Tổng quan hoạt động hệ thống</p>
      </div>
      <div class="header-actions">
        <button @click="refreshData" class="btn-refresh" :class="{ loading: isLoading }">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
          </svg>
          Làm mới
        </button>
        <button @click="exportToExcel" class="btn-export">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
          </svg>
          Xuất Excel
        </button>
      </div>
    </div>

    <!-- Date Range Filter -->
    <div class="filter-section">
      <div class="filter-group">
        <label>Từ ngày:</label>
        <input type="date" v-model="startDate" class="date-input" />
      </div>
      <div class="filter-group">
        <label>Đến ngày:</label>
        <input type="date" v-model="endDate" class="date-input" />
      </div>
      <button @click="applyFilter" class="btn-filter">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        Áp dụng
      </button>
      <button @click="clearFilter" class="btn-clear" v-if="startDate || endDate">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
        Xóa bộ lọc
      </button>
    </div>

    <!-- Overview Cards -->
    <div class="overview-grid">
      <div class="stat-card" v-for="(card, index) in statCards" :key="index" :style="{ animationDelay: `${index * 0.1}s` }">
        <div class="stat-icon" :style="{ background: card.gradient }">
          {{ card.icon }}
        </div>
        <div class="stat-info">
          <span class="stat-value">{{ card.value }}</span>
          <span class="stat-label">{{ card.label }}</span>
          <span class="stat-sub" v-if="card.sub">{{ card.sub }}</span>
        </div>
        <div class="stat-trend" v-if="card.trend">
          <span :class="card.trend > 0 ? 'trend-up' : 'trend-down'">
            {{ card.trend > 0 ? '↑' : '↓' }} {{ Math.abs(card.trend) }}%
          </span>
        </div>
      </div>
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
      <!-- Events by Type -->
      <div class="chart-card">
        <div class="chart-header">
          <h3>📈 Sự kiện theo loại</h3>
          <div class="chart-legend">
            <span class="legend-item">
              <span class="legend-dot" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)"></span>
              Số lượng
            </span>
          </div>
        </div>
        <div class="chart-content">
          <div v-if="eventsByType.length > 0" class="bar-chart">
            <div v-for="item in eventsByType" :key="item.label" class="bar-item">
              <div class="bar-label">{{ item.label }}</div>
              <div class="bar-container">
                <div class="bar-fill" :style="{ width: getPercentage(item.value, maxEventType) + '%' }">
                  <span class="bar-value">{{ item.value }}</span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="no-data">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <p>Không có dữ liệu</p>
          </div>
        </div>
      </div>

      <!-- Events Trend -->
      <div class="chart-card">
        <div class="chart-header">
          <h3>📅 Xu hướng 7 ngày</h3>
          <div class="chart-legend">
            <span class="legend-item">
              <span class="legend-dot" style="background: #10b981"></span>
              Sự kiện mới
            </span>
          </div>
        </div>
        <div class="chart-content">
          <div v-if="eventsTrend.length > 0" class="line-chart">
            <div v-for="item in eventsTrend" :key="item.date" class="trend-item">
              <span class="trend-date">{{ formatDate(item.date) }}</span>
              <div class="trend-bar">
                <div class="trend-fill" :style="{ width: getPercentage(item.count, maxTrend) + '%' }"></div>
              </div>
              <span class="trend-count">{{ item.count }}</span>
            </div>
          </div>
          <div v-else class="no-data">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <p>Không có dữ liệu</p>
          </div>
        </div>
      </div>

      <!-- AI by Label -->
      <div class="chart-card">
        <div class="chart-header">
          <h3>🤖 AI theo nhãn</h3>
        </div>
        <div class="chart-content">
          <div v-if="aiByLabel.length > 0" class="pie-list">
            <div v-for="item in aiByLabel" :key="item.label" class="pie-item">
              <div class="pie-color" :style="{ background: getColorForLabel(item.label) }"></div>
              <span class="pie-label">{{ item.label }}</span>
              <div class="pie-progress">
                <div class="pie-progress-bar" :style="{ width: getPercentage(item.value, totalAI) + '%', background: getColorForLabel(item.label) }"></div>
              </div>
              <span class="pie-value">{{ item.value }}</span>
            </div>
          </div>
          <div v-else class="no-data">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <p>Không có dữ liệu</p>
          </div>
        </div>
      </div>

      <!-- Top Users -->
      <div class="chart-card">
        <div class="chart-header">
          <h3>🏆 Top người dùng</h3>
        </div>
        <div class="chart-content">
          <div v-if="topUsers.length > 0" class="top-list">
            <div v-for="(user, index) in topUsers" :key="user.name" class="top-item">
              <span class="top-rank" :class="'rank-' + (index + 1)">{{ index + 1 }}</span>
              <span class="top-name">{{ user.name }}</span>
              <div class="top-progress">
                <div class="top-progress-bar" :style="{ width: getPercentage(user.count, maxUserCount) + '%' }"></div>
              </div>
              <span class="top-count">{{ user.count }}</span>
            </div>
          </div>
          <div v-else class="no-data">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <p>Không có dữ liệu</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const isLoading = ref(false)
const stats = ref({})
const eventsByType = ref([])
const eventsTrend = ref([])
const aiByLabel = ref([])
const topUsers = ref([])
const startDate = ref('')
const endDate = ref('')

const statCards = computed(() => [
  {
    icon: '🚨',
    value: stats.value.total_events || 0,
    label: 'Tổng sự kiện',
    sub: `${stats.value.active_events || 0} đang hoạt động`,
    gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    trend: 12
  },
  {
    icon: '👥',
    value: stats.value.total_users || 0,
    label: 'Người dùng',
    gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    trend: 8
  },
  {
    icon: '🤖',
    value: stats.value.total_ai_results || 0,
    label: 'Kết quả AI',
    sub: `${stats.value.verified_ai || 0} đã xác minh`,
    gradient: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    trend: -3
  },
  {
    icon: '📢',
    value: stats.value.total_notifications || 0,
    label: 'Thông báo',
    sub: `${stats.value.sent_notifications || 0} đã gửi`,
    gradient: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    trend: 15
  }
])

const maxEventType = computed(() => Math.max(...eventsByType.value.map(i => i.value), 1))
const maxTrend = computed(() => Math.max(...eventsTrend.value.map(i => i.count), 1))
const totalAI = computed(() => aiByLabel.value.reduce((sum, item) => sum + item.value, 0))
const maxUserCount = computed(() => Math.max(...topUsers.value.map(u => u.count), 1))

onMounted(() => {
  fetchAllStats()
})

function getAuthHeaders() {
  const token = localStorage.getItem('access_token') || localStorage.getItem('token')
  return {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
}

async function fetchAllStats() {
  isLoading.value = true
  await Promise.all([
    fetchOverview(),
    fetchEventsByType(),
    fetchEventsTrend(),
    fetchAIByLabel(),
    // fetchTopUsers() // Tạm thời disable - không quan trọng
  ])
  isLoading.value = false
}

async function fetchOverview() {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/admin/statistics/overview', {
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) stats.value = data.data
  } catch (err) {
    console.error('Error:', err)
  }
}

async function fetchEventsByType() {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/admin/statistics/events-by-type', {
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) eventsByType.value = data.data
  } catch (err) {
    console.error('Error:', err)
  }
}

async function fetchEventsTrend() {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/admin/statistics/events-trend', {
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) eventsTrend.value = data.data
  } catch (err) {
    console.error('Error:', err)
  }
}

async function fetchAIByLabel() {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/admin/statistics/ai-by-label', {
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) aiByLabel.value = data.data
  } catch (err) {
    console.error('Error:', err)
  }
}

async function fetchTopUsers() {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/admin/statistics/top-users', {
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) topUsers.value = data.data
  } catch (err) {
    console.error('Error:', err)
  }
}

async function refreshData() {
  await fetchAllStats()
}

function applyFilter() {
  if (startDate.value && endDate.value && startDate.value > endDate.value) {
    alert('Ngày bắt đầu phải nhỏ hơn ngày kết thúc')
    return
  }
  fetchAllStats()
}

function clearFilter() {
  startDate.value = ''
  endDate.value = ''
  fetchAllStats()
}

async function exportToExcel() {
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    
    if (!token) {
      alert('Vui lòng đăng nhập lại')
      return
    }
    
    let url = 'http://127.0.0.1:8000/api/admin/statistics/export'
    const params = new URLSearchParams()
    if (startDate.value) params.append('start_date', startDate.value)
    if (endDate.value) params.append('end_date', endDate.value)
    if (params.toString()) url += '?' + params.toString()
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
      }
    })
    
    if (!response.ok) {
      const errorText = await response.text()
      console.error('Export error:', errorText)
      alert(`Lỗi khi xuất file: ${response.status} ${response.statusText}`)
      return
    }
    
    const blob = await response.blob()
    const downloadUrl = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = downloadUrl
    a.download = `thong-ke-${new Date().toISOString().split('T')[0]}.xlsx`
    document.body.appendChild(a)
    a.click()
    window.URL.revokeObjectURL(downloadUrl)
    document.body.removeChild(a)
    
    console.log('✅ Export thành công!')
  } catch (err) {
    console.error('Export error:', err)
    alert('Có lỗi khi xuất file Excel: ' + err.message)
  }
}

function getPercentage(value, max) {
  return Math.max((value / max) * 100, 5)
}

function getColorForLabel(label) {
  const colors = {
    traffic_jam: '#f59e0b',
    traffic: '#f59e0b',
    accident: '#ef4444',
    flood: '#3b82f6',
    construction: '#8b5cf6',
    normal: '#10b981'
  }
  return colors[label] || '#6b7280'
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' })
}
</script>

<style scoped>
.admin-statistics {
  padding: 2rem;
  max-width: 1600px;
  margin: 0 auto;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  animation: slideDown 0.5s ease-out;
}

.header-content h1 {
  font-size: 2.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin: 0;
  font-weight: 800;
}

.subtitle {
  color: #64748b;
  margin: 0.5rem 0 0 0;
  font-size: 1rem;
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.btn-refresh,
.btn-export {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.btn-refresh {
  background: white;
  color: #667eea;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-refresh:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.btn-refresh.loading svg {
  animation: spin 1s linear infinite;
}

.btn-export {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 6px rgba(102, 126, 234, 0.4);
}

.btn-export:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(102, 126, 234, 0.6);
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.filter-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  animation: fadeInUp 0.4s ease-out;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-group label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #4a5568;
  white-space: nowrap;
}

.date-input {
  padding: 0.625rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.875rem;
  color: #1a202c;
  transition: all 0.3s ease;
  background: white;
}

.date-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-filter,
.btn-clear {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-filter {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 6px rgba(102, 126, 234, 0.4);
}

.btn-filter:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(102, 126, 234, 0.6);
}

.btn-clear {
  background: #f1f5f9;
  color: #64748b;
}

.btn-clear:hover {
  background: #e2e8f0;
  color: #1a202c;
}

.overview-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.75rem;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  display: flex;
  gap: 1.25rem;
  align-items: center;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  animation: fadeInUp 0.5s ease-out;
  animation-fill-mode: both;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: white;
  flex-shrink: 0;
}

.stat-info {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.stat-value {
  font-size: 2.25rem;
  font-weight: 800;
  color: #1a202c;
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  color: #64748b;
  margin-top: 0.5rem;
  font-weight: 500;
}

.stat-sub {
  font-size: 0.75rem;
  color: #10b981;
  margin-top: 0.5rem;
  font-weight: 600;
}

.stat-trend {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
}

.trend-up {
  color: #10b981;
  background: #d1fae5;
}

.trend-down {
  color: #ef4444;
  background: #fee2e2;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.chart-card {
  background: white;
  padding: 1.75rem;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  transition: all 0.3s ease;
  animation: fadeInUp 0.6s ease-out;
}

.chart-card:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.chart-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #1a202c;
  font-weight: 700;
}

.chart-legend {
  display: flex;
  gap: 1rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #64748b;
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.chart-content {
  min-height: 250px;
}

.bar-chart {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.bar-item {
  display: grid;
  grid-template-columns: 140px 1fr;
  gap: 1rem;
  align-items: center;
}

.bar-label {
  font-size: 0.875rem;
  color: #4a5568;
  font-weight: 600;
}

.bar-container {
  background: #f1f5f9;
  border-radius: 10px;
  height: 40px;
  position: relative;
  overflow: hidden;
}

.bar-fill {
  background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
  height: 100%;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding-right: 0.75rem;
  min-width: 50px;
  transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
}

.bar-value {
  color: white;
  font-weight: 700;
  font-size: 0.875rem;
}

.line-chart {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.trend-item {
  display: grid;
  grid-template-columns: 90px 1fr 50px;
  gap: 1rem;
  align-items: center;
}

.trend-date {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 600;
}

.trend-bar {
  background: #f1f5f9;
  height: 32px;
  border-radius: 8px;
  overflow: hidden;
}

.trend-fill {
  background: linear-gradient(90deg, #10b981 0%, #059669 100%);
  height: 100%;
  border-radius: 8px;
  transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.trend-count {
  font-weight: 700;
  color: #1a202c;
  font-size: 0.95rem;
  text-align: right;
}

.pie-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.pie-item {
  display: grid;
  grid-template-columns: 20px 120px 1fr 60px;
  gap: 1rem;
  align-items: center;
}

.pie-color {
  width: 20px;
  height: 20px;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.pie-label {
  font-size: 0.875rem;
  color: #4a5568;
  font-weight: 600;
}

.pie-progress {
  background: #f1f5f9;
  height: 8px;
  border-radius: 4px;
  overflow: hidden;
}

.pie-progress-bar {
  height: 100%;
  border-radius: 4px;
  transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.pie-value {
  font-weight: 700;
  color: #1a202c;
  text-align: right;
}

.top-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.top-item {
  display: grid;
  grid-template-columns: 36px 140px 1fr 60px;
  gap: 1rem;
  align-items: center;
  padding: 1rem;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 12px;
  transition: all 0.3s ease;
}

.top-item:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.top-rank {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.95rem;
  box-shadow: 0 2px 4px rgba(102, 126, 234, 0.4);
}

.top-rank.rank-1 {
  background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
  color: #1a202c;
}

.top-rank.rank-2 {
  background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%);
  color: #1a202c;
}

.top-rank.rank-3 {
  background: linear-gradient(135deg, #cd7f32 0%, #e8a87c 100%);
  color: white;
}

.top-name {
  font-size: 0.875rem;
  color: #1a202c;
  font-weight: 600;
}

.top-progress {
  background: #cbd5e1;
  height: 8px;
  border-radius: 4px;
  overflow: hidden;
}

.top-progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
  border-radius: 4px;
  transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.top-count {
  font-weight: 700;
  color: #667eea;
  text-align: right;
  font-size: 1rem;
}

.no-data {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 250px;
  color: #94a3b8;
}

.no-data svg {
  margin-bottom: 1rem;
  opacity: 0.5;
}

.no-data p {
  font-size: 0.875rem;
  margin: 0;
}

@media (max-width: 1400px) {
  .overview-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 1024px) {
  .charts-grid {
    grid-template-columns: 1fr;
  }
  
  .header-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
}

@media (max-width: 768px) {
  .overview-grid {
    grid-template-columns: 1fr;
  }
  
  .admin-statistics {
    padding: 1rem;
  }
}
</style>
