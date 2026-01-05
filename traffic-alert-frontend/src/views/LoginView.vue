<script setup>
import { useRouter, RouterLink } from 'vue-router'
import LoginForm from '../components/LoginForm.vue'
import { ref } from 'vue'

const router = useRouter()
const user = ref(null)

function onLogin(u) {
  user.value = u
  // store a simple flag (demo only)
  try { localStorage.setItem('user', JSON.stringify(u)) } catch (e) {}
  
  // Check if user is admin and redirect accordingly
  if (u.vai_tro === 'admin') {
    router.push('/admin/dashboard')
  } else {
    router.push('/')
  }
}
</script>
<template>
  <main class="login-page">
    <section class="welcome-section">
      <div class="welcome-content">
        <h1 class="welcome-title">🧭 CẢNH BÁO GIAO THÔNG</h1>
        <p class="welcome-text">Hệ thống cảnh báo giao thông thông minh giúp bạn di chuyển an toàn và hiệu quả hơn.</p>
      </div>
    </section>
    <section class="card">
      <h1 class="title">Đăng nhập</h1>
      <p class="lead">Đăng nhập để tiếp tục vào ứng dụng.</p>
      <LoginForm @login="onLogin" />
      <div class="links">
        <RouterLink to="/forgot-password" class="link">Quên mật khẩu?</RouterLink>
        <RouterLink to="/register" class="link">Đăng ký tài khoản</RouterLink>
      </div>
    </section>
  </main>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  width: 100vw;
  display: grid;
  grid-template-columns: 1fr 1fr;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background: linear-gradient(135deg,#67a6d6 100%, #67a6d6 100%);
}

@media (max-width: 768px) {
  .login-page {
    grid-template-columns: 1fr;
  }
}

.card {
  width: 100%;
  max-width: 440px;
  padding: 2.5rem;
  border-radius: 0;
  background: #ffffff;
  box-shadow: none;
  justify-self: center;
  align-self: center;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  box-sizing: border-box;
}

.welcome-section {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  padding: 2rem;
  text-align: center;
  background: linear-gradient(135deg, #67a6d6 100%, rgba(103, 166, 214, 0.8) 100%);
}

.welcome-content {
  max-width: 600px;
}

.welcome-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  color: #faae2a
}

.welcome-text {
  font-size: 1.25rem;
  line-height: 1.6;
  opacity: 0.9;
}

@media (max-width: 768px) {
  .welcome-section {
    display: none;
  }
  
  .card {
    max-width: 100%;
    padding: 2rem;
  }
}

.title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1a1a1a;
  text-align: center;
  margin-bottom: 0.5rem;
}
.lead {
  color: #666;
  text-align: center;
  margin-bottom: 2rem;
  font-size: 0.95rem;
}

.links {
  display: flex;
  justify-content: space-between;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #eee;
}

.link {
  color: #3b82f6;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.2s;
}

.link:hover {
  color: #2563eb;
  text-decoration: underline;
}
</style>
