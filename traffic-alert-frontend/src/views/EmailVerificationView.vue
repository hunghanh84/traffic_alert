<template>
  <div class="verify-email-container">
    <!-- Back Button - Top Left -->
    <button @click="router.push('/login')" class="back-btn-top">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
      <span>Quay lại</span>
    </button>

    <div class="verify-card">
      <div class="verify-header">
        <div class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <h1>Xác thực Email</h1>
        <p class="subtitle">Chúng tôi đã gửi mã xác thực 6 số đến email của bạn</p>
        <p class="email-display">{{ email }}</p>
      </div>

      <div class="verify-body">
        <!-- Code Input -->
        <div class="code-input-group">
          <input
            v-for="(digit, index) in 6"
            :key="index"
            :ref="el => codeInputs[index] = el"
            v-model="code[index]"
            type="text"
            maxlength="1"
            class="code-input"
            :class="{ 'error': hasError }"
            @input="handleInput(index, $event)"
            @keydown="handleKeydown(index, $event)"
            @paste="handlePaste"
          />
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <!-- Verify Button -->
        <button 
          @click="verifyCode" 
          :disabled="isVerifying || code.join('').length !== 6"
          class="verify-btn"
        >
          <span v-if="!isVerifying">Xác thực</span>
          <span v-else class="loading-spinner"></span>
        </button>

        <!-- Resend Code -->
        <div class="resend-section">
          <p v-if="canResend">
            Không nhận được mã? 
            <button @click="resendCode" :disabled="isResending" class="resend-btn">
              {{ isResending ? 'Đang gửi...' : 'Gửi lại' }}
            </button>
          </p>
          <p v-else class="countdown">
            Gửi lại sau {{ countdown }}s
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const email = ref(route.query.email || '')
const code = reactive(['', '', '', '', '', ''])
const codeInputs = ref([])
const isVerifying = ref(false)
const isResending = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const hasError = ref(false)
const canResend = ref(false)
const countdown = ref(60)
let countdownInterval = null

// Auto focus first input
onMounted(() => {
  if (codeInputs.value[0]) {
    codeInputs.value[0].focus()
  }
  startCountdown()
})

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
})

const startCountdown = () => {
  countdown.value = 60
  canResend.value = false
  
  countdownInterval = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(countdownInterval)
      canResend.value = true
    }
  }, 1000)
}

const handleInput = (index, event) => {
  const value = event.target.value
  
  // Only allow numbers
  if (!/^\d*$/.test(value)) {
    code[index] = ''
    return
  }

  // Move to next input
  if (value && index < 5) {
    codeInputs.value[index + 1]?.focus()
  }

  // Clear error when user types
  if (hasError.value) {
    hasError.value = false
    errorMessage.value = ''
  }
}

const handleKeydown = (index, event) => {
  // Move to previous input on backspace
  if (event.key === 'Backspace' && !code[index] && index > 0) {
    codeInputs.value[index - 1]?.focus()
  }
}

const handlePaste = (event) => {
  event.preventDefault()
  const pastedData = event.clipboardData.getData('text').trim()
  
  if (!/^\d{6}$/.test(pastedData)) {
    return
  }

  // Fill all inputs
  for (let i = 0; i < 6; i++) {
    code[i] = pastedData[i]
  }
  
  // Focus last input
  codeInputs.value[5]?.focus()
}

const verifyCode = async () => {
  const verificationCode = code.join('')
  
  if (verificationCode.length !== 6) {
    errorMessage.value = 'Vui lòng nhập đầy đủ 6 số'
    hasError.value = true
    return
  }

  isVerifying.value = true
  errorMessage.value = ''
  hasError.value = false

  try {
    const response = await fetch('http://127.0.0.1:8000/api/email/verify-code', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: email.value,
        code: verificationCode,
      }),
    })

    const result = await response.json()

    if (result.success) {
      successMessage.value = 'Xác thực thành công! Đang đăng nhập...'
      
      // Auto login after verification
      try {
        // Get user data from register response (stored in localStorage during registration)
        const registerData = JSON.parse(localStorage.getItem('pending_verification') || '{}')
        
        if (registerData.access_token) {
          // Save token and user info
          localStorage.setItem('access_token', registerData.access_token)
          localStorage.setItem('user_info', JSON.stringify(registerData.user))
          localStorage.removeItem('pending_verification')
          
          // Redirect to home/news page
          setTimeout(() => {
            router.push('/news')
          }, 1500)
        } else {
          // Fallback to login page if no token
          setTimeout(() => {
            router.push('/login')
          }, 1500)
        }
      } catch (err) {
        // Fallback to login page
        setTimeout(() => {
          router.push('/login')
        }, 1500)
      }
    } else {
      errorMessage.value = result.message
      hasError.value = true
      // Clear code inputs
      code.forEach((_, i) => code[i] = '')
      codeInputs.value[0]?.focus()
    }
  } catch (error) {
    errorMessage.value = 'Có lỗi xảy ra. Vui lòng thử lại.'
    hasError.value = true
  } finally {
    isVerifying.value = false
  }
}

const resendCode = async () => {
  isResending.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('http://127.0.0.1:8000/api/email/send-code', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: email.value,
      }),
    })

    const result = await response.json()

    if (result.success) {
      successMessage.value = 'Mã xác thực mới đã được gửi!'
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
      startCountdown()
    } else {
      errorMessage.value = result.message
    }
  } catch (error) {
    errorMessage.value = 'Không thể gửi lại mã. Vui lòng thử lại.'
  } finally {
    isResending.value = false
  }
}
</script>

<style scoped>
.verify-email-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #ffffff;
  padding: 2rem;
}

.verify-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  max-width: 500px;
  width: 100%;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

.verify-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 3rem 2rem;
  text-align: center;
}

.icon-wrapper {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

.icon-wrapper svg {
  width: 40px;
  height: 40px;
}

.verify-header h1 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
}

.subtitle {
  font-size: 1rem;
  opacity: 0.9;
  margin: 0 0 1rem 0;
}

.email-display {
  font-size: 1.1rem;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  display: inline-block;
}

.verify-body {
  padding: 3rem 2rem;
}

.code-input-group {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.code-input {
  width: 60px;
  height: 70px;
  font-size: 2rem;
  font-weight: 700;
  text-align: center;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  transition: all 0.3s ease;
  outline: none;
}

.code-input:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  transform: scale(1.05);
}

.code-input.error {
  border-color: #ef4444;
  animation: shake 0.5s;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-10px); }
  75% { transform: translateX(10px); }
}

.error-message {
  background: #fee2e2;
  color: #dc2626;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  text-align: center;
  font-weight: 500;
}

.success-message {
  background: #d1fae5;
  color: #059669;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  text-align: center;
  font-weight: 500;
}

.verify-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 1.5rem;
}

.verify-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.verify-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.resend-section {
  text-align: center;
  color: #64748b;
}

.resend-btn {
  background: none;
  border: none;
  color: #667eea;
  font-weight: 600;
  cursor: pointer;
  text-decoration: underline;
  padding: 0;
  margin-left: 0.5rem;
}

.resend-btn:hover:not(:disabled) {
  color: #764ba2;
}

.resend-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.countdown {
  color: #94a3b8;
  font-size: 0.9rem;
}

.back-btn-top {
  position: absolute;
  top: 2rem;
  left: 2rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  border: 1px solid #e5e7eb;
  color: #64748b;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  z-index: 10;
}

.back-btn-top:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: #475569;
  transform: translateX(-2px);
}

.back-btn-top svg {
  flex-shrink: 0;
}

@media (max-width: 640px) {
  .back-btn-top {
    top: 1rem;
    left: 1rem;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
  }

  .back-btn-top span {
    display: none;
  }

  .code-input {
    width: 45px;
    height: 55px;
    font-size: 1.5rem;
  }

  .code-input-group {
    gap: 0.5rem;
  }

  .verify-header {
    padding: 2rem 1rem;
  }

  .verify-body {
    padding: 2rem 1rem;
  }
}
</style>
