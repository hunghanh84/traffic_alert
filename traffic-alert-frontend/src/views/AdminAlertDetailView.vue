<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const alert = ref(null)
const isLoading = ref(true)

const fetchAlertDetail = async () => {
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/alerts/${route.params.id}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      alert.value = result.data
      console.log('Alert data:', result.data)
      console.log('User data:', result.data.nguoi_dung)
    }
  } catch (error) {
    console.error('Error fetching alert:', error)
  } finally {
    isLoading.value = false
  }
}

const goBack = () => {
  router.push('/admin/alerts')
}

onMounted(() => {
  fetchAlertDetail()
})
</script>

<template>
  <div class="admin-alert-detail">
    <div class="page-header">
      <button @click="goBack" class="back-btn">
        ← Quay lại
      </button>
      <h1>Chi tiết Bài Đăng {{ route.params.id }}</h1>
    </div>

    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Đang tải...</p>
    </div>

    <div v-else-if="alert" class="detail-content">
      <div class="info-grid">
        <div class="info-card">
          <h3>📍 Thông tin địa điểm</h3>
          <div class="info-row">
            <span class="label">Đường:</span>
            <span class="value">{{ alert.duong?.ten || 'N/A' }}</span>
          </div>
          <div class="info-row">
            <span class="label">Phường/Xã:</span>
            <span class="value">{{ alert.duong?.phuong_xa?.ten || 'N/A' }}</span>
          </div>
        </div>

        <div class="info-card">
          <h3>👤 Người gửi</h3>
          <div class="info-row">
            <span class="label">Tên đăng nhập:</span>
            <span class="value">{{ alert.nguoi_dung?.ten_dang_nhap || `User #${alert.nguoi_dung?.id}` }}</span>
          </div>
          <div class="info-row">
            <span class="label">Email:</span>
            <span class="value">{{ alert.nguoi_dung?.email || 'N/A' }}</span>
          </div>
          <div class="info-row">
            <span class="label">ID:</span>
            <span class="value">#{{ alert.nguoi_dung?.id }}</span>
          </div>
        </div>

        <div class="info-card">
          <h3>🚨 Thông tin cảnh báo</h3>
          <div class="info-row">
            <span class="label">Loại:</span>
            <span class="badge" :class="alert.loai_canh_bao">
              {{ alert.loai_canh_bao === 'traffic' ? '🚗 Tắc đường' : '🌊 Ngập lụt' }}
            </span>
          </div>
          <div class="info-row">
            <span class="label">Mức độ:</span>
            <span class="value">{{ alert.muc_do_su_kien?.ten || 'N/A' }}</span>
          </div>
          <div class="info-row">
            <span class="label">Trạng thái:</span>
            <span class="status-badge" :class="alert.trang_thai">
              {{ alert.trang_thai }}
            </span>
          </div>
        </div>

        <div class="info-card">
          <h3>⏰ Thời gian</h3>
          <div class="info-row">
            <span class="label">Tạo lúc:</span>
            <span class="value">{{ new Date(alert.created_at).toLocaleString('vi-VN') }}</span>
          </div>
          <div class="info-row">
            <span class="label">Cập nhật:</span>
            <span class="value">{{ new Date(alert.updated_at).toLocaleString('vi-VN') }}</span>
          </div>
        </div>
      </div>

      <div class="description-card">
        <h3>📝 Mô tả chi tiết</h3>
        <p>{{ alert.mo_ta || 'Không có mô tả' }}</p>
      </div>

      <div v-if="alert.media && alert.media.length > 0" class="media-card">
        <h3>🖼️ Hình ảnh/Video ({{ alert.media.length }})</h3>
        <div class="media-grid">
          <div v-for="media in alert.media" :key="media.id" class="media-item">
            <img v-if="media.loai === 'image'" :src="media.duong_dan" :alt="media.ten_file" />
            <video v-else :src="media.duong_dan" controls></video>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-alert-detail {
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
  max-width: 1200px;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.info-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: all 0.2s;
}

.info-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #667eea;
}

.info-card h3 {
  margin: 0 0 1.25rem 0;
  padding-bottom: 0.75rem;
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 700;
  border-bottom: 2px solid #f3f4f6;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.info-row:last-child {
  border-bottom: none;
}

.label {
  color: #6b7280;
  font-weight: 500;
}

.value {
  color: #1f2937;
  font-weight: 600;
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
}

.badge.traffic {
  background: #dbeafe;
  color: #1e40af;
}

.badge.flood {
  background: #ddd6fe;
  color: #5b21b6;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.cho_duyet {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.da_duyet {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.tu_choi {
  background: #fee2e2;
  color: #991b1b;
}

.description-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 1.5rem;
}

.description-card h3 {
  margin: 0 0 1rem 0;
  color: #1f2937;
}

.description-card p {
  margin: 0;
  color: #374151;
  line-height: 1.6;
}

.media-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.media-card h3 {
  margin: 0 0 1rem 0;
  color: #1f2937;
}

.media-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.media-item img,
.media-item video {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
}
</style>
