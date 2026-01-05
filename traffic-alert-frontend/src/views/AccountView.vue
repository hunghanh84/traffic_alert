<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref(JSON.parse(localStorage.getItem('user_info') || '{}'))
const isSubmitting = ref(false)
const message = ref({ type: '', text: '' })

// Trạng thái cho các Modal xác nhận
const showLogoutConfirm = ref(false)
const showDeactivateConfirm = ref(false)

// Location data
const cities = ref([])
const wards = ref([])
const zones = ref([])
const filteredWards = ref([])
const filteredZones = ref([])

const formData = ref({
  ten_dang_nhap: user.value.ten_dang_nhap || '',
  email: user.value.email || '',
  so_dien_thoai: user.value.so_dien_thoai || '',
  phuong_xa_id: user.value.phuong_xa_id || null,
  khu_vuc_id: user.value.khu_vuc_id || null,
  mat_khau_cu: '',
  mat_khau_moi: ''
})

// Extract thanh_pho_id from phuongXa relationship for display
const selectedThanhPhoId = ref(user.value.phuong_xa?.thanh_pho_id || null)

async function handleUpdateProfile() {
  isSubmitting.value = true
  message.value = { type: '', text: '' }

  try {
    const response = await fetch('http://localhost:8000/api/auth/profile', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('access_token')}`
      },
      body: JSON.stringify(formData.value)
    })

    const result = await response.json()

    if (!response.ok) {
      if (result.errors) {
        const firstError = Object.values(result.errors)[0][0]
        throw new Error(firstError)
      }
      throw new Error(result.message || 'Cập nhật thất bại')
    }

    message.value = { type: 'success', text: 'Cập nhật thông tin thành công!' }
    user.value = result.data
    localStorage.setItem('user_info', JSON.stringify(result.data))
    formData.value.mat_khau_cu = ''
    formData.value.mat_khau_moi = ''
    
    // Notify other components (like sidebar)
    window.dispatchEvent(new Event('storage'))
    
    // Tự động tắt thông báo sau 3 giây
    setTimeout(() => {
        message.value = { type: '', text: '' }
    }, 3000)
  } catch (err) {
    message.value = { type: 'error', text: err.message }
  } finally {
    isSubmitting.value = false
  }
}

async function confirmLogout() {
  try {
    await fetch('http://localhost:8000/api/auth/logout', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('access_token')}`
      }
    })
  } catch (err) {
    console.error('Logout error:', err)
  } finally {
    localStorage.removeItem('access_token')
    localStorage.removeItem('user_info')
    router.push('/login')
  }
}

async function confirmDeactivate() {
  try {
    const response = await fetch('http://localhost:8000/api/auth/deactivate', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('access_token')}`
      }
    })

    if (response.ok) {
      localStorage.removeItem('access_token')
      localStorage.removeItem('user_info')
      router.push('/login')
    } else {
      throw new Error('Có lỗi xảy ra khi thực hiện yêu cầu.')
    }
  } catch (err) {
    alert(err.message)
    showDeactivateConfirm.value = false
  }
}

// Fetch location data
async function fetchLocationData() {
  try {
    const response = await fetch('http://localhost:8000/api/locations/data')
    const result = await response.json()
    
    if (result.success) {
      cities.value = result.data.cities
      wards.value = result.data.wards
      zones.value = result.data.zones
      
      console.log('📍 Location data loaded:', {
        cities: cities.value.length,
        wards: wards.value.length,
        zones: zones.value.length
      })
      
      // Initialize filtered data based on current user data
      if (formData.value.thanh_pho_id) {
        filteredWards.value = wards.value.filter(w => w.thanh_pho_id === formData.value.thanh_pho_id)
      }
      if (formData.value.phuong_xa_id) {
        filteredZones.value = zones.value.filter(z => z.phuong_id === formData.value.phuong_xa_id)
      }
      
      // Set initial selectedThanhPhoId if user has phuong
      if (formData.value.phuong_xa_id) {
        const userWard = wards.value.find(w => w.id === formData.value.phuong_xa_id)
        if (userWard) {
          selectedThanhPhoId.value = userWard.thanh_pho_id
        }
      }
    }
  } catch (error) {
    console.error('Error loading location data:', error)
  }
}

// Watch for city changes to filter wards
function onCityChange() {
  formData.value.phuong_xa_id = null
  formData.value.khu_vuc_id = null
  filteredWards.value = wards.value.filter(w => w.thanh_pho_id === selectedThanhPhoId.value)
  filteredZones.value = []
}

// Watch for ward changes to filter zones
function onWardChange() {
  formData.value.khu_vuc_id = null
  filteredZones.value = zones.value.filter(z => z.phuong_id === formData.value.phuong_xa_id)
}

onMounted(() => {
  fetchLocationData()
})
</script>

<template>
  <div class="account-page">
    <div class="page-header">
      <div class="header-info">
        <h1>Hồ sơ cá nhân</h1>
        <p>Quản lý thông tin tài khoản và bảo mật hệ thống</p>
      </div>
    </div>

    <div class="content-grid">
      <!-- Main Info Section -->
      <section class="main-card">
        <div class="card-header">
          <div class="user-avatar-large">
            {{ user.ten_dang_nhap?.charAt(0).toUpperCase() }}
          </div>
          <div class="user-meta">
            <h2>{{ user.ten_dang_nhap }}</h2>
            <span class="role-badge" :class="user.vai_tro">{{ user.vai_tro }}</span>
            <p class="joined-date">Trạng thái: <span class="status-active">{{ user.trang_thai }}</span></p>
          </div>
        </div>

        <div class="card-body">
          <transition name="fade">
            <div v-if="message.text" :class="['alert-box', message.type]">
              <span class="alert-icon">{{ message.type === 'success' ? '✅' : '❌' }}</span>
              <p>{{ message.text }}</p>
            </div>
          </transition>

          <form @submit.prevent="handleUpdateProfile" class="profile-form">
            <div class="input-grid">
              <div class="form-group">
                <label>Tên đăng nhập</label>
                <div class="input-wrapper">
                  <span class="input-icon">👤</span>
                  <input v-model="formData.ten_dang_nhap" type="text" required />
                </div>
              </div>

              <div class="form-group">
                <label>Địa chỉ Email</label>
                <div class="input-wrapper">
                  <span class="input-icon">✉️</span>
                  <input v-model="formData.email" type="email" required />
                </div>
              </div>

              <div class="form-group">
                <label>Số điện thoại</label>
                <div class="input-wrapper">
                  <span class="input-icon">📞</span>
                  <input v-model="formData.so_dien_thoai" type="text" placeholder="09xxxxxxxx" />
                </div>
              </div>

              <div class="form-group">
                <label>Thành phố</label>
                <div class="input-wrapper">
                  <span class="input-icon">🏙️</span>
                  <select v-model="selectedThanhPhoId" @change="onCityChange" class="select-input">
                    <option :value="null">-- Chọn thành phố --</option>
                    <option v-for="city in cities" :key="city.id" :value="city.id">
                      {{ city.ten_thanh_pho }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label>Phường/Quận</label>
                <div class="input-wrapper">
                  <span class="input-icon">🏘️</span>
                  <select v-model="formData.phuong_xa_id" @change="onWardChange" class="select-input" :disabled="!selectedThanhPhoId">
                    <option :value="null">-- Chọn phường --</option>
                    <option v-for="ward in filteredWards" :key="ward.id" :value="ward.id">
                      {{ ward.ten_phuong }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label>Khu vực (tùy chọn)</label>
                <div class="input-wrapper">
                  <span class="input-icon">📍</span>
                  <select v-model="formData.khu_vuc_id" class="select-input" :disabled="!formData.phuong_xa_id">
                    <option :value="null">-- Chọn khu vực --</option>
                    <option v-for="zone in filteredZones" :key="zone.id" :value="zone.id">
                      {{ zone.ten_khu_vuc }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label>Mật khẩu cũ (nếu muốn đổi mật khẩu)</label>
                <div class="input-wrapper">
                  <span class="input-icon">🔐</span>
                  <input v-model="formData.mat_khau_cu" type="password" placeholder="Nhập mật khẩu hiện tại" />
                </div>
              </div>

              <div class="form-group">
                <label>Mật khẩu mới (không bắt buộc)</label>
                <div class="input-wrapper">
                  <span class="input-icon">🔒</span>
                  <input v-model="formData.mat_khau_moi" type="password" placeholder="Nhập mật khẩu mới" />
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary" :disabled="isSubmitting">
                {{ isSubmitting ? 'Đang xử lý...' : 'Cập nhật hồ sơ' }}
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Sidebar Actions -->
      <aside class="side-actions">
        <div class="simple-actions">
          <button @click="showLogoutConfirm = true" class="btn-outline logout-simple">
            <span class="icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logout-svg"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </span> Đăng xuất
          </button>
          
          <button @click="showDeactivateConfirm = true" class="btn-text-danger">
            <span class="icon">🗑️</span> Xóa tài khoản
          </button>
        </div>

      </aside>
    </div>

    <!-- Confirm Logout Modal -->
    <transition name="fade">
      <div v-if="showLogoutConfirm" class="modal-overlay" @click.self="showLogoutConfirm = false">
        <div class="custom-modal">
          <div class="modal-icon logout-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          </div>
          <h3>Xác nhận đăng xuất?</h3>
          <p>Bạn có chắc chắn muốn đăng xuất?</p>
          <div class="modal-buttons">
            <button @click="showLogoutConfirm = false" class="modal-btn btn-secondary">Hủy</button>
            <button @click="confirmLogout" class="modal-btn btn-primary-logout">Đồng ý</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Confirm Deactivate Modal -->
    <transition name="fade">
      <div v-if="showDeactivateConfirm" class="modal-overlay" @click.self="showDeactivateConfirm = false">
        <div class="custom-modal logout-modal">
          <div class="modal-icon delete-icon">⚠️</div>
          <h3>Xóa tài khoản?</h3>
          <p>Tài khoản của bạn sẽ bị <strong>tạm dừng</strong> và không thể đăng nhập. Hành động này không thể hoàn tác ngay lập tức.</p>
          <div class="modal-buttons">
            <button @click="showDeactivateConfirm = false" class="modal-btn btn-secondary">Hủy bỏ</button>
            <button @click="confirmDeactivate" class="modal-btn btn-danger-modal">Xác nhận xóa</button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.account-page {
  padding: 2.5rem;
  background: transparent;
  min-height: 100%;
  position: relative;
}

.page-header {
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.header-info h1 {
  font-size: 2.25rem;
  font-weight: 800;
  color: #1a202c;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
}

.header-info p {
  color: #718096;
  font-size: 1.1rem;
}

.content-grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 2rem;
}

@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

.main-card, .action-card, .info-card {
  background: white;
  border-radius: 24px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  border: 1px solid rgba(226, 232, 240, 0.8);
}

.card-header {
  padding: 3rem;
  background: linear-gradient(to right, #f8fafc, #ffffff);
  display: flex;
  align-items: center;
  gap: 2rem;
  border-bottom: 1px solid #edf2f7;
}

.user-avatar-large {
  width: 100px;
  height: 100px;
  background: var(--primary-gradient);
  border-radius: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: white;
  font-weight: 800;
  box-shadow: 0 15px 30px -10px rgba(102, 126, 234, 0.5);
  transform: rotate(-3deg);
}

.user-meta h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 0.5rem 0;
}

.role-badge {
  display: inline-block;
  padding: 0.25rem 0.875rem;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.role-badge.admin { background: #fff5f5; color: #c53030; }
.role-badge.nguoi_dung { background: #f0fff4; color: #2f855a; }
.role-badge.dieu_hanh { background: #fffaf0; color: #9c4221; }

.joined-date {
  margin-top: 0.75rem;
  font-size: 0.9rem;
  color: #718096;
}

.status-active {
  color: #38a169;
  font-weight: 700;
}

.card-body {
  padding: 3rem;
}

.alert-box {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  border-radius: 16px;
  margin-bottom: 2.5rem;
}

.alert-box.success { background: #f0fff4; border: 1px solid #c6f6d5; color: #276749; }
.alert-box.error { background: #fff5f5; border: 1px solid #fed7d7; color: #9b2c2c; }

.input-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

@media (max-width: 640px) {
  .input-grid { grid-template-columns: 1fr; }
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.form-group label {
  font-size: 0.95rem;
  font-weight: 600;
  color: #4a5568;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 1rem;
  font-size: 1.2rem;
  opacity: 0.7;
}

.input-wrapper input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid #edf2f7;
  border-radius: 16px;
  font-size: 1rem;
  transition: all 0.3s;
  background: #f8fafc;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

/* Select input styles */
.input-wrapper select.select-input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid #edf2f7;
  border-radius: 16px;
  font-size: 1rem;
  transition: all 0.3s;
  background: #f8fafc;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234a5568' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  padding-right: 3rem;
}

.input-wrapper select.select-input:hover:not(:disabled) {
  border-color: #cbd5e0;
}

.input-wrapper select.select-input:focus {
  outline: none;
  border-color: #667eea;
  background-color: white;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.input-wrapper select.select-input:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background-color: #e2e8f0;
}

.form-actions {
  margin-top: 3rem;
  display: flex;
  justify-content: flex-end;
}

.btn-primary {
  padding: 1rem 2.5rem;
  background: var(--primary-gradient);
  color: white;
  border: none;
  border-radius: 16px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 15px 25px -5px rgba(102, 126, 234, 0.5);
}

.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

/* Side Actions Styles */
.side-actions {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.simple-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.action-card, .info-card {
  padding: 2rem;
}

.btn-outline.logout-simple {
  width: 100%;
  padding: 1rem;
  background: white;
  border: 2px solid #edf2f7;
  border-radius: 16px;
  font-weight: 600;
  color: #4a5568;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  transition: all 0.2s;
}

.btn-outline.logout-simple:hover {
  background: #fff5f5;
  border-color: #feb2b2;
  color: #e53e3e;
}

.btn-text-danger {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border: none;
  color: white;
  border-radius: 16px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  transition: all 0.3s;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
}

.btn-text-danger:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
  filter: brightness(1.1);
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 1rem 0;
  border-bottom: 1px solid #f7fafc;
}

.info-item:last-child { border: none; }

.info-item .label { color: #718096; font-size: 0.95rem; }
.info-item .value { color: #2d3748; font-weight: 700; }

/* Logout SVG Style */
.logout-svg {
  display: inline-block;
  vertical-align: middle;
}

.btn-outline.logout:hover .logout-svg {
  stroke: #667eea;
}

/* Modal Overlay Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.custom-modal {
  background: white;
  padding: 3rem;
  border-radius: 32px;
  max-width: 450px;
  width: 90%;
  text-align: center;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: modalIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.modal-icon {
  font-size: 4.5rem;
  margin-bottom: 1.5rem;
  filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1));
}

.logout-icon {
  animation: bounce 2s infinite;
}

.delete-icon {
  animation: shake 1.5s infinite;
}

.custom-modal h3 {
  font-size: 1.75rem;
  font-weight: 800;
  color: #1a202c;
  margin-bottom: 1rem;
}

.custom-modal p {
  color: #4a5568;
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.modal-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.modal-btn {
  padding: 0.875rem 2rem;
  border: none;
  border-radius: 14px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 140px;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

.btn-primary-logout {
  background: var(--primary-gradient);
  color: white;
  box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.4);
}

.btn-primary-logout:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 25px -5px rgba(102, 126, 234, 0.5);
}

.btn-danger-modal {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.4);
}

.btn-danger-modal:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 25px -5px rgba(239, 68, 68, 0.5);
}

/* Animations */
@keyframes modalIn {
  from { opacity: 0; transform: scale(0.9) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@keyframes shake {
  0%, 100% { transform: rotate(0); }
  10%, 30%, 50%, 70%, 90% { transform: rotate(-5deg); }
  20%, 40%, 60%, 80% { transform: rotate(5deg); }
}

.fade-enter-active, .fade-leave-active {
  transition: all 0.4s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
