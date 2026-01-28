<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const statistics = ref(null)
const recentActivities = ref(null)
const isLoading = ref(true)

const fetchStatistics = async () => {
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch('http://127.0.0.1:8000/api/admin/dashboard/statistics', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      statistics.value = result.data
    }
  } catch (error) {
    console.error('Error fetching statistics:', error)
  }
}

const fetchRecentActivities = async () => {
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch('http://127.0.0.1:8000/api/admin/dashboard/activities', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      recentActivities.value = result.data
    }
  } catch (error) {
    console.error('Error fetching activities:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchStatistics(), fetchRecentActivities()])
})
</script>

<template>
  <div class="admin-dashboard">
    <div class="dashboard-header">
      <h1>🎛️ Admin Dashboard</h1>
      <p>Tổng quan hệ thống cảnh báo giao thông</p>
    </div>

    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Đang tải dữ liệu...</p>
    </div>

    <div v-else class="dashboard-content">
      <!-- Statistics Cards -->
      <div class="stats-grid" v-if="statistics">
        <div class="stat-card">
          <div class="stat-icon">📋</div>
          <div class="stat-info">
            <h3>{{ statistics.totals.alerts }}</h3>
            <p>Tổng báo cáo</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">🚨</div>
          <div class="stat-info">
            <h3>{{ statistics.totals.events }}</h3>
            <p>Sự kiện</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <h3>{{ statistics.totals.users }}</h3>
            <p>Người dùng</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">📢</div>
          <div class="stat-info">
            <h3>{{ statistics.totals.notifications }}</h3>
            <p>Thông báo</p>
          </div>
        </div>

        <div class="stat-card pending">
          <div class="stat-icon">⏳</div>
          <div class="stat-info">
            <h3>{{ statistics.pending.alerts }}</h3>
            <p>Chờ duyệt</p>
          </div>
        </div>

        <div class="stat-card active">
          <div class="stat-icon">🔴</div>
          <div class="stat-info">
            <h3>{{ statistics.pending.events }}</h3>
            <p>Sự kiện đang xảy ra</p>
          </div>
        </div>
      </div>

      <!-- Today & Week Stats -->
      <div class="time-stats" v-if="statistics">
        <div class="time-card">
          <h3>📅 Hôm nay</h3>
          <div class="time-info">
            <div class="time-item">
              <span class="label">Báo cáo:</span>
              <span class="value">{{ statistics.today.alerts }}</span>
            </div>
            <div class="time-item">
              <span class="label">Sự kiện:</span>
              <span class="value">{{ statistics.today.events }}</span>
            </div>
          </div>
        </div>

        <div class="time-card">
          <h3>📊 Tuần này</h3>
          <div class="time-info">
            <div class="time-item">
              <span class="label">Báo cáo:</span>
              <span class="value">{{ statistics.this_week.alerts }}</span>
            </div>
            <div class="time-item">
              <span class="label">Sự kiện:</span>
              <span class="value">{{ statistics.this_week.events }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activities -->
      <div class="recent-section" v-if="recentActivities">
        <div class="recent-card">
          <h3>🆕 Báo cáo gần đây</h3>
          <div class="activity-list">
            <div 
              v-for="alert in recentActivities.recent_alerts" 
              :key="alert.id"
              class="activity-item"
            >
              <div class="activity-icon">
                {{ alert.loai_canh_bao === 'traffic' ? '🚗' : '🌊' }}
              </div>
              <div class="activity-details">
                <p class="activity-title">{{ alert.duong }}</p>
                <p class="activity-meta">
                  <span class="badge" :class="alert.trang_thai">{{ alert.trang_thai }}</span>
                  <span>{{ alert.nguoi_dung }}</span>
                  <span>{{ alert.created_at }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="recent-card">
          <h3>⚡ Sự kiện gần đây</h3>
          <div class="activity-list">
            <div 
              v-for="event in recentActivities.recent_events" 
              :key="event.id"
              class="activity-item"
            >
              <div class="activity-icon">🚨</div>
              <div class="activity-details">
                <p class="activity-title">{{ event.loai_su_kien }} - {{ event.duong }}</p>
                <p class="activity-meta">
                  <span class="badge">{{ event.nguon }}</span>
                  <span>{{ event.created_at }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-dashboard {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  margin-left: 0; /* Account for sidebar width (260px + 20px padding) */
  min-height: 100vh;
}

.dashboard-header {
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  font-size: 2rem;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.dashboard-header p {
  color: #6b7280;
  font-size: 1.1rem;
}

.loading {
  text-align: center;
  padding: 4rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f4f6;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-card.pending {
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
}

.stat-card.active {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.stat-icon {
  font-size: 2.5rem;
}

.stat-info h3 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.stat-info p {
  color: #6b7280;
  margin: 0;
  font-size: 0.9rem;
}

.time-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.time-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.time-card h3 {
  margin: 0 0 1rem 0;
  color: #1f2937;
}

.time-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.time-item {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.time-item .label {
  color: #6b7280;
}

.time-item .value {
  font-weight: 700;
  color: #667eea;
  font-size: 1.1rem;
}

.recent-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
}

.recent-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.recent-card h3 {
  margin: 0 0 1rem 0;
  color: #1f2937;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  max-height: 400px;
  overflow-y: auto;
}

.activity-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  transition: background 0.2s;
}

.activity-item:hover {
  background: #f3f4f6;
}

.activity-icon {
  font-size: 1.5rem;
}

.activity-details {
  flex: 1;
}

.activity-title {
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.activity-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.85rem;
  color: #6b7280;
  margin: 0;
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.badge.cho_duyet {
  background: #fef3c7;
  color: #92400e;
}

.badge.da_duyet {
  background: #d1fae5;
  color: #065f46;
}

.badge.tu_choi {
  background: #fee2e2;
  color: #991b1b;
}

.badge.he_thong {
  background: #dbeafe;
  color: #1e40af;
}
</style>
