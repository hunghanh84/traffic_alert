<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'

const router = useRouter()
const email = ref('')
const sending = ref(false)
const message = ref('')

function sendReset() {
  if (!email.value) {
    message.value = 'Vui lòng nhập email.'
    return
  }
  sending.value = true
  // mock sending
  setTimeout(() => {
    sending.value = false
    message.value = 'Một liên kết đặt lại mật khẩu đã được gửi đến email của bạn (demo).'
    // optional: redirect to login after short delay
    setTimeout(() => router.push('/login'), 2200)
  }, 1200)
}
</script>

<template>
  <main class="auth-page">
    <section class="auth-card">
      <h1 class="title">Quên mật khẩu</h1>
      <p class="lead">Nhập email đã đăng ký, chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu.</p>

      <label class="field">
        <span>Email</span>
        <input v-model="email" type="email" placeholder="you@example.com" />
      </label>

      <button class="btn" :disabled="sending" @click="sendReset">
        {{ sending ? 'Đang gửi...' : 'Gửi liên kết' }}
      </button>

      <p class="message" v-if="message">{{ message }}</p>

      <div class="foot-links">
        <RouterLink to="/login" class="link">Quay lại Đăng nhập</RouterLink>
        <RouterLink to="/register" class="link">Đăng ký</RouterLink>
      </div>
    </section>
  </main>
</template>

<style scoped>
.auth-page {
  min-height: 100vh;
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: linear-gradient(135deg, #4283e4 0%, #67a6d6 100%);
}

.auth-card {
  width: 100%;
  max-width: 480px;
  padding: 2.25rem;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  text-align: center;
}

.title { font-size: 1.5rem; margin-bottom: 0.5rem }
.lead { color:#666; margin-bottom:1.25rem }
.field { display:block; text-align:left; margin-bottom:1rem }
.field input { width:100%; padding:0.6rem 0.8rem; border:1px solid #ddd; border-radius:6px }
.btn { width:100%; padding:0.75rem; background: #3b82f6; color:#fff; border:none; border-radius:8px; cursor:pointer }
.btn:disabled { opacity:0.7; cursor:default }
.message { margin-top:1rem; color: #166534 }
.foot-links { margin-top:1.25rem; display:flex; justify-content:space-between }
.link { color:#3b82f6; text-decoration:none }
</style>