<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const alerts = ref([])
const isLoading = ref(true)
const pagination = ref({
  total: 0,
  per_page: 10,
  current_page: 1,
  last_page: 1,
})

// Popup states
const showDeleteConfirm = ref(false)
const deleteAlertId = ref(null)
const showMessage = ref(false)
const messageContent = ref('')
const messageType = ref('success') // 'success' or 'error'

const severityColors = {
  low: '#10b981',
  medium: '#f59e0b',
  high: '#ef4444',
  critical: '#dc2626',
}

const severityLabels = {
  low: 'Thấp',
  medium: 'Trung bình',
  high: 'Cao',
  critical: 'Nghiêm trọng',
}

const statusColors = {
  cho_duyet: '#f59e0b',
  da_duyet: '#10b981',
  tu_choi: '#ef4444',
}

const statusLabels = {
  cho_duyet: 'Chờ duyệt',
  da_duyet: 'Đã duyệt',
  tu_choi: 'Từ chối',
}

const typeLabels = {
  traffic: 'Tắc đường',
  flood: 'Ngập đường',
}

const typeIcons = {
  traffic: '🚗',
  flood: '🌊',
}

const isMineOnly = ref(true)
const isLoggedIn = computed(() => !!localStorage.getItem('access_token'))

const toggleMineOnly = () => {
  isMineOnly.value = !isMineOnly.value
  fetchAlerts(1)
}

const fetchAlerts = async (page = 1) => {
  try {
    isLoading.value = true
    console.log('Đang tải danh sách cảnh báo, trang:', page, 'Chỉ của tôi:', isMineOnly.value)
    
    // Thêm token nếu đã đăng nhập
    const token = localStorage.getItem('access_token') || localStorage.getItem('token')
    const headers = {
      'Accept': 'application/json'
    }
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    const url = new URL('http://127.0.0.1:8000/api/alerts')
    url.searchParams.append('page', page)
    url.searchParams.append('per_page', 10)
    // No need for 'mine' parameter - API always returns user's own alerts

    const response = await fetch(url.toString(), {
      headers
    })
    
    if (!response.ok) {
        throw new Error(`Server returned ${response.status}: ${response.statusText}`)
    }
    
    const result = await response.json()
    console.log('Kết quả từ API:', result)

    if (result.success) {
      alerts.value = result.data
      pagination.value = result.pagination
    } else {
        console.error('API thông báo thất bại:', result.message)
    }
  } catch (error) {
    console.error('Lỗi khi tải danh sách cảnh báo:', error)
  } finally {
    isLoading.value = false
  }
}

const goToCreateAlert = () => {
  router.push('/alert')
}

const changePage = (page) => {
  fetchAlerts(page)
}

const editAlert = (id) => {
  // TODO: Navigate to edit page
  router.push(`/alert/edit/${id}`)
}

const deleteAlert = async (id) => {
  showDeleteConfirm.value = true
  deleteAlertId.value = id
}

const confirmDelete = async () => {
  showDeleteConfirm.value = false
  
  try {
    const token = localStorage.getItem('access_token')
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    // Soft delete using DELETE method (backend handles soft delete)
    const response = await fetch(`http://127.0.0.1:8000/api/alerts/${deleteAlertId.value}`, {
      method: 'DELETE',
      headers
    })
    const result = await response.json()

    if (result.success) {
      showMessagePopup('Xóa cảnh báo thành công!', 'success')
      fetchAlerts(pagination.value.current_page)
    } else {
      showMessagePopup('Không thể xóa cảnh báo!', 'error')
    }
  } catch (error) {
    console.error('Lỗi khi xóa cảnh báo:', error)
    showMessagePopup('Không thể xóa cảnh báo!', 'error')
  }
  
  deleteAlertId.value = null
}

const cancelDelete = () => {
  showDeleteConfirm.value = false
  deleteAlertId.value = null
}

const showMessagePopup = (message, type = 'success') => {
  messageContent.value = message
  messageType.value = type
  showMessage.value = true
  
  // Auto close after 3 seconds
  setTimeout(() => {
    showMessage.value = false
  }, 3000)
}

const viewDetail = (id) => {
  router.push(`/alerts/${id}`)
}

onMounted(() => {
  fetchAlerts()
})
</script>

<template>
  <div class="alert-list-page">
    <div class="alert-list-container">
      <!-- Header -->
      <header class="page-header">
        <div class="header-content">
          <h1>📋 Cảnh Báo Của Tôi</h1>
          <p>Quản lý các cảnh báo giao thông bạn đã gửi</p>
        </div>
        <button @click="goToCreateAlert" class="create-btn">
          <span>➕</span>
          <span>Tạo Cảnh Báo Mới</span>
        </button>
      </header>

      <!-- Loading -->
      <div v-if="isLoading" class="loading-state">
        <div class="spinner"></div>
        <p>Đang tải dữ liệu...</p>
      </div>

      <!-- Table List -->
      <div v-else-if="alerts.length > 0" class="table-wrapper">
        <table class="alerts-table">
          <thead>
            <tr>
              <th>Loại</th>
              <th>Địa điểm</th>
              <th>Mức độ</th>
              <th>Trạng thái</th>
              <th>Mô tả</th>
              <th>Ảnh</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="alert in alerts"
              :key="alert.id"
              class="table-row"
              @click="viewDetail(alert.id)"
            >
              <td class="type-cell">
                <div class="type-badge">
                  <span class="type-icon">{{ typeIcons[alert.loai_canh_bao] }}</span>
                  <span>{{ typeLabels[alert.loai_canh_bao] }}</span>
                </div>
              </td>



              <td class="location-cell">
                <div class="location-info">
                  <span class="location-icon">📍</span>
                  <span>{{ alert.dia_chi }}</span>
                </div>
              </td>

              <td class="severity-cell">
                <span
                  class="severity-badge"
                  :style="{
                    backgroundColor: severityColors[alert.muc_do],
                    boxShadow: `0 0 8px ${severityColors[alert.muc_do]}40`,
                  }"
                >
                  {{ severityLabels[alert.muc_do] }}
                </span>
              </td>

              <td class="status-cell">
                <span
                  class="status-badge"
                  :style="{
                    backgroundColor: statusColors[alert.trang_thai],
                    boxShadow: `0 0 8px ${statusColors[alert.trang_thai]}40`,
                  }"
                >
                  {{ statusLabels[alert.trang_thai] }}
                </span>
              </td>

              <td class="description-cell">
                <span class="description-text">{{ alert.mo_ta || 'Không có mô tả' }}</span>
              </td>

              <td class="media-cell">
                <div v-if="alert.media && alert.media.length > 0" class="media-preview">
                  <img
                    :src="alert.media[0].url"
                    :alt="typeLabels[alert.loai_canh_bao]"
                    class="media-thumbnail"
                  />
                  <span v-if="alert.media.length > 1" class="media-count">
                    +{{ alert.media.length - 1 }}
                  </span>
                </div>
                <span v-else class="no-media">Không có ảnh</span>
              </td>

              <td class="action-cell">
                <div class="action-buttons">
                  <button
                    v-if="alert.trang_thai === 'cho_duyet'"
                    @click.stop="editAlert(alert.id)"
                    class="action-btn edit-btn"
                    title="Chỉnh sửa"
                  >
                    ✏️
                  </button>
                  <button @click.stop="deleteAlert(alert.id)" class="action-btn delete-btn" title="Xóa">
                    🗑️
                  </button>
                  <span v-if="alert.trang_thai !== 'cho_duyet'" class="no-action">
                    {{ alert.trang_thai === 'da_duyet' ? '✓ Đã duyệt' : '✕ Đã từ chối' }}
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Chưa có cảnh báo nào</h3>
        <p>Hãy là người đầu tiên tạo cảnh báo giao thông!</p>
        <button @click="goToCreateAlert" class="empty-create-btn">Tạo Cảnh Báo Đầu Tiên</button>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="pagination">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="page-btn"
        >
          ← Trước
        </button>
        <span class="page-info">
          Trang {{ pagination.current_page }} / {{ pagination.last_page }}
        </span>
        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="page-btn"
        >
          Sau →
        </button>
      </div>
    </div>

    <!-- Delete Confirmation Popup -->
    <div v-if="showDeleteConfirm" class="popup-overlay" @click="cancelDelete">
      <div class="popup-modal" @click.stop>
        <div class="popup-header">
          <div class="popup-icon warning">⚠️</div>
          <h3>Xác nhận xóa</h3>
        </div>
        <div class="popup-body">
          <p>Bạn có chắc muốn xóa cảnh báo này?</p>
          <p class="popup-warning">Hành động này không thể hoàn tác!</p>
        </div>
        <div class="popup-actions">
          <button @click="cancelDelete" class="popup-btn cancel-btn">
            <span>✕</span>
            <span>Hủy</span>
          </button>
          <button @click="confirmDelete" class="popup-btn confirm-btn">
            <span>🗑️</span>
            <span>Xóa</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Message Popup -->
    <div v-if="showMessage" class="popup-overlay message-overlay" @click="showMessage = false">
      <div class="popup-modal message-modal" :class="messageType" @click.stop>
        <div class="popup-header">
          <div class="popup-icon" :class="messageType">
            {{ messageType === 'success' ? '✓' : '✕' }}
          </div>
          <h3>{{ messageType === 'success' ? 'Thành công' : 'Lỗi' }}</h3>
        </div>
        <div class="popup-body">
          <p>{{ messageContent }}</p>
        </div>
        <div class="popup-actions">
          <button @click="showMessage = false" class="popup-btn ok-btn">
            <span>OK</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.alert-list-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  padding: 2rem;
  overflow-y: auto;
}

.alert-list-container {
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
  animation: slideDown 0.6s ease;
}

.header-content h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.5rem;
  text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.header-content p {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 1rem;
}

.filter-actions {
  display: flex;
  gap: 1rem;
}

.filter-btn {
  padding: 0.6rem 1.2rem;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.filter-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.filter-btn.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.create-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  border-radius: 16px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
}

.create-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(16, 185, 129, 0.6);
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 4rem;
  background: white;
  border-radius: 24px;
}

.spinner {
  width: 60px;
  height: 60px;
  border: 4px solid #f3f4f6;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1.5rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loading-state p {
  font-size: 1.2rem;
  color: #6b7280;
  font-weight: 500;
}

/* Table */
.table-wrapper {
  background: white;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  animation: fadeIn 0.6s ease;
}

.alerts-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.alerts-table thead {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.alerts-table th {
  padding: 1.25rem 1rem;
  text-align: left;
  font-weight: 700;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

/* Column width distribution - Total: 100% */
.alerts-table th:nth-child(1), /* Loại */
.alerts-table td:nth-child(1) {
  width: 12%;
}

.alerts-table th:nth-child(2), /* Địa điểm */
.alerts-table td:nth-child(2) {
  width: 22%;
}

.alerts-table th:nth-child(3), /* Mức độ */
.alerts-table td:nth-child(3) {
  width: 12%;
}

.alerts-table th:nth-child(4), /* Trạng thái */
.alerts-table td:nth-child(4) {
  width: 10%;
}

.alerts-table th:nth-child(5), /* Mô tả */
.alerts-table td:nth-child(5) {
  width: 20%;
}

.alerts-table th:nth-child(6), /* Ảnh */
.alerts-table td:nth-child(6) {
  width: 10%;
  text-align: center;
}

.alerts-table th:nth-child(7), /* Hành động */
.alerts-table td:nth-child(7) {
  width: 14%;
  text-align: center;
}

.alerts-table tbody tr {
  border-bottom: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.alerts-table tbody tr:hover {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  transform: scale(1.01);
}

.alerts-table td {
  padding: 1.25rem 1rem;
  vertical-align: middle;
}

.type-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-radius: 12px;
  font-weight: 600;
}

.type-icon {
  font-size: 1.25rem;
}

.sender-info-table {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.sender-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #667eea;
}

.sender-avatar-placeholder {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.8rem;
}

.sender-name {
  font-weight: 600;
  color: #1f2937;
  font-size: 0.9rem;
}

.system-sender-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-style: italic;
  font-size: 0.85rem;
}

.system-icon {
  font-size: 1.1rem;
}

.location-cell {
  max-width: 280px;
}

.location-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #374151;
  font-weight: 500;
}

.location-info span:last-child {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 240px;
}

.location-icon {
  font-size: 1.1rem;
}

.severity-badge,
.status-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 12px;
  color: white;
  font-size: 0.9rem;
  font-weight: 600;
  white-space: nowrap;
}

.description-text {
  display: block;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #6b7280;
}

.media-preview {
  position: relative;
  display: inline-block;
}

.media-thumbnail {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.media-count {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #667eea;
  color: white;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.25rem 0.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
}

.no-media {
  color: #9ca3af;
  font-style: italic;
  font-size: 0.9rem;
}

.time-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.9rem;
}

.time-icon {
  font-size: 1rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.edit-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.edit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(59, 130, 246, 0.5);
}

.delete-btn {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.delete-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.5);
}

.no-action {
  color: #9ca3af;
  font-size: 0.9rem;
  font-style: italic;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 5rem 2rem;
  background: white;
  border-radius: 24px;
}

.empty-icon {
  font-size: 6rem;
  margin-bottom: 1.5rem;
}

.empty-state h3 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.75rem;
}

.empty-state p {
  font-size: 1.1rem;
  color: #6b7280;
  margin-bottom: 2rem;
}

.empty-create-btn {
  padding: 1rem 2.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 16px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.empty-create-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(102, 126, 234, 0.6);
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  margin-top: 3rem;
  padding: 2rem;
  background: white;
  border-radius: 20px;
}

.page-btn {
  padding: 0.875rem 1.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.page-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
}

/* Animations */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Responsive */
@media (max-width: 1200px) {
  .alerts-table {
    font-size: 0.9rem;
  }

  .alerts-table th,
  .alerts-table td {
    padding: 1rem 0.75rem;
  }
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1.5rem;
    align-items: flex-start;
  }

  .header-content h1 {
    font-size: 2rem;
  }

  .table-wrapper {
    overflow-x: auto;
  }

  .alerts-table {
    min-width: 1000px;
  }
}

/* Popup Styles */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  animation: fadeIn 0.3s ease;
}

.popup-modal {
  background: white;
  border-radius: 24px;
  padding: 2rem;
  max-width: 480px;
  width: 90%;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease;
}

.popup-header {
  text-align: center;
  margin-bottom: 1.5rem;
}

.popup-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  margin: 0 auto 1rem;
  animation: scaleIn 0.4s ease;
}

.popup-icon.warning {
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
  box-shadow: 0 8px 24px rgba(245, 158, 11, 0.3);
}

.popup-icon.success {
  background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
  box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
  color: #059669;
  font-weight: 900;
}

.popup-icon.error {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);
  color: #dc2626;
  font-weight: 900;
}

.popup-header h3 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.popup-body {
  text-align: center;
  margin-bottom: 2rem;
}

.popup-body p {
  font-size: 1.1rem;
  color: #4b5563;
  margin: 0.5rem 0;
  line-height: 1.6;
}

.popup-warning {
  color: #dc2626;
  font-weight: 600;
  font-size: 0.95rem !important;
}

.popup-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.popup-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.75rem;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 120px;
  justify-content: center;
}

.cancel-btn {
  background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
  color: #374151;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.cancel-btn:hover {
  background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.confirm-btn {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.confirm-btn:hover {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.6);
}

.ok-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.ok-btn:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.6);
}

.message-modal.success .popup-btn {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.message-modal.success .popup-btn:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.6);
}

.message-modal.error .popup-btn {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.message-modal.error .popup-btn:hover {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.6);
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scaleIn {
  from {
    transform: scale(0);
  }
  to {
    transform: scale(1);
  }
}

/* Responsive for popups */
@media (max-width: 480px) {
  .popup-modal {
    padding: 1.5rem;
  }

  .popup-icon {
    width: 60px;
    height: 60px;
    font-size: 2rem;
  }

  .popup-header h3 {
    font-size: 1.5rem;
  }

  .popup-body p {
    font-size: 1rem;
  }

  .popup-actions {
    flex-direction: column;
  }

  .popup-btn {
    width: 100%;
  }
}
</style>
