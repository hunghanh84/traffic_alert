<script setup>
import { ref, onMounted } from 'vue'

const alerts = ref([])
const isLoading = ref(true)
const pagination = ref({
  total: 0,
  per_page: 20,
  current_page: 1,
  last_page: 1
})
const filter = ref({
  trang_thai: '',
  loai_canh_bao: '',
  search: ''
})

const fetchAlerts = async (page = 1) => {
  try {
    isLoading.value = true
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    
    const params = new URLSearchParams()
    if (filter.value.trang_thai) params.append('trang_thai', filter.value.trang_thai)
    if (filter.value.loai_canh_bao) params.append('loai_canh_bao', filter.value.loai_canh_bao)
    if (filter.value.search) params.append('search', filter.value.search)
    params.append('page', page)
    
    const response = await fetch(`http://127.0.0.1:8000/api/admin/alerts?${params}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      alerts.value = result.data.items
      pagination.value = result.data.pagination
    }
  } catch (error) {
    console.error('Error fetching alerts:', error)
  } finally {
    isLoading.value = false
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchAlerts(page)
  }
}

const approveAlert = async (id) => {
  if (!confirm('Bạn có chắc muốn duyệt báo cáo này?')) return
  
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/admin/alerts/${id}/approve`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      alert('Đã duyệt báo cáo thành công!')
      fetchAlerts()
    }
  } catch (error) {
    console.error('Error approving alert:', error)
  }
}

const rejectAlert = async (id) => {
  if (!confirm('Bạn có chắc muốn từ chối báo cáo này?')) return
  
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/admin/alerts/${id}/reject`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      alert('Đã từ chối báo cáo!')
      fetchAlerts()
    }
  } catch (error) {
    console.error('Error rejecting alert:', error)
  }
}

const deleteAlert = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa báo cáo này?')) return
  
  try {
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/admin/alerts/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const result = await response.json()
    if (result.success) {
      alert('Đã xóa báo cáo!')
      fetchAlerts()
    }
  } catch (error) {
    console.error('Error deleting alert:', error)
  }
}

onMounted(() => {
  fetchAlerts()
})
</script>

<template>
  <div class="admin-alerts">
    <div class="page-header">
      <h1>📋 Quản lý Bài Đăng</h1>
      <p>Duyệt và quản lý các cảnh báo từ người dùng</p>
    </div>

    <div class="filters">
      <div class="search-bar">
        <input 
          v-model="filter.search" 
          @keyup.enter="fetchAlerts(1)"
          type="text" 
          placeholder="Tìm kiếm theo địa điểm, mô tả..."
          class="search-input"
        />
        <button @click="fetchAlerts(1)" class="search-btn">
          🔍 Tìm kiếm
        </button>
      </div>
      
      <select v-model="filter.trang_thai" @change="fetchAlerts(1)" class="filter-select">
        <option value="">Tất cả trạng thái</option>
        <option value="cho_duyet">Chờ duyệt</option>
        <option value="da_duyet">Đã duyệt</option>
        <option value="tu_choi">Từ chối</option>
      </select>

      <select v-model="filter.loai_canh_bao" @change="fetchAlerts(1)" class="filter-select">
        <option value="">Tất cả loại</option>
        <option value="traffic">Tắc đường</option>
        <option value="flood">Ngập lụt</option>
      </select>
    </div>

    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Đang tải...</p>
    </div>

    <div v-else class="alerts-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Người gửi</th>
            <th>Địa điểm</th>
            <th>Loại</th>
            <th>Mức độ</th>
            <th>Trạng thái</th>
            <th>Thời gian</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="alert in alerts" :key="alert.id">
            <td>{{ alert.id }}</td>
            <td>{{ alert.nguoi_dung?.ho_ten || alert.nguoi_dung?.ten || `User ${alert.nguoi_dung?.id}` || 'N/A' }}</td>
            <td>
              <div class="location">
                <strong>{{ alert.duong }}</strong>
                <small v-if="alert.phuong_xa">{{ alert.phuong_xa }}</small>
              </div>
            </td>
            <td>
              <span class="badge" :class="alert.loai_canh_bao">
                {{ alert.loai_canh_bao === 'traffic' ? '🚗 Tắc đường' : '🌊 Ngập lụt' }}
              </span>
            </td>
            <td>{{ alert.muc_do || 'N/A' }}</td>
            <td>
              <span class="status-badge" :class="alert.trang_thai">
                {{ alert.trang_thai }}
              </span>
            </td>
            <td>{{ alert.created_at }}</td>
            <td>
              <div class="actions">
                <button 
                  @click="$router.push(`/admin/alerts/${alert.id}`)"
                  class="btn-view"
                  title="Xem chi tiết"
                >
                  ✏️
                </button>
                <button 
                  v-if="alert.trang_thai === 'cho_duyet'"
                  @click="approveAlert(alert.id)"
                  class="btn-approve"
                  title="Duyệt"
                >
                  ✓
                </button>
                <button 
                  v-if="alert.trang_thai === 'cho_duyet'"
                  @click="rejectAlert(alert.id)"
                  class="btn-reject"
                  title="Từ chối"
                >
                  ✗
                </button>
                <button 
                  @click="deleteAlert(alert.id)"
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
          ({{ pagination.total }} bài đăng)
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
  </div>
</template>

<style scoped>
.admin-alerts {
  padding: 2rem;
  margin-left: 280px; /* Account for sidebar width */
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

.alerts-table {
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

td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.location strong {
  display: block;
  color: #1f2937;
}

.location small {
  color: #6b7280;
  font-size: 0.85rem;
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
}

.badge.traffic {
  background: #dbeafe;
  color: #1e40af;
}

.badge.flood {
  background: #ddd6fe;
  color: #5b21b6;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.cho_duyet {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.da_duyet {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.tu_choi {
  background: #fee2e2;
  color: #991b1b;
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

.btn-approve {
  background: #d1fae5;
  color: #065f46;
}

.btn-approve:hover {
  background: #a7f3d0;
}

.btn-reject {
  background: #fee2e2;
  color: #991b1b;
}

.btn-reject:hover {
  background: #fecaca;
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
</style>
