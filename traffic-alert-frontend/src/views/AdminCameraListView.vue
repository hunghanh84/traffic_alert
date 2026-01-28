<template>
  <div class="admin-cameras">
    <div class="page-header">
      <h1>📹 Quản lý Camera</h1>
      <button @click="showCreateModal = true" class="btn-primary">
        <span>➕</span> Thêm Camera
      </button>
    </div>

    <!-- Filters -->
    <div class="filters">
      <input v-model="searchQuery" type="text" placeholder="🔍 Tìm kiếm..." class="search-input" @input="fetchCameras" />
      <select v-model="filterStatus" @change="fetchCameras" class="filter-select">
        <option value="">Tất cả trạng thái</option>
        <option value="active">Hoạt động</option>
        <option value="inactive">Không hoạt động</option>
      </select>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table v-if="!loading && cameras.length > 0">
        <thead>
          <tr>
            <th>Mã Camera</th>
            <th>Tên Camera</th>
            <th>Đường</th>
            <th>Stream URL</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="camera in cameras" :key="camera.id">
            <td><span class="code">{{ camera.ma_camera }}</span></td>
            <td>{{ camera.ten_camera }}</td>
            <td>{{ camera.ten_duong || '-' }}</td>
            <td>
              <a :href="camera.stream_url" target="_blank" class="stream-link">
                🔗 Xem stream
              </a>
            </td>
            <td>
              <span :class="`status ${camera.trang_thai_ket_noi}`">
                {{ camera.trang_thai_ket_noi === 'active' ? '✓ Hoạt động' : '✗ Tắt' }}
              </span>
            </td>
            <td class="actions">
              <button @click="toggleStatus(camera.id)" class="btn-toggle" :title="camera.trang_thai_ket_noi === 'active' ? 'Tắt' : 'Bật'">
                {{ camera.trang_thai_ket_noi === 'active' ? '⏸️' : '▶️' }}
              </button>
              <button @click="editCamera(camera)" class="btn-edit" title="Sửa">✏️</button>
              <button @click="deleteCamera(camera.id)" class="btn-delete" title="Xóa">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Đang tải...</p>
      </div>

      <div v-else class="empty-state">
        <p>📭 Không có camera nào</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="pagination">
      <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="btn-page">← Trước</button>
      <span class="page-info">Trang {{ pagination.current_page }} / {{ pagination.last_page }}</span>
      <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="btn-page">Sau →</button>
    </div>

    <!-- Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ showEditModal ? 'Sửa Camera' : 'Thêm Camera Mới' }}</h2>
          <button @click="closeModal" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Mã Camera *</label>
            <input v-model="form.ma_camera" type="text" placeholder="CAM001" required />
          </div>
          <div class="form-group">
            <label>Tên Camera *</label>
            <input v-model="form.ten_camera" type="text" placeholder="Camera giao lộ..." required />
          </div>
          <div class="form-group">
            <label>Đường *</label>
            <input v-model="form.duong_id" type="number" placeholder="ID đường" required />
          </div>
          <div class="form-group">
            <label>Stream URL *</label>
            <input v-model="form.stream_url" type="url" placeholder="https://youtube.com/..." required />
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

const cameras = ref([])
const loading = ref(false)
const submitting = ref(false)
const searchQuery = ref('')
const filterStatus = ref('')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const error = ref('')
const editingId = ref(null)

const form = ref({
  ma_camera: '',
  ten_camera: '',
  duong_id: '',
  stream_url: ''
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
})

onMounted(() => {
  fetchCameras()
})

function getAuthHeaders() {
  const token = localStorage.getItem('access_token') || localStorage.getItem('token')
  return {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
}

async function fetchCameras() {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    })

    if (searchQuery.value) params.append('search', searchQuery.value)
    if (filterStatus.value) params.append('trang_thai', filterStatus.value)

    const response = await fetch(`http://127.0.0.1:8000/api/admin/cameras?${params}`, {
      headers: getAuthHeaders()
    })
    const data = await response.json()

    if (data.success) {
      cameras.value = data.data.data
      pagination.value = {
        current_page: data.data.current_page,
        last_page: data.data.last_page,
        per_page: data.data.per_page,
        total: data.data.total
      }
    }
  } catch (err) {
    console.error('Error:', err)
  } finally {
    loading.value = false
  }
}

async function submitForm() {
  if (!form.value.ma_camera || !form.value.ten_camera || !form.value.duong_id || !form.value.stream_url) {
    error.value = 'Vui lòng điền đầy đủ thông tin'
    return
  }

  submitting.value = true
  error.value = ''

  try {
    const url = showEditModal.value
      ? `http://127.0.0.1:8000/api/admin/cameras/${editingId.value}`
      : 'http://127.0.0.1:8000/api/admin/cameras'

    const response = await fetch(url, {
      method: showEditModal.value ? 'PUT' : 'POST',
      headers: getAuthHeaders(),
      body: JSON.stringify(form.value)
    })

    const data = await response.json()

    if (data.success) {
      closeModal()
      fetchCameras()
    } else {
      error.value = data.message || 'Có lỗi xảy ra'
    }
  } catch (err) {
    error.value = 'Không thể kết nối đến server'
  } finally {
    submitting.value = false
  }
}

async function toggleStatus(id) {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/admin/cameras/${id}/toggle-status`, {
      method: 'POST',
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) {
      fetchCameras()
    }
  } catch (err) {
    alert('Không thể thay đổi trạng thái')
  }
}

function editCamera(camera) {
  editingId.value = camera.id
  form.value = {
    ma_camera: camera.ma_camera,
    ten_camera: camera.ten_camera,
    duong_id: camera.duong_id,
    stream_url: camera.stream_url
  }
  showEditModal.value = true
}

async function deleteCamera(id) {
  if (!confirm('Bạn có chắc muốn xóa camera này?')) return

  try {
    const response = await fetch(`http://127.0.0.1:8000/api/admin/cameras/${id}`, {
      method: 'DELETE',
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) {
      fetchCameras()
    }
  } catch (err) {
    alert('Không thể xóa camera')
  }
}

function closeModal() {
  showCreateModal.value = false
  showEditModal.value = false
  editingId.value = null
  form.value = {
    ma_camera: '',
    ten_camera: '',
    duong_id: '',
    stream_url: ''
  }
  error.value = ''
}

function changePage(page) {
  pagination.value.current_page = page
  fetchCameras()
}
</script>

<style scoped>
.admin-cameras {
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
}

td {
  padding: 1rem;
  border-top: 1px solid #e2e8f0;
}

.code {
  font-family: monospace;
  background: #f1f5f9;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-weight: 600;
}

.stream-link {
  color: #3b82f6;
  text-decoration: none;
}

.stream-link:hover {
  text-decoration: underline;
}

.status {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status.active {
  background: #d1fae5;
  color: #059669;
}

.status.inactive {
  background: #fee2e2;
  color: #dc2626;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-toggle,
.btn-edit,
.btn-delete {
  padding: 0.5rem;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 1.25rem;
  transition: transform 0.2s;
}

.btn-toggle:hover,
.btn-edit:hover,
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
  to { transform: rotate(360deg); }
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

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 1rem;
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
