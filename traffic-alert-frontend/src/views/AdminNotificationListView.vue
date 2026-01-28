<template>
  <div class="admin-notification-list">
    <div class="page-header">
      <h1>📢 Quản lý Thông báo</h1>
      <button @click="showCreateModal = true" class="btn-primary">
        <span class="icon">➕</span>
        Tạo thông báo mới
      </button>
    </div>

    <!-- Filters -->
    <div class="filters">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="🔍 Tìm kiếm thông báo..."
        class="search-input"
        @input="fetchNotifications"
      />

      <select v-model="filterStatus" @change="fetchNotifications" class="filter-select">
        <option value="">Tất cả trạng thái</option>
        <option value="pending">Chờ gửi</option>
        <option value="sent">Đã gửi</option>
        <option value="failed">Thất bại</option>
      </select>

      <select v-model="filterType" @change="fetchNotifications" class="filter-select">
        <option value="">Tất cả loại</option>
        <option value="email">Email</option>
        <option value="sms">SMS</option>
        <option value="push">Push</option>
        <option value="telegram">Telegram</option>
      </select>
    </div>

    <!-- Notifications Table -->
    <div class="table-container">
      <table v-if="!loading && notifications.length > 0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Sự kiện</th>
            <th>Loại</th>
            <th>Ưu tiên</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="notification in notifications" :key="notification.id">
            <td>{{ notification.id }}</td>
            <td class="title-cell">{{ notification.tieu_de }}</td>
            <td>
              <span v-if="notification.su_kien">
                {{ notification.su_kien.loai_su_kien?.ten || 'N/A' }}
              </span>
              <span v-else class="text-muted">-</span>
            </td>
            <td>
              <span :class="`badge badge-${notification.loai_thong_bao}`">
                {{ getTypeLabel(notification.loai_thong_bao) }}
              </span>
            </td>
            <td>
              <span :class="`priority priority-${notification.muc_do_uu_tien}`">
                {{ getPriorityLabel(notification.muc_do_uu_tien) }}
              </span>
            </td>
            <td>
              <span :class="`status status-${notification.trang_thai_gui}`">
                {{ getStatusLabel(notification.trang_thai_gui) }}
              </span>
            </td>
            <td>{{ formatDate(notification.created_at) }}</td>
            <td class="actions">
              <button
                v-if="notification.trang_thai_gui === 'pending'"
                @click="sendNotification(notification.id)"
                class="btn-send"
                title="Gửi thông báo"
              >
                📤
              </button>
              <button
                @click="viewNotification(notification.id)"
                class="btn-view"
                title="Xem chi tiết"
              >
                👁️
              </button>
              <button
                @click="editNotification(notification)"
                class="btn-edit"
                title="Chỉnh sửa"
                v-if="notification.trang_thai_gui === 'pending'"
              >
                ✏️
              </button>
              <button
                @click="deleteNotification(notification.id)"
                class="btn-delete"
                title="Xóa"
                v-if="notification.trang_thai_gui === 'pending'"
              >
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Đang tải dữ liệu...</p>
      </div>

      <div v-else class="empty-state">
        <p>📭 Không có thông báo nào</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="pagination">
      <button
        @click="changePage(pagination.current_page - 1)"
        :disabled="pagination.current_page === 1"
        class="btn-page"
      >
        ← Trước
      </button>
      <span class="page-info">
        Trang {{ pagination.current_page }} / {{ pagination.last_page }}
      </span>
      <button
        @click="changePage(pagination.current_page + 1)"
        :disabled="pagination.current_page === pagination.last_page"
        class="btn-page"
      >
        Sau →
      </button>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ showEditModal ? 'Chỉnh sửa thông báo' : 'Tạo thông báo mới' }}</h2>
          <button @click="closeModal" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Sự kiện *</label>
            <select v-model="form.su_kien_id" required :disabled="showEditModal">
              <option value="">-- Chọn sự kiện --</option>
              <option v-for="event in events" :key="event.id" :value="event.id">
                {{ event.loai_su_kien?.ten }} - {{ event.duong?.ten }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Tiêu đề *</label>
            <input v-model="form.tieu_de" type="text" placeholder="Nhập tiêu đề" required />
          </div>

          <div class="form-group">
            <label>Nội dung *</label>
            <textarea
              v-model="form.noi_dung"
              placeholder="Nhập nội dung thông báo"
              rows="4"
              required
            ></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Loại thông báo *</label>
              <select v-model="form.loai_thong_bao" required>
                <option value="telegram">Telegram</option>
                <option value="email">Email</option>
                <option value="sms">SMS</option>
                <option value="push">Push Notification</option>
              </select>
            </div>

            <div class="form-group">
              <label>Mức độ ưu tiên *</label>
              <select v-model="form.muc_do_uu_tien" required>
                <option value="low">Thấp</option>
                <option value="medium">Trung bình</option>
                <option value="high">Cao</option>
                <option value="urgent">Khẩn cấp</option>
              </select>
            </div>
          </div>

          <div v-if="error" class="error-message">{{ error }}</div>
        </div>
        <div class="modal-footer">
          <button @click="closeModal" class="btn-secondary">Hủy</button>
          <button @click="submitForm" class="btn-primary" :disabled="submitting">
            {{ submitting ? 'Đang xử lý...' : showEditModal ? 'Cập nhật' : 'Tạo mới' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const notifications = ref([])
const events = ref([])
const loading = ref(false)
const submitting = ref(false)
const searchQuery = ref('')
const filterStatus = ref('')
const filterType = ref('')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const error = ref('')
const editingId = ref(null)

const form = ref({
  su_kien_id: '',
  tieu_de: '',
  noi_dung: '',
  loai_thong_bao: 'telegram',
  muc_do_uu_tien: 'medium',
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
})

onMounted(() => {
  fetchNotifications()
  fetchEvents()
})

async function fetchNotifications() {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: pagination.value.current_page,
      per_page: pagination.value.per_page,
    })

    if (searchQuery.value) params.append('search', searchQuery.value)
    if (filterStatus.value) params.append('trang_thai_gui', filterStatus.value)
    if (filterType.value) params.append('loai_thong_bao', filterType.value)

    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/admin/notifications?${params}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    const data = await response.json()

    if (data.success) {
      notifications.value = data.data.data
      pagination.value = {
        current_page: data.data.current_page,
        last_page: data.data.last_page,
        per_page: data.data.per_page,
        total: data.data.total,
      }
    }
  } catch (err) {
    console.error('Error fetching notifications:', err)
  } finally {
    loading.value = false
  }
}

async function fetchEvents() {
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch('http://127.0.0.1:8000/api/admin/notifications/events', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    const data = await response.json()
    if (data.success) {
      events.value = data.data
    }
  } catch (err) {
    console.error('Error fetching events:', err)
  }
}

// Helper function for auth headers
function getAuthHeaders() {
  const token = localStorage.getItem('access_token') || localStorage.getItem('token')
  return {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
}

async function submitForm() {
  if (!form.value.su_kien_id || !form.value.tieu_de || !form.value.noi_dung) {
    error.value = 'Vui lòng điền đầy đủ thông tin bắt buộc'
    return
  }

  submitting.value = true
  error.value = ''

  try {
    const url = showEditModal.value
      ? `http://127.0.0.1:8000/api/admin/notifications/${editingId.value}`
      : 'http://127.0.0.1:8000/api/admin/notifications'

    const response = await fetch(url, {
      method: showEditModal.value ? 'PUT' : 'POST',
      headers: getAuthHeaders(),
      body: JSON.stringify(form.value),
    })

    const data = await response.json()

    if (data.success) {
      closeModal()
      fetchNotifications()
    } else {
      error.value = data.message || 'Có lỗi xảy ra'
    }
  } catch (err) {
    error.value = 'Không thể kết nối đến server'
  } finally {
    submitting.value = false
  }
}

async function sendNotification(id) {
  if (!confirm('Bạn có chắc muốn gửi thông báo này?')) return

  try {
    const response = await fetch(`http://127.0.0.1:8000/api/admin/notifications/${id}/send`, {
      method: 'POST',
      headers: getAuthHeaders()
    })
    const data = await response.json()

    if (data.success) {
      alert('Gửi thông báo thành công!')
      fetchNotifications()
    } else {
      alert(data.message || 'Gửi thông báo thất bại')
    }
  } catch (err) {
    alert('Không thể gửi thông báo')
  }
}

function viewNotification(id) {
  router.push(`/admin/notifications/${id}`)
}

function editNotification(notification) {
  editingId.value = notification.id
  form.value = {
    su_kien_id: notification.su_kien_id,
    tieu_de: notification.tieu_de,
    noi_dung: notification.noi_dung,
    loai_thong_bao: notification.loai_thong_bao,
    muc_do_uu_tien: notification.muc_do_uu_tien,
  }
  showEditModal.value = true
}

async function deleteNotification(id) {
  if (!confirm('Bạn có chắc muốn xóa thông báo này?')) return

  try {
    const response = await fetch(`http://127.0.0.1:8000/api/admin/notifications/${id}`, {
      method: 'DELETE',
      headers: getAuthHeaders()
    })
    const data = await response.json()

    if (data.success) {
      fetchNotifications()
    } else {
      alert(data.message || 'Xóa thất bại')
    }
  } catch (err) {
    alert('Không thể xóa thông báo')
  }
}

function closeModal() {
  showCreateModal.value = false
  showEditModal.value = false
  editingId.value = null
  form.value = {
    su_kien_id: '',
    tieu_de: '',
    noi_dung: '',
    loai_thong_bao: 'telegram',
    muc_do_uu_tien: 'medium',
  }
  error.value = ''
}

function changePage(page) {
  pagination.value.current_page = page
  fetchNotifications()
}

function getTypeLabel(type) {
  const labels = {
    email: 'Email',
    sms: 'SMS',
    push: 'Push',
    telegram: 'Telegram',
  }
  return labels[type] || type
}

function getPriorityLabel(priority) {
  const labels = {
    low: 'Thấp',
    medium: 'Trung bình',
    high: 'Cao',
    urgent: 'Khẩn cấp',
  }
  return labels[priority] || priority
}

function getStatusLabel(status) {
  const labels = {
    pending: 'Chờ gửi',
    sent: 'Đã gửi',
    failed: 'Thất bại',
  }
  return labels[status] || status
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleString('vi-VN')
}
</script>

<style scoped>
.admin-notification-list {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  color: #1a202c;
  margin: 0;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.search-input,
.filter-select {
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.95rem;
}

.search-input {
  flex: 1;
  max-width: 400px;
}

.filter-select {
  min-width: 180px;
}

.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f7fafc;
}

th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #4a5568;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

td {
  padding: 1rem;
  border-top: 1px solid #e2e8f0;
}

.title-cell {
  font-weight: 500;
  max-width: 300px;
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-email {
  background: #e6f7ff;
  color: #0050b3;
}
.badge-sms {
  background: #fff7e6;
  color: #d46b08;
}
.badge-push {
  background: #f6ffed;
  color: #389e0d;
}
.badge-telegram {
  background: #e6f7ff;
  color: #1890ff;
}

.priority {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.priority-low {
  background: #f0f0f0;
  color: #666;
}
.priority-medium {
  background: #fff7e6;
  color: #d46b08;
}
.priority-high {
  background: #fff1f0;
  color: #cf1322;
}
.priority-urgent {
  background: #ff4d4f;
  color: white;
}

.status {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-pending {
  background: #fff7e6;
  color: #d46b08;
}
.status-sent {
  background: #f6ffed;
  color: #389e0d;
}
.status-failed {
  background: #fff1f0;
  color: #cf1322;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-send,
.btn-view,
.btn-edit,
.btn-delete {
  padding: 0.5rem;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 1.25rem;
  transition: transform 0.2s;
}

.btn-send:hover {
  transform: scale(1.2);
}
.btn-view:hover {
  transform: scale(1.2);
}
.btn-edit:hover {
  transform: scale(1.2);
}
.btn-delete:hover {
  transform: scale(1.2);
}

.btn-primary {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: transform 0.2s;
}

.btn-primary:hover {
  transform: translateY(-2px);
}

.btn-secondary {
  padding: 0.75rem 1.5rem;
  background: #e2e8f0;
  color: #4a5568;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.loading,
.empty-state {
  padding: 4rem 2rem;
  text-align: center;
  color: #718096;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-page {
  padding: 0.5rem 1rem;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 6px;
  cursor: pointer;
}

.btn-page:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  margin-left: 0;
}

.modal {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #718096;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #4a5568;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 1rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.error-message {
  padding: 1rem;
  background: #fff1f0;
  color: #cf1322;
  border-radius: 6px;
  margin-top: 1rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid #e2e8f0;
}
</style>
