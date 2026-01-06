<script setup>
import { ref, computed } from 'vue'

const username = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const confirmPassword = ref('')
const submitting = ref(false)
const error = ref('')
const successMessage = ref('')

// State cho việc hiển thị mật khẩu
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const emit = defineEmits(['register', 'success'])

const emailValid = computed(() => /\S+@\S+\.\S+/.test(email.value))
const passwordValid = computed(() => password.value.length >= 6)
const usernameValid = computed(() => username.value.trim().length >= 3)
const passwordsMatch = computed(() => password.value === confirmPassword.value)

function togglePassword() {
  showPassword.value = !showPassword.value
}

function toggleConfirmPassword() {
  showConfirmPassword.value = !showConfirmPassword.value
}

function reset() {
  username.value = ''
  email.value = ''
  phone.value = ''
  password.value = ''
  confirmPassword.value = ''
  error.value = ''
  successMessage.value = ''
  showPassword.value = false
  showConfirmPassword.value = false
}

async function submitForm(e) {
  e.preventDefault()
  error.value = ''
  successMessage.value = ''

  if (!usernameValid.value) {
    error.value = 'Tên đăng nhập phải có ít nhất 3 ký tự.'
    return
  }
  if (!emailValid.value) {
    error.value = 'Vui lòng nhập email hợp lệ.'
    return
  }
  if (!passwordValid.value) {
    error.value = 'Mật khẩu phải có ít nhất 6 ký tự.'
    return
  }
  if (!passwordsMatch.value) {
    error.value = 'Mật khẩu xác nhận không khớp.'
    return
  }

  submitting.value = true
  try {
    const response = await fetch('http://localhost:8000/api/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        ten_dang_nhap: username.value,
        email: email.value,
        mat_khau: password.value,
        so_dien_thoai: phone.value
      })
    })

    const result = await response.json()

    if (!response.ok) {
      if (result.errors) {
        const firstErrorKey = Object.keys(result.errors)[0]
        throw new Error(result.errors[firstErrorKey][0])
      }
      throw new Error(result.message || 'Đăng ký thất bại')
    }

    // HIỂN THỊ THÔNG BÁO THÀNH CÔNG
    successMessage.value = 'Đăng ký tài khoản thành công! Đang chuyển đến trang xác thực...'
    
    // Lưu email TRƯỚC KHI reset
    const savedEmail = email.value
    
    // Lưu token và user info để auto-login sau khi verify
    localStorage.setItem('pending_verification', JSON.stringify({
      access_token: result.data.access_token,
      user: result.data.user
    }))
    
    // Reset form fields
    username.value = ''
    email.value = ''
    phone.value = ''
    password.value = ''
    confirmPassword.value = ''

    setTimeout(() => {
      emit('success', { email: savedEmail })
    }, 2000)

  } catch (err) {
    error.value = err.message || 'Đăng ký thất bại. Vui lòng thử lại.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="signup-container">
    <div v-if="successMessage" class="success-alert">
      <div class="success-icon">✅</div>
      <p>{{ successMessage }}</p>
    </div>

    <form v-else class="signup-form" @submit="submitForm">
      <div class="field">
        <label for="username">Tên đăng nhập</label>
        <input id="username" v-model="username" placeholder="Ví dụ: nguyenvana" required />
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input id="email" type="email" v-model="email" placeholder="you@example.com" required />
      </div>

      <div class="field">
        <label for="phone">Số điện thoại</label>
        <input id="phone" v-model="phone" placeholder="09xxxxxxxx" />
      </div>

      <div class="field">
        <label for="password">Mật khẩu</label>
        <div class="password-wrapper">
          <input 
            id="password" 
            :type="showPassword ? 'text' : 'password'" 
            v-model="password" 
            placeholder="••••••" 
            required 
          />
          <button type="button" class="eye-btn" @click="togglePassword" tabindex="-1" title="Hiện/Ẩn mật khẩu">
            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
          </button>
        </div>
      </div>

      <div class="field">
        <label for="confirm">Xác nhận mật khẩu</label>
        <div class="password-wrapper">
          <input 
            id="confirm" 
            :type="showConfirmPassword ? 'text' : 'password'" 
            v-model="confirmPassword" 
            placeholder="••••••" 
            required 
          />
          <button type="button" class="eye-btn" @click="toggleConfirmPassword" tabindex="-1" title="Hiện/Ẩn mật khẩu">
            <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
          </button>
        </div>
      </div>

      <p v-if="error" class="error">{{ error }}</p>

      <button type="submit" :disabled="submitting">
        {{ submitting ? 'Đang xử lý...' : 'Đăng ký' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.signup-container {
  width: 100%;
}

.success-alert {
  background-color: #ecfdf5;
  border: 1px solid #10b981;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  color: #065f46;
  animation: fadeIn 0.5s ease-out;
}

.success-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.signup-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
.signup-form .field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.signup-form label {
  font-size: 0.9rem;
  color: #4b5563;
  font-weight: 500;
}

.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-wrapper input {
  width: 100%;
  padding-right: 2.5rem;
}

.eye-btn {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  transition: color 0.2s;
}

.eye-btn:hover {
  color: #3b82f6;
}

.signup-form input {
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s;
  background: #f9fafb;
}
.signup-form input:focus {
  outline: none;
  border-color: #3b82f6;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.signup-form .error {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}
.signup-form button[type="submit"] {
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
.signup-form button[type="submit"]:hover:not(:disabled) {
  background: #2563eb;
  transform: translateY(-1px);
}
.signup-form button[type="submit"]:active:not(:disabled) {
  transform: translateY(0);
}
.signup-form button[disabled] {
  opacity: 0.7;
  cursor: default;
  background: #9ca3af;
}
</style>
