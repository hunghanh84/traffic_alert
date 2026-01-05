<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const user = ref(null)
const isLoading = ref(true)

const fetchUserDetail = async () => {
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/admin/users/${route.params.id}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      user.value = result.data
    }
  } catch (error) {
    console.error('Error fetching user:', error)
  } finally {
    isLoading.value = false
  }
}

const goBack = () => {
  router.push('/admin/users')
}

onMounted(() => {
  fetchUserDetail()
})
</script>

<template>
  <div class="admin-user-detail">
    <div class="page-header">
      <button @click="goBack" class="back-btn">
        ← Quay lại
      </button>
      <h1>Chi tiết Người dùng #{{ route.params.id }}</h1>
    </div>

    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Đang tải...</p>
    </div>

    <div v-else-if="user" class="detail-content">
      <!-- User Header Card -->
      <div class="user-header-card">
        <div class="user-avatar">
          <div class="avatar-circle">
            {{ user.ten_dang_nhap.charAt(0).toUpperCase() }}
          </div>
        </div>
        <div class="user-header-info">
          <h2>{{ user.ten_dang_nhap }}</h2>
          <p class="user-email">{{ user.email }}</p>
          <div class="user-badges">
            <span class="role-badge" :class="user.vai_tro">
              {{ user.vai_tro === 'admin' ? '👑 Admin' : user.vai_tro === 'dieu_hanh' ? '⚙️ Điều hành' : '👤 Người dùng' }}
            </span>
            <span class="status-badge" :class="user.trang_thai">
              {{ user.trang_thai === 'hoat_dong' ? '✓ Hoạt động' : user.trang_thai === 'khoa' ? '🔒 Khóa' : '⏸ Tạm dừng' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Main Info Card -->
      <div class="main-info-card">
        <div class="info-section">
          <h3>📋 Thông tin chi tiết</h3>
          <div class="info-grid-2col">
            <div class="info-item">
              <span class="info-label">Tên đăng nhập</span>
              <span class="info-value">{{ user.ten_dang_nhap }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Email</span>
              <span class="info-value">{{ user.email }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Số điện thoại</span>
              <span class="info-value">{{ user.so_dien_thoai || 'Chưa cập nhật' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Vai trò</span>
              <span class="info-value">{{ user.vai_tro === 'admin' ? 'Quản trị viên' : user.vai_tro === 'dieu_hanh' ? 'Điều hành' : 'Người dùng' }}</span>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="info-section">
          <h3>📍 Khu vực hoạt động</h3>
          <div class="info-grid-3col">
            <div class="info-item">
              <span class="info-label">Thành phố</span>
              <span class="info-value">{{ user.phuong_xa?.thanh_pho?.ten || 'Chưa cập nhật' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Phường/Xã</span>
              <span class="info-value">{{ user.phuong_xa?.ten || 'Chưa cập nhật' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Khu vực</span>
              <span class="info-value">{{ user.khu_vuc?.ten || 'Chưa cập nhật' }}</span>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="info-section">
          <h3>📊 Hoạt động</h3>
          <div class="stats-grid">
            <div class="stat-item">
              <div class="stat-icon">📝</div>
              <div class="stat-info">
                <span class="stat-value">{{ user.bai_dang_count || 0 }}</span>
                <span class="stat-label">Bài đăng</span>
              </div>
            </div>
            <div class="stat-item">
              <div class="stat-icon">📅</div>
              <div class="stat-info">
                <span class="stat-value">{{ user.created_at }}</span>
                <span class="stat-label">Ngày tham gia</span>
              </div>
            </div>
            <div class="stat-item">
              <div class="stat-icon">🔄</div>
              <div class="stat-info">
                <span class="stat-value">{{ user.updated_at }}</span>
                <span class="stat-label">Cập nhật lần cuối</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-user-detail {
  padding: 2rem;
  margin-left: 280px;
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

/* User Header Card */
.user-header-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2.5rem;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.user-avatar {
  flex-shrink: 0;
}

.avatar-circle {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  font-weight: 700;
  color: white;
  border: 4px solid rgba(255, 255, 255, 0.3);
}

.user-header-info {
  flex: 1;
}

.user-header-info h2 {
  margin: 0 0 0.5rem 0;
  color: white;
  font-size: 2rem;
  font-weight: 700;
}

.user-email {
  margin: 0 0 1rem 0;
  color: rgba(255, 255, 255, 0.9);
  font-size: 1.1rem;
}

.user-badges {
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

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  background: #f9fafb;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}

.stat-icon {
  font-size: 2rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.stat-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.stat-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
}

/* Badges */
.role-badge {
  padding: 0.5rem 1rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
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
  .user-header-card {
    flex-direction: column;
    text-align: center;
  }

  .info-grid-2col,
  .info-grid-3col,
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
