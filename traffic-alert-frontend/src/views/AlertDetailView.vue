<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const alert = ref(null)
const isLoading = ref(true)

const severityColors = {
  low: '#10b981',
  medium: '#f59e0b',
  high: '#ef4444',
  critical: '#dc2626',
}

const severityLabels = {
  low: 'Thấp',
  medium: 'Trung bình',
  high: 'Cao',
  critical: 'Nghiêm trọng',
}

const statusColors = {
  cho_duyet: '#f59e0b',
  da_duyet: '#10b981',
  tu_choi: '#ef4444',
}

const statusLabels = {
  cho_duyet: 'Chờ duyệt',
  da_duyet: 'Đã duyệt',
  tu_choi: 'Từ chối',
}

const typeLabels = {
  traffic: 'Tắc đường',
  flood: 'Ngập đường',
}

const typeIcons = {
  traffic: '🚗',
  flood: '🌊',
}

const fetchAlertDetail = async () => {
  try {
    isLoading.value = true
    const response = await fetch(`http://127.0.0.1:8000/api/alerts/${route.params.id}`)
    const result = await response.json()

    if (result.success) {
      alert.value = result.data
      console.log('🔍 Alert data:', alert.value)
      console.log('📍 phuong_xa:', alert.value.phuong_xa)
      console.log('🏙️ thanh_pho from phuong_xa:', alert.value.phuong_xa?.thanh_pho)
    } else {
      router.push('/alerts')
    }
  } catch (error) {
    console.error('Lỗi khi tải chi tiết cảnh báo:', error)
    router.push('/alerts')
  } finally {
    isLoading.value = false
  }
}

const goBack = () => {
  router.push('/alerts')
}

const editAlert = () => {
  router.push(`/alert/edit/${route.params.id}`)
}

onMounted(() => {
  fetchAlertDetail()
})
</script>

<template>
  <div class="alert-detail-page">
    <div class="detail-container">
      <!-- Loading -->
      <div v-if="isLoading" class="loading-state">
        <div class="spinner"></div>
        <p>Đang tải chi tiết...</p>
      </div>

      <!-- Alert Detail -->
      <div v-else-if="alert" class="detail-content">
        <!-- Header -->
        <header class="detail-header">
          <button @click="goBack" class="back-btn">
            <span>←</span>
            <span>Quay lại</span>
          </button>
          <h1>Chi Tiết Cảnh Báo {{ alert.id }}</h1>
          <button v-if="alert.trang_thai === 'cho_duyet'" @click="editAlert" class="edit-btn">
            <span>✏️</span>
            <span>Chỉnh sửa</span>
          </button>
        </header>

        <!-- Main Info Card -->
        <div class="info-card">
          <div class="card-header">
            <div class="type-badge">
              <span class="type-icon">{{ typeIcons[alert.loai_canh_bao] }}</span>
              <span class="type-label">{{ typeLabels[alert.loai_canh_bao] }}</span>
            </div>
            <span
              class="status-badge"
              :style="{
                backgroundColor: statusColors[alert.trang_thai],
                boxShadow: `0 0 12px ${statusColors[alert.trang_thai]}40`,
              }"
            >
              {{ statusLabels[alert.trang_thai] }}
            </span>
          </div>

          <!-- Location -->
          <div class="info-section">
            <h3 class="section-title">
              <span class="section-icon">📍</span>
              Địa điểm
            </h3>
            <div class="location-detail">
              <p class="location-text">
                <strong>Địa chỉ:</strong> 
                Đường {{ alert.duong?.ten }}, 
                Phường {{ alert.phuong_xa?.ten }}, 
                {{ alert.phuong_xa?.thanh_pho?.ten || 'Đà Nẵng' }}
              </p>
            </div>
          </div>

          <!-- Severity -->
          <div class="info-section">
            <h3 class="section-title">
              <span class="section-icon">⚠️</span>
              Mức độ nghiêm trọng
            </h3>
            <span
              class="severity-badge-large"
              :style="{
                backgroundColor: severityColors[alert.muc_do],
                boxShadow: `0 0 16px ${severityColors[alert.muc_do]}40`,
              }"
            >
              {{ severityLabels[alert.muc_do] }}
            </span>
          </div>

          <!-- Description -->
          <div class="info-section">
            <h3 class="section-title">
              <span class="section-icon">📝</span>
              Mô tả
            </h3>
            <p class="description-text">
              {{ alert.mo_ta || 'Không có mô tả' }}
            </p>
          </div>

          <!-- Media Gallery -->
          <div v-if="alert.media && alert.media.length > 0" class="info-section">
            <h3 class="section-title">
              <span class="section-icon">📷</span>
              Hình ảnh ({{ alert.media.length }})
            </h3>
            <div class="media-gallery">
              <div v-for="media in alert.media" :key="media.id" class="media-item">
                <img
                  :src="media.duong_dan"
                  :alt="`Ảnh ${typeLabels[alert.loai_canh_bao]}`"
                  class="media-image"
                />
                <div class="media-info">
                  <span class="media-format">{{ media.ten_file?.toUpperCase() }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Timestamps -->
          <div class="info-section">
            <h3 class="section-title">
              <span class="section-icon">🕒</span>
              Thông tin thời gian
            </h3>
            <div class="time-info">
              <p><strong>Tạo lúc:</strong> {{ alert.created_at }}</p>
              <p><strong>Cập nhật:</strong> {{ alert.updated_at }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.alert-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  padding: 2rem;
}

.detail-container {
  max-width: 900px;
  margin: 0 auto;
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 4rem;
  background: white;
  border-radius: 24px;
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
  to {
    transform: rotate(360deg);
  }
}

.loading-state p {
  font-size: 1.2rem;
  color: #6b7280;
  font-weight: 500;
}

/* Header */
.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  animation: slideDown 0.6s ease;
}

.detail-header h1 {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.back-btn,
.edit-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  backdrop-filter: blur(10px);
}

.back-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateX(-4px);
}

.edit-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

.edit-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(59, 130, 246, 0.6);
}

/* Info Card */
.info-card {
  background: white;
  border-radius: 24px;
  padding: 2.5rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  animation: fadeIn 0.6s ease;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 2rem;
  border-bottom: 2px solid #e5e7eb;
  margin-bottom: 2rem;
}

.type-badge {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-radius: 16px;
}

.type-icon {
  font-size: 2rem;
}

.type-label {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

.status-badge {
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  color: white;
  font-size: 1rem;
  font-weight: 600;
}

/* Info Sections */
.info-section {
  margin-bottom: 2.5rem;
}

.info-section:last-child {
  margin-bottom: 0;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1.25rem;
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

.location-text {
  font-size: 1.05rem;
  color: #374151;
  margin-bottom: 0.75rem;
  line-height: 1.6;
}

.location-text:last-child {
  margin-bottom: 0;
}

.location-text strong {
  color: #667eea;
  font-weight: 600;
}

.severity-badge-large {
  display: inline-block;
  padding: 1rem 2rem;
  border-radius: 16px;
  color: white;
  font-size: 1.25rem;
  font-weight: 700;
}

.description-text {
  font-size: 1.05rem;
  color: #4b5563;
  line-height: 1.8;
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 12px;
}

/* Media Gallery */
.media-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.media-item {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.media-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
}

.media-image {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
  display: block;
}

.media-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  padding: 1rem;
}

.media-format {
  color: white;
  font-size: 0.875rem;
  font-weight: 600;
}

.time-info {
  background: #f9fafb;
  padding: 1.5rem;
  border-radius: 12px;
}

.time-info p {
  font-size: 1.05rem;
  color: #4b5563;
  margin-bottom: 0.75rem;
  line-height: 1.6;
}

.time-info p:last-child {
  margin-bottom: 0;
}

.time-info strong {
  color: #1f2937;
  font-weight: 600;
}

/* Animations */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .detail-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }

  .detail-header h1 {
    font-size: 1.5rem;
  }

  .info-card {
    padding: 1.5rem;
  }

  .card-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }

  .media-gallery {
    grid-template-columns: 1fr;
  }
}
</style>
