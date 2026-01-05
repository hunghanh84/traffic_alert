<script setup>
import { useRouter, RouterLink } from 'vue-router'
import SignupForm from '../components/SignupForm.vue'
import { ref } from 'vue'

const router = useRouter()

function onSuccess() {
  // Sau khi đăng ký thành công, chuyển hướng người dùng về trang đăng nhập
  router.push('/login')
}
</script>

<template>
  <main class="register-page">
    <section class="welcome-section">
      <div class="welcome-content">
        <div class="title-group">
          <h1 class="welcome-title">🧭 CẢNH BÁO GIAO THÔNG</h1>
        </div>
        <p class="welcome-text">Hệ thống cảnh báo giao thông thông minh giúp bạn di chuyển an toàn và hiệu quả hơn.</p>
      </div>
    </section>
    <section class="card">
      <h1 class="title">Đăng ký</h1>
      <p class="lead">Tạo tài khoản mới để sử dụng ứng dụng.</p>
      <SignupForm @success="onSuccess" />
      <div class="links">
        <RouterLink to="/login" class="link">Đã có tài khoản? Đăng nhập</RouterLink>
      </div>
    </section>
  </main>
</template>

<style scoped>
.register-page {
  min-height: 100vh;
  width: 100vw;
  display: grid;
  grid-template-columns: 1fr 1fr;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background: linear-gradient(135deg, #67a6d6 100%, #67a6d6 100%);
}

@media (max-width: 768px) {
  .register-page {
    grid-template-columns: 1fr;
  }
}

.card {
  width: 100%;
  max-width: 480px;
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

.welcome-logo {
  width: 120px;
  height: 120px;
  object-fit: contain;
  margin-right: 1rem;
  filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
}

.title-group {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.welcome-title {
  font-size: 2rem;
  font-weight: 700;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  color: #faae2a
}

.welcome-text {
  font-size: 1.125rem;
  line-height: 1.6;
  opacity: 0.95;
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

.card-header {
  position: absolute;
  top: 1rem;
  right: 1.25rem;
}

.secondary-link {
  color: rgba(0,0,0,0.6);
  background: rgba(255,255,255,0.06);
  padding: 0.35rem 0.6rem;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.95rem;
}

.secondary-link:hover {
  color: #0b5ed7;
  background: rgba(255,255,255,0.12);
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
  justify-content: center;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #eee;
}

.link {
  color: #3b82f6;
  text-decoration: none;
  font-size: 0.95rem;
  transition: color 0.2s;
}

.link:hover {
  color: #2563eb;
  text-decoration: underline;
}
</style>
