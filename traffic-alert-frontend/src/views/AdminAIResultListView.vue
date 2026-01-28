<template>
  <div class="admin-ai-results">
    <div class="page-header">
      <h1>🤖 Quản lý Kết quả AI</h1>
      <div class="stats">
        <div class="stat-card">
          <span class="stat-value">{{ stats.total }}</span>
          <span class="stat-label">Tổng số</span>
        </div>
        <div class="stat-card verified">
          <span class="stat-value">{{ stats.verified }}</span>
          <span class="stat-label">Đã xác minh</span>
        </div>
        <div class="stat-card unverified">
          <span class="stat-value">{{ stats.unverified }}</span>
          <span class="stat-label">Chưa xác minh</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters">
      <input v-model="searchQuery" type="text" placeholder="🔍 Tìm kiếm..." class="search-input" @input="fetchResults" />
      
      <select v-model="filterVerified" @change="fetchResults" class="filter-select">
        <option value="">Tất cả trạng thái</option>
        <option value="true">Đã xác minh</option>
        <option value="false">Chưa xác minh</option>
      </select>

      <select v-model="filterLabel" @change="fetchResults" class="filter-select">
        <option value="">Tất cả nhãn</option>
        <option value="traffic_jam">Tắc đường</option>
        <option value="accident">Tai nạn</option>
        <option value="flood">Ngập nước</option>
        <option value="construction">Thi công</option>
      </select>
    </div>

    <!-- Results Table -->
    <div class="table-container">
      <table v-if="!loading && results.length > 0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nhãn</th>
            <th>Độ tin cậy</th>
            <th>Trạng thái</th>
            <th>Người xác minh</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="result in results" :key="result.id">
            <td>{{ result.id }}</td>
            <td><span class="label-badge">{{ getLabelText(result.nhan) }}</span></td>
            <td>
              <div class="confidence-bar">
                <div class="confidence-fill" :style="{ width: (result.do_tin_cay * 100) + '%', background: getConfidenceColor(result.do_tin_cay) }"></div>
                <span class="confidence-text">{{ (result.do_tin_cay * 100).toFixed(1) }}%</span>
              </div>
            </td>
            <td>
              <span :class="`status ${result.da_xac_minh ? 'verified' : 'unverified'}`">
                {{ result.da_xac_minh ? '✓ Đã xác minh' : '⏳ Chưa xác minh' }}
              </span>
            </td>
            <td>{{ result.nguoi_xac_minh?.ten_dang_nhap || '-' }}</td>
            <td>{{ formatDate(result.created_at) }}</td>
            <td class="actions">
              <button v-if="!result.da_xac_minh" @click="verifyResult(result.id)" class="btn-verify" title="Xác minh">✓</button>
              <button v-else @click="unverifyResult(result.id)" class="btn-unverify" title="Hủy xác minh">✗</button>
              <button @click="deleteResult(result.id)" class="btn-delete" title="Xóa">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Đang tải...</p>
      </div>

      <div v-else class="empty-state">
        <p>📭 Không có kết quả AI nào</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="pagination">
      <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="btn-page">← Trước</button>
      <span class="page-info">Trang {{ pagination.current_page }} / {{ pagination.last_page }}</span>
      <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="btn-page">Sau →</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const results = ref([])
const stats = ref({ total: 0, verified: 0, unverified: 0 })
const loading = ref(false)
const searchQuery = ref('')
const filterVerified = ref('')
const filterLabel = ref('')

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
})

onMounted(() => {
  fetchResults()
  fetchStats()
})

function getAuthHeaders() {
  const token = localStorage.getItem('access_token') || localStorage.getItem('token')
  return {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
}

async function fetchResults() {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    })

    if (searchQuery.value) params.append('search', searchQuery.value)
    if (filterVerified.value) params.append('da_xac_minh', filterVerified.value)
    if (filterLabel.value) params.append('nhan', filterLabel.value)

    const response = await fetch(`http://127.0.0.1:8000/api/admin/ai-results?${params}`, {
      headers: getAuthHeaders()
    })
    const data = await response.json()

    if (data.success) {
      results.value = data.data.data
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

async function fetchStats() {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/admin/ai-results/statistics', {
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) {
      stats.value = data.data
    }
  } catch (err) {
    console.error('Error:', err)
  }
}

async function verifyResult(id) {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/admin/ai-results/${id}/verify`, {
      method: 'POST',
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) {
      fetchResults()
      fetchStats()
    }
  } catch (err) {
    alert('Không thể xác minh')
  }
}

async function unverifyResult(id) {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/admin/ai-results/${id}/unverify`, {
      method: 'POST',
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) {
      fetchResults()
      fetchStats()
    }
  } catch (err) {
    alert('Không thể hủy xác minh')
  }
}

async function deleteResult(id) {
  if (!confirm('Bạn có chắc muốn xóa kết quả này?')) return
  
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/admin/ai-results/${id}`, {
      method: 'DELETE',
      headers: getAuthHeaders()
    })
    const data = await response.json()
    if (data.success) {
      fetchResults()
      fetchStats()
    }
  } catch (err) {
    alert('Không thể xóa')
  }
}

function changePage(page) {
  pagination.value.current_page = page
  fetchResults()
}

function getLabelText(label) {
  const labels = {
    traffic_jam: '🚗 Tắc đường',
    accident: '💥 Tai nạn',
    flood: '🌊 Ngập nước',
    construction: '🚧 Thi công'
  }
  return labels[label] || label
}

function getConfidenceColor(confidence) {
  if (confidence >= 0.8) return '#10b981'
  if (confidence >= 0.6) return '#f59e0b'
  return '#ef4444'
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleString('vi-VN')
}
</script>

<style scoped>
.admin-ai-results {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  color: #1a202c;
  margin: 0 0 1.5rem 0;
}

.stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.stat-card.verified {
  border-left: 4px solid #10b981;
}

.stat-card.unverified {
  border-left: 4px solid #f59e0b;
}

.stat-value {
  display: block;
  font-size: 2rem;
  font-weight: 700;
  color: #1a202c;
}

.stat-label {
  display: block;
  font-size: 0.875rem;
  color: #64748b;
  margin-top: 0.5rem;
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

.label-badge {
  padding: 0.25rem 0.75rem;
  background: #e6f7ff;
  color: #0050b3;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
}

.confidence-bar {
  position: relative;
  width: 120px;
  height: 24px;
  background: #f1f5f9;
  border-radius: 12px;
  overflow: hidden;
}

.confidence-fill {
  height: 100%;
  transition: width 0.3s;
}

.confidence-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 0.75rem;
  font-weight: 600;
  color: #1e293b;
}

.status {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status.verified {
  background: #d1fae5;
  color: #059669;
}

.status.unverified {
  background: #fef3c7;
  color: #d97706;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-verify,
.btn-unverify,
.btn-delete {
  padding: 0.5rem;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 1.25rem;
  transition: transform 0.2s;
}

.btn-verify:hover,
.btn-unverify:hover,
.btn-delete:hover {
  transform: scale(1.2);
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
</style>
