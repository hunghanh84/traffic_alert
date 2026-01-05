<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const currentUser = ref(null)
const showLogoutModal = ref(false)

const menuItems = [
  { path: '/admin/dashboard', icon: '🎛️', label: 'Dashboard', section: 'main' },
  { path: '/admin/alerts', icon: '📋', label: 'Quản lý Bài Đăng', section: 'main' },
  { path: '/admin/events', icon: '🚨', label: 'Quản lý Sự kiện', section: 'main' },
  { path: '/admin/users', icon: '👥', label: 'Quản lý Người dùng', section: 'main' },
  { path: '/admin/notifications', icon: '📢', label: 'Quản lý Thông báo', section: 'main' },
  { path: '/admin/cameras', icon: '📹', label: 'Quản lý Camera', section: 'main' },
  { path: '/admin/statistics', icon: '📊', label: 'Thống kê', section: 'main' },
  { path: '/', icon: '🏠', label: 'Về trang chủ', section: 'footer' },
]

const fetchCurrentUser = async () => {
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch('http://127.0.0.1:8000/api/auth/me', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      currentUser.value = result.data
    }
  } catch (error) {
    console.error('Error fetching user:', error)
  }
}

const logout = () => {
  showLogoutModal.value = true
}

const confirmLogout = () => {
  localStorage.removeItem('access_token')
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  localStorage.removeItem('user_info')
  router.push('/login')
}

const cancelLogout = () => {
  showLogoutModal.value = false
}

onMounted(() => {
  fetchCurrentUser()
})
</script>

<template>
  <div class="admin-sidebar">
    <div class="sidebar-header">
      <div v-if="currentUser" class="user-info">
        <div class="user-avatar">
          <img v-if="currentUser.anh_dai_dien" :src="currentUser.anh_dai_dien" alt="Avatar" />
          <div v-else class="avatar-placeholder">
            {{ (currentUser.ho_ten || currentUser.ten || 'A').charAt(0) }}
          </div>
        </div>
        <div class="user-details">
          <p class="user-name">{{ currentUser.ho_ten || currentUser.ten }}</p>
          <p class="user-role">{{ currentUser.vai_tro === 'admin' ? '👑 Administrator' : 'User' }}</p>
        </div>
      </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div v-if="showLogoutModal" class="modal-overlay" @click="cancelLogout">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>🚪 Xác nhận đăng xuất</h3>
        </div>
        <div class="modal-body">
          <p>Bạn có chắc chắn muốn đăng xuất khỏi Admin Panel?</p>
        </div>
        <div class="modal-footer">
          <button @click="cancelLogout" class="btn-cancel">Hủy</button>
          <button @click="confirmLogout" class="btn-confirm">Đăng xuất</button>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <router-link
        v-for="item in menuItems.filter(i => i.section === 'main')"
        :key="item.path"
        :to="item.path"
        class="nav-item"
        :class="{ active: route.path === item.path }"
      >
        <span class="nav-icon">{{ item.icon }}</span>
        <span class="nav-label">{{ item.label }}</span>
      </router-link>
    </nav>

    <div class="sidebar-footer">
      <router-link
        v-for="item in menuItems.filter(i => i.section === 'footer')"
        :key="item.path"
        :to="item.path"
        class="nav-item footer-link"
      >
        <span class="nav-icon">{{ item.icon }}</span>
        <span class="nav-label">{{ item.label }}</span>
      </router-link>
      
      <button @click="logout" class="logout-btn">
        <span>🚪</span>
        <span>Đăng xuất</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.admin-sidebar {
  width: 260px;
  height: 100vh;
  background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.5);
  z-index: 1000;
  overflow-y: auto;
}

.admin-sidebar::-webkit-scrollbar {
  width: 6px;
}

.admin-sidebar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

.admin-sidebar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

.sidebar-header {
  padding: 2rem 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(0, 0, 0, 0.2);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-radius: 12px;
  border: 1px solid rgba(102, 126, 234, 0.2);
}

.user-avatar img {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #667eea;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.avatar-placeholder {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.user-details {
  flex: 1;
  min-width: 0;
}

.user-name {
  margin: 0 0 0.25rem 0;
  font-weight: 600;
  font-size: 1rem;
  color: white;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  margin: 0;
  font-size: 0.85rem;
  color: #fbbf24;
  font-weight: 600;
}

.sidebar-nav {
  flex: 1;
  padding: 1.5rem 0;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
  position: relative;
}

.nav-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 0;
  background: linear-gradient(90deg, rgba(102, 126, 234, 0.3) 0%, transparent 100%);
  transition: width 0.3s ease;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.nav-item:hover::before {
  width: 100%;
}

.nav-item.active {
  background: linear-gradient(90deg, rgba(102, 126, 234, 0.2) 0%, transparent 100%);
  border-left-color: #667eea;
  color: white;
  box-shadow: inset 0 0 20px rgba(102, 126, 234, 0.1);
}

.nav-icon {
  font-size: 1.25rem;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.nav-label {
  font-weight: 500;
  font-size: 0.95rem;
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(0, 0, 0, 0.2);
}

.footer-link {
  margin-bottom: 1rem;
  border-left: none !important;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
}

.footer-link:hover {
  background: rgba(255, 255, 255, 0.1);
}

.logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1rem;
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%);
  color: #fca5a5;
  border: 1px solid rgba(239, 68, 68, 0.4);
  border-radius: 12px;
  cursor: pointer;
  font-weight: 700;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.logout-btn:hover {
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.3) 0%, rgba(220, 38, 38, 0.3) 100%);
  color: #fecaca;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.logout-btn:active {
  transform: translateY(0);
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  animation: fadeIn 0.2s;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 420px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
  animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  overflow: hidden;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-icon {
  padding: 2rem 2rem 1rem;
  display: flex;
  justify-content: center;
}

.icon-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  box-shadow: 0 8px 16px rgba(239, 68, 68, 0.2);
}

.modal-header {
  padding: 0 2rem 2rem;
  text-align: center;
}

.modal-header h3 {
  margin: 0 0 0.75rem 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

.modal-header p {
  margin: 0;
  color: #6b7280;
  line-height: 1.6;
  font-size: 0.95rem;
}

.modal-footer {
  padding: 1.5rem 2rem 2rem;
  display: flex;
  gap: 1rem;
}

.btn-cancel,
.btn-confirm {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel {
  background: #f3f4f6;
  color: #374151;
  border: 2px solid #e5e7eb;
}

.btn-cancel:hover {
  background: #e5e7eb;
  border-color: #d1d5db;
  transform: translateY(-2px);
}

.btn-confirm {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-confirm:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

.btn-cancel:active,
.btn-confirm:active {
  transform: translateY(0);
}
</style>
