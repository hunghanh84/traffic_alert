<script setup>
import { RouterLink, RouterView, useRoute } from 'vue-router'
import { ref, computed } from 'vue'
import './assets/reset.css'

const isSidebarOpen = ref(true)
const route = useRoute()

// Lấy thông tin user từ localStorage
const user = ref(JSON.parse(localStorage.getItem('user_info') || 'null'))

const isAuthPage = computed(() => {
  return ['/login', '/register', '/forgot-password'].includes(route.path)
})

const isAdminPage = computed(() => {
  return route.path.startsWith('/admin')
})

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
}
// Lắng nghe sự kiện để cập nhật thông tin user ngay lập tức khi đổi ở trang Account
window.addEventListener('storage', () => {
  user.value = JSON.parse(localStorage.getItem('user_info') || 'null')
})
</script>
<template>
  <div class="app-container" :class="{ 'auth-layout': isAuthPage }">
    <!-- Sidebar - Hidden for auth and admin pages -->
    <aside v-if="!isAuthPage && !isAdminPage" class="sidebar" :class="{ 'sidebar-closed': !isSidebarOpen }">
      <div class="sidebar-header">
        <h1 class="app-title" v-show="isSidebarOpen">🧭 CẢNH BÁO GIAO THÔNG</h1>
        <button
          class="toggle-btn"
          @click="toggleSidebar"
          :title="isSidebarOpen ? 'Thu gọn' : 'Mở rộng'"
        >
          {{ isSidebarOpen ? '←' : '→' }}
        </button>
      </div>

      <nav class="nav-menu">
        <RouterLink to="/news" class="nav-item">
          <span class="nav-icon">📰</span>
          <span class="nav-label">Bản tin</span>
        </RouterLink>

        <RouterLink to="/routes" class="nav-item">
          <span class="nav-icon">📍</span>
          <span class="nav-label">Chi tiết tuyến</span>
        </RouterLink>

        <RouterLink to="/map" class="nav-item">
          <span class="nav-icon">🗺️</span>
          <span class="nav-label">Bản đồ thời gian thực</span>
        </RouterLink>

        <RouterLink to="/alert" class="nav-item">
          <span class="nav-icon">🚨</span>
          <span class="nav-label">Gửi cảnh báo</span>
        </RouterLink>

        <RouterLink to="/alerts" class="nav-item">
          <span class="nav-icon">📋</span>
          <span class="nav-label">Danh sách cảnh báo</span>
        </RouterLink>
      </nav>

      <div class="sidebar-footer">
        <!-- Thông tin user tóm tắt -->
        <div class="user-info-mini" v-if="user && isSidebarOpen">
          <div class="header-avatar">{{ user.ten_dang_nhap?.charAt(0).toUpperCase() }}</div>
          <div class="header-text">
            <span class="u-name">{{ user.ten_dang_nhap }}</span>
            <span class="u-status">Trực tuyến</span>
          </div>
        </div>

        <RouterLink to="/account" class="nav-item">
          <span class="nav-icon">👤</span>
          <span class="nav-label">Tài khoản</span>
        </RouterLink>
      </div>
    </aside>

    <!-- Main Content -->
    <main
      class="main-content"
      :class="{ 
        'main-expanded': !isSidebarOpen && !isAuthPage && !isAdminPage, 
        'auth-content': isAuthPage,
        'admin-content': isAdminPage
      }"
    >
      <RouterView />
    </main>
  </div>
</template>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

:root {
  --sidebar-width: 280px;
  --sidebar-collapsed-width: 80px;
  --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  --sidebar-bg: linear-gradient(180deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
  --text-light: #ffffff;
  --text-muted: #a0aec0;
  --auth-bg: #f7fafc;
  --glow-color: rgba(102, 126, 234, 0.4);
}

* {
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
  font-family:
    'Inter',
    system-ui,
    -apple-system,
    sans-serif;
  height: 100vh;
  overflow: hidden;
  background: #0f0c29;
}

.app-container {
  display: flex;
  height: 100vh;
  width: 100vw;
  overflow: hidden;
  position: fixed;
  top: 0;
  left: 0;
}

/* Sidebar Styles */
.sidebar {
  width: var(--sidebar-width);
  background: var(--sidebar-bg);
  color: var(--text-light);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  bottom: 0;
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(10px);
  z-index: 1000;
}

.sidebar::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  pointer-events: none;
}

.sidebar-closed {
  width: var(--sidebar-collapsed-width);
}

.sidebar-header {
  padding: 1.5rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  position: relative;
  z-index: 1;
}

.sidebar-closed .sidebar-header {
  justify-content: center;
  padding: 1.5rem 0.5rem;
}

.app-title {
  font-size: 1.1rem;
  font-weight: 700;
  margin: 0;
  white-space: nowrap;
  background: linear-gradient(135deg, #667eea 0%, #f5576c 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.5px;
  transition: opacity 0.3s ease;
}

.toggle-btn {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: var(--text-light);
  cursor: pointer;
  padding: 0.5rem 0.75rem;
  font-size: 1.25rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  flex-shrink: 0;
}

.sidebar-closed .toggle-btn {
  padding: 0.6rem;
  font-size: 1.4rem;
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}

.toggle-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.sidebar-closed .toggle-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
}

.nav-menu {
  padding: 1.5rem 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  overflow-y: auto;
  position: relative;
  z-index: 1;
}

.nav-menu::-webkit-scrollbar {
  width: 6px;
}

.nav-menu::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

.nav-menu::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 0.875rem 1rem;
  color: var(--text-light);
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 12px;
  margin: 0 0.75rem;
  gap: 1rem;
  position: relative;
  overflow: hidden;
  font-weight: 500;
  font-size: 0.95rem;
}

.nav-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.nav-item:hover::before {
  opacity: 1;
}

.nav-item.router-link-active {
  background: var(--primary-gradient);
  box-shadow:
    0 8px 16px var(--glow-color),
    0 0 20px var(--glow-color);
  transform: translateX(4px);
}

.nav-item.router-link-active::before {
  opacity: 0;
}

.nav-icon {
  font-size: 1.5rem;
  min-width: 1.5rem;
  text-align: center;
  position: relative;
  z-index: 1;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.nav-label {
  white-space: nowrap;
  opacity: 1;
  transition: opacity 0.3s ease;
  position: relative;
  z-index: 1;
  font-weight: 500;
}

.sidebar-closed .nav-label {
  opacity: 0;
  pointer-events: none;
}

.sidebar-closed .app-title {
  opacity: 0;
  pointer-events: none;
}

.nav-divider {
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.2) 50%,
    transparent 100%
  );
  margin: 0.75rem 1rem;
}

.sidebar-footer {
  margin-top: auto;
  padding: 1rem 0;
  border-top: 1px solid rgba(255, 255, 255, 0.15);
  background: rgba(0, 0, 0, 0.2);
  position: relative;
  z-index: 1;
}

.sidebar-footer .nav-item {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-footer .nav-item:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.2);
}

.sidebar-footer .nav-item.router-link-active {
  background: var(--secondary-gradient);
  border: none;
}

/* User Info Mini in Sidebar */
.user-info-mini {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  margin: 0 0.75rem 1rem 0.75rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.header-avatar {
  width: 42px;
  height: 42px;
  background: var(--primary-gradient);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  flex-shrink: 0;
}

.header-text {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.u-name {
  font-size: 0.95rem;
  font-weight: 700;
  color: white;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.u-status {
  font-size: 0.75rem;
  color: #48bb78;
  font-weight: 600;
}

.sidebar-closed .logout-btn-sidebar .nav-label {
  display: none;
}

.sidebar-closed .user-info-mini {
  display: none;
}

/* Main Content Styles */
.main-content {
  flex: 1;
  margin-left: var(--sidebar-width);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  height: 100vh;
  overflow-y: auto;
  overflow-x: hidden;
  position: relative;
}

.main-content::-webkit-scrollbar {
  width: 8px;
}

.main-content::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
}

.main-content::-webkit-scrollbar-thumb {
  background: rgba(102, 126, 234, 0.3);
  border-radius: 4px;
}

.main-content::-webkit-scrollbar-thumb:hover {
  background: rgba(102, 126, 234, 0.5);
}

/* Auth Layout Styles */
.auth-layout .main-content {
  margin-left: 0;
  background: var(--auth-bg);
}

.auth-content {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.main-expanded {
  margin-left: var(--sidebar-collapsed-width);
}

.admin-content {
  margin-left: 0 !important;
}

/* Animations */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.nav-item {
  animation: slideIn 0.3s ease forwards;
}

.nav-item:nth-child(1) {
  animation-delay: 0.05s;
}
.nav-item:nth-child(2) {
  animation-delay: 0.1s;
}
.nav-item:nth-child(3) {
  animation-delay: 0.15s;
}
.nav-item:nth-child(4) {
  animation-delay: 0.2s;
}
.nav-item:nth-child(5) {
  animation-delay: 0.25s;
}

@media (max-width: 768px) {
  .sidebar {
    width: var(--sidebar-collapsed-width);
  }
  .main-content {
    margin-left: var(--sidebar-collapsed-width);
  }
  .nav-label {
    display: none;
  }
  .app-title {
    display: none;
  }
}
</style>
