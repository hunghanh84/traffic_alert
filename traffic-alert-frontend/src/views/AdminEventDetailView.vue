<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const event = ref(null)
const isLoading = ref(true)

const fetchEventDetail = async () => {
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/admin/events/${route.params.id}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      event.value = result.data
      console.log('Event data:', result.data)
    }
  } catch (error) {
    console.error('Error fetching event:', error)
  } finally {
    isLoading.value = false
  }
}

const goBack = () => {
  router.push('/admin/events')
}

onMounted(() => {
  fetchEventDetail()
})
</script>

<template>
  <div class="admin-event-detail">
    <div class="page-header">
      <button @click="goBack" class="back-btn">
        ← Quay lại
      </button>
      <h1>Chi tiết Sự kiện #{{ route.params.id }}</h1>
    </div>

    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Đang tải...</p>
    </div>

    <div v-else-if="event" class="detail-content">
      <!-- Event Header Card -->
      <div class="event-header-card" :class="event.trang_thai">
        <div class="event-icon">
          🚦
        </div>
        <div class="event-header-info">
          <h2>{{ event.loai_su_kien?.ten || 'Sự kiện giao thông' }}</h2>
          <p class="event-location">{{ event.duong?.ten }} - {{ event.duong?.phuong_xa?.ten }}</p>
          <div class="event-badges">
            <span class="severity-badge" :class="event.muc_do_su_kien?.ma">
              {{ event.muc_do_su_kien?.ten || 'N/A' }}
            </span>
            <span class="status-badge">
              {{ event.trang_thai_ten || event.trang_thai }}
            </span>
          </div>
        </div>
      </div>

      <!-- Main Info Card -->
      <div class="main-info-card">
        <div class="info-section">
          <h3>📍 Thông tin địa điểm</h3>
          <div class="info-grid-3col">
            <div class="info-item">
              <span class="info-label">Thành phố</span>
              <span class="info-value">{{ event.duong?.phuong_xa?.thanh_pho?.ten || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Phường/Xã</span>
              <span class="info-value">{{ event.duong?.phuong_xa?.ten || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Đường</span>
              <span class="info-value">{{ event.duong?.ten || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="info-section">
          <h3>🚨 Chi tiết sự kiện</h3>
          <div class="info-grid-2col">
            <div class="info-item">
              <span class="info-label">Loại sự kiện</span>
              <span class="info-value">{{ event.loai_su_kien?.ten || 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Mức độ</span>
              <span class="severity-badge-inline" :class="event.muc_do_su_kien?.ma">
                {{ event.muc_do_su_kien?.ten || 'N/A' }}
              </span>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="info-section">
          <h3>⏰ Thời gian</h3>
          <div class="info-grid-2col">
            <div class="info-item">
              <span class="info-label">Bắt đầu</span>
              <span class="info-value">{{ event.thoi_gian_bat_dau ? new Date(event.thoi_gian_bat_dau).toLocaleString('vi-VN') : 'N/A' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Kết thúc</span>
              <span class="info-value">{{ event.thoi_gian_ket_thuc ? new Date(event.thoi_gian_ket_thuc).toLocaleString('vi-VN') : 'Chưa kết thúc' }}</span>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="info-section">
          <h3>📝 Mô tả</h3>
          <p class="description-text">{{ event.mo_ta || 'Không có mô tả' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-event-detail {
  padding: 2rem;
  margin-left: 0;
  min-height: 100vh;
}

.page-header {
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.back-btn {
  padding: 0.75rem 1.5rem;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.back-btn:hover {
  background: #f9fafb;
  border-color: #667eea;
}

.page-header h1 {
  font-size: 2rem;
  color: #1f2937;
  margin: 0;
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

.detail-content {
  max-width: 900px;
}

/* Event Header Card */
.event-header-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2.5rem;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.event-icon {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  border: 4px solid rgba(255, 255, 255, 0.3);
  flex-shrink: 0;
}

.event-header-info {
  flex: 1;
}

.event-header-info h2 {
  margin: 0 0 0.5rem 0;
  color: white;
  font-size: 2rem;
  font-weight: 700;
}

.event-location {
  margin: 0 0 1rem 0;
  color: rgba(255, 255, 255, 0.9);
  font-size: 1.1rem;
}

.event-badges {
  display: flex;
  gap: 0.75rem;
}

/* Main Info Card */
.main-info-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.info-section {
  padding: 2rem;
}

.info-section h3 {
  margin: 0 0 1.5rem 0;
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 700;
}

.divider {
  height: 1px;
  background: #e5e7eb;
}

/* Info Grid */
.info-grid-2col {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.info-grid-3col {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.info-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.info-value {
  font-size: 1rem;
  color: #1f2937;
  font-weight: 600;
}

.description-text {
  margin: 0;
  color: #374151;
  line-height: 1.8;
  font-size: 1rem;
}

/* Badges */
.severity-badge,
.severity-badge-inline {
  padding: 0.5rem 1rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  display: inline-block;
}

.severity-badge {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.severity-badge-inline.low {
  background: #d1fae5;
  color: #065f46;
}

.severity-badge-inline.medium {
  background: #fef3c7;
  color: #92400e;
}

.severity-badge-inline.high {
  background: #fed7aa;
  color: #9a3412;
}

.severity-badge-inline.critical {
  background: #fee2e2;
  color: #991b1b;
}

.status-badge {
  padding: 0.5rem 1rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .event-header-card {
    flex-direction: column;
    text-align: center;
  }

  .info-grid-2col,
  .info-grid-3col {
    grid-template-columns: 1fr;
  }
}
</style>
