<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const users = ref([])
const isLoading = ref(true)
const pagination = ref({
  total: 0,
  per_page: 20,
  current_page: 1,
  last_page: 1
})
const filter = ref({
  vai_tro: '',
  trang_thai: '',
  search: ''
})

const showModal = ref(false)
const modalConfig = ref({
  title: '',
  message: '',
  type: 'confirm', // 'confirm' or 'alert'
  onConfirm: null
})

const openConfirmModal = (title, message, onConfirm) => {
  modalConfig.value = { title, message, type: 'confirm', onConfirm }
  showModal.value = true
}

const openAlertModal = (title, message) => {
  modalConfig.value = { title, message, type: 'alert', onConfirm: null }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const confirmModal = () => {
  if (modalConfig.value.onConfirm) {
    modalConfig.value.onConfirm()
  }
  closeModal()
}

const fetchUsers = async (page = 1) => {
  try {
    isLoading.value = true
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    
    const params = new URLSearchParams()
    if (filter.value.vai_tro) params.append('vai_tro', filter.value.vai_tro)
    if (filter.value.trang_thai) params.append('trang_thai', filter.value.trang_thai)
    if (filter.value.search) params.append('search', filter.value.search)
    params.append('page', page)
    
    const response = await fetch(`http://127.0.0.1:8000/api/admin/users?${params}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      users.value = result.data.items
      pagination.value = result.data.pagination
    }
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    isLoading.value = false
  }
}

const toggleStatus = async (id) => {
  openConfirmModal(
    '🔄 Thay đổi trạng thái',
    'Bạn có chắc muốn thay đổi trạng thái người dùng này?',
    async () => {
      try {
        const token = localStorage.getItem('access_token') || localStorage.getItem('token')
        const response = await fetch(`http://127.0.0.1:8000/api/admin/users/${id}/toggle-status`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })

        const result = await response.json()
        if (result.success) {
          openAlertModal('✅ Thành công', 'Cập nhật trạng thái thành công!')
          fetchUsers(pagination.value.current_page)
        }
      } catch (error) {
        console.error('Error toggling status:', error)
        openAlertModal('❌ Lỗi', 'Có lỗi xảy ra khi cập nhật trạng thái')
      }
    }
  )
}

const deleteUser = async (id) => {
  openConfirmModal(
    '🗑️ Xóa người dùng',
    'Bạn có chắc muốn xóa người dùng này? Hành động này không thể hoàn tác!',
    async () => {
      try {
        const token = localStorage.getItem('access_token') || localStorage.getItem('token')
        const response = await fetch(`http://127.0.0.1:8000/api/admin/users/${id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })

        const result = await response.json()
        if (result.success) {
          openAlertModal('✅ Thành công', 'Xóa người dùng thành công!')
          fetchUsers(pagination.value.current_page)
        } else {
          openAlertModal('❌ Lỗi', result.message || 'Có lỗi xảy ra')
        }
      } catch (error) {
        console.error('Error deleting user:', error)
        openAlertModal('❌ Lỗi', 'Có lỗi xảy ra khi xóa người dùng')
      }
    }
  )
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchUsers(page)
  }
}

onMounted(() => {
  fetchUsers()
})
</script>

<template>
  <div class="admin-users">
    <div class="page-header">
      <h1>👥 Quản lý Người dùng</h1>
      <p>Quản lý tài khoản người dùng trong hệ thống</p>
    </div>

    <div class="filters">
      <div class="search-bar">
        <input 
          v-model="filter.search" 
          @keyup.enter="fetchUsers(1)"
          type="text" 
          placeholder="Tìm kiếm theo tên, email, số điện thoại..."
          class="search-input"
        />
        <button @click="fetchUsers(1)" class="search-btn">
          🔍 Tìm kiếm
        </button>
      </div>
      
      <select v-model="filter.vai_tro" @change="fetchUsers(1)" class="filter-select">
        <option value="">Tất cả vai trò</option>
        <option value="admin">Admin</option>
        <option value="nguoi_dung">Người dùng</option>
        <option value="dieu_hanh">Điều hành</option>
      </select>

      <select v-model="filter.trang_thai" @change="fetchUsers(1)" class="filter-select">
        <option value="">Tất cả trạng thái</option>
        <option value="hoat_dong">Hoạt động</option>
        <option value="khoa">Khóa</option>
        <option value="tam_dung">Tạm dừng</option>
      </select>
    </div>

    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Đang tải...</p>
    </div>

    <div v-else class="users-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên đăng nhập</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>
              <strong>{{ user.ten_dang_nhap }}</strong>
            </td>
            <td>{{ user.email }}</td>
            <td>{{ user.so_dien_thoai || 'N/A' }}</td>
            <td>
              <span class="role-badge" :class="user.vai_tro">
                {{ user.vai_tro === 'admin' ? '👑 Admin' : user.vai_tro === 'dieu_hanh' ? '⚙️ Điều hành' : '👤 Người dùng' }}
              </span>
            </td>
            <td>
              <span class="status-badge" :class="user.trang_thai">
                {{ user.trang_thai }}
              </span>
            </td>
            <td>{{ user.created_at }}</td>
            <td>
              <div class="actions">
                <button 
                  @click="router.push(`/admin/users/${user.id}`)"
                  class="btn-view"
                  title="Xem chi tiết"
                >
                  ✏️
                </button>
                <button 
                  @click="toggleStatus(user.id)"
                  class="btn-toggle"
                  :title="user.trang_thai === 'hoat_dong' ? 'Khóa' : 'Kích hoạt'"
                >
                  {{ user.trang_thai === 'hoat_dong' ? '🔒' : '🔓' }}
                </button>
                <button 
                  @click="deleteUser(user.id)"
                  class="btn-delete"
                  title="Xóa"
                >
                  🗑
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="pagination">
        <button 
          @click="goToPage(1)" 
          :disabled="pagination.current_page === 1"
          class="page-btn"
        >
          ««
        </button>
        <button 
          @click="goToPage(pagination.current_page - 1)" 
          :disabled="pagination.current_page === 1"
          class="page-btn"
        >
          «
        </button>
        
        <span class="page-info">
          Trang {{ pagination.current_page }} / {{ pagination.last_page }}
          ({{ pagination.total }} người dùng)
        </span>
        
        <button 
          @click="goToPage(pagination.current_page + 1)" 
          :disabled="pagination.current_page === pagination.last_page"
          class="page-btn"
        >
          »
        </button>
        <button 
          @click="goToPage(pagination.last_page)" 
          :disabled="pagination.current_page === pagination.last_page"
          class="page-btn"
        >
          »»
        </button>
      </div>
    </div>

    <!-- Custom Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ modalConfig.title }}</h3>
        </div>
        <div class="modal-body">
          <p>{{ modalConfig.message }}</p>
        </div>
        <div class="modal-footer">
          <button v-if="modalConfig.type === 'confirm'" @click="closeModal" class="btn-cancel">
            Hủy
          </button>
          <button @click="modalConfig.type === 'confirm' ? confirmModal() : closeModal()" class="btn-confirm">
            {{ modalConfig.type === 'confirm' ? 'Xác nhận' : 'Đóng' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-users {
  padding: 2rem;
  margin-left: 0;
  min-height: 100vh;
}

.page-header h1 {
  font-size: 2rem;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.page-header p {
  color: #6b7280;
}

.filters {
  display: flex;
  gap: 1rem;
  margin: 2rem 0;
  align-items: center;
}

.search-bar {
  flex: 1;
  display: flex;
  gap: 0.75rem;
  min-width: 400px;
}

.search-input {
  flex: 1;
  padding: 0.875rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s;
  background: white;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.search-btn {
  padding: 0.875rem 1.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.search-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.filter-select {
  padding: 0.875rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.95rem;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 180px;
}

.filter-select:hover {
  border-color: #d1d5db;
}

.filter-select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.loading {
  text-align: center;
  padding: 3rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f3f4f6;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.users-table {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f9fafb;
}

th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 2px solid #e5e7eb;
}

th:nth-child(5) {
  width: 180px;
}

td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.role-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
}

.role-badge.admin {
  background: #fef3c7;
  color: #92400e;
}

.role-badge.dieu_hanh {
  background: #dbeafe;
  color: #1e40af;
}

.role-badge.nguoi_dung {
  background: #f3f4f6;
  color: #374151;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.hoat_dong {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.khoa {
  background: #fee2e2;
  color: #991b1b;
}

.status-badge.tam_dung {
  background: #fef3c7;
  color: #92400e;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.actions button {
  padding: 0.5rem 0.75rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s;
}

.btn-view {
  background: #dbeafe;
  color: #1e40af;
}

.btn-view:hover {
  background: #bfdbfe;
}

.btn-toggle {
  background: #fef3c7;
  color: #92400e;
}

.btn-toggle:hover {
  background: #fde68a;
}

.btn-delete {
  background: #f3f4f6;
  color: #6b7280;
}

.btn-delete:hover {
  background: #e5e7eb;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.page-btn {
  padding: 0.5rem 1rem;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  color: #374151;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  padding: 0.5rem 1rem;
  color: #6b7280;
  font-weight: 500;
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
  border-radius: 16px;
  width: 90%;
  max-width: 420px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
  animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
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

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #1f2937;
  font-weight: 700;
}

.modal-body {
  padding: 1.5rem;
}

.modal-body p {
  margin: 0;
  color: #6b7280;
  line-height: 1.6;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.modal-footer .btn-cancel,
.modal-footer .btn-confirm {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.modal-footer .btn-cancel {
  background: #f3f4f6;
  color: #374151;
}

.modal-footer .btn-cancel:hover {
  background: #e5e7eb;
}

.modal-footer .btn-confirm {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.modal-footer .btn-confirm:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}
</style>
