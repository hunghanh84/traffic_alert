<script setup>
import { ref, computed } from 'vue'

const email = ref('')
const password = ref('')
const submitting = ref(false)
const error = ref('')

// define emit at top-level (script setup macro)
const emit = defineEmits(['login'])

const passwordValid = computed(() => password.value.length >= 6)

function reset() {
  email.value = ''
  password.value = ''
  error.value = ''
}

async function submitForm(e) {
  e.preventDefault()
  error.value = ''
  
  if (!email.value) {
    error.value = 'Vui lòng nhập email hoặc tên đăng nhập.'
    return
  }
  if (!passwordValid.value) {
    error.value = 'Mật khẩu phải có ít nhất 6 ký tự.'
    return
  }

  submitting.value = true
  try {
    const response = await fetch('http://localhost:8000/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        login: email.value,
        mat_khau: password.value
      })
    })

    const result = await response.json()

    if (!response.ok) {
      throw new Error(result.message || 'Đăng nhập thất bại')
    }

    // Lưu token vào localStorage
    localStorage.setItem('access_token', result.data.access_token)
    localStorage.setItem('user_info', JSON.stringify(result.data.user))

    // Trả kết quả về cho parent
    emit('login', result.data.user)
    reset()
  } catch (err) {
    error.value = err.message || 'Đăng nhập thất bại. Vui lòng thử lại.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <form class="login-form" @submit="submitForm">
    <div class="field">
      <label for="email">Email / Tên đăng nhập</label>
      <input id="email" type="text" v-model="email" placeholder="you@example.com hoặc username" required />
    </div>

    <div class="field">
      <label for="password">Mật khẩu</label>
      <input id="password" type="password" v-model="password" placeholder="••••••" required />
    </div>

    <p v-if="error" class="error">{{ error }}</p>

    <button type="submit" :disabled="submitting">{{ submitting ? 'Đang xử lý...' : 'Đăng nhập' }}</button>
  </form>
</template>

<style scoped>
.login-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
.login-form .field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.login-form label {
  font-size: 0.9rem;
  color: #4b5563;
  font-weight: 500;
}
.login-form input {
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s;
  background: #f9fafb;
}
.login-form input:focus {
  outline: none;
  border-color: #3b82f6;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.login-form .error {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}
.login-form button {
  margin-top: 0.5rem;
  padding: 0.875rem 1.5rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}
.login-form button:hover:not(:disabled) {
  background: #2563eb;
  transform: translateY(-1px);
}
.login-form button:active:not(:disabled) {
  transform: translateY(0);
}
.login-form button[disabled] {
  opacity: 0.7;
  cursor: default;
  background: #9ca3af;
}
</style>
