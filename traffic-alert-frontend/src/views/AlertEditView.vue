<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const alertForm = reactive({
  type: '',
  ward: '',
  street: '',
  severity: '',
  description: '',
  images: [],
})

const alertTypes = [
  { value: 'traffic', label: 'Tắc đường', icon: '🚗' },
  { value: 'flood', label: 'Ngập đường', icon: '🌊' },
]

const wards = ref([])
const streets = ref([])
const isLoadingData = ref(true)
const isLoadingStreets = ref(false)

const severityLevels = [
  { value: 'low', label: 'Thấp', color: '#10b981' },
  { value: 'medium', label: 'Trung bình', color: '#f59e0b' },
  { value: 'high', label: 'Cao', color: '#ef4444' },
  { value: 'critical', label: 'Nghiêm trọng', color: '#dc2626' },
]

const isSubmitting = ref(false)
const showSuccess = ref(false)
const showError = ref(false)
const errorMessage = ref('')
const imagePreview = ref([])
const existingImages = ref([])

const locationText = computed(() => {
  if (!alertForm.ward && !alertForm.street) return ''
  const wardLabel = wards.value.find((w) => w.value === alertForm.ward)?.label || ''
  const streetLabel = streets.value.find((s) => s.value === alertForm.street)?.label || ''
  return `${streetLabel}${streetLabel && wardLabel ? ', ' : ''}${wardLabel}`
})

const fetchLocationData = async () => {
  try {
    isLoadingData.value = true
    const response = await fetch('http://127.0.0.1:8000/api/locations/wards')
    const result = await response.json()
    if (result.success) {
      wards.value = result.data
    }
  } catch (error) {
    console.error('Lỗi khi tải dữ liệu phường/xã:', error)
  } finally {
    isLoadingData.value = false
  }
}

const fetchStreetsByWard = async (wardId) => {
  if (!wardId) {
    streets.value = []
    return
  }

  try {
    isLoadingStreets.value = true
    const response = await fetch(`http://127.0.0.1:8000/api/locations/streets/ward/${wardId}`)
    const result = await response.json()
    if (result.success) {
      streets.value = result.data
    }
  } catch (error) {
    console.error('Lỗi khi tải dữ liệu đường:', error)
    streets.value = []
  } finally {
    isLoadingStreets.value = false
  }
}

const fetchAlertDetail = async () => {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/alerts/${route.params.id}`)
    const result = await response.json()

    if (result.success) {
      const alert = result.data

      // Populate form
      alertForm.type = alert.loai_canh_bao
      alertForm.severity = alert.muc_do
      alertForm.description = alert.mo_ta || ''

      // Set existing images
      existingImages.value = alert.media || []

      // Wait for wards to load, then set ward and fetch streets
      await fetchLocationData()

      // Find ward by khu_vuc
      const wardId = alert.khu_vuc?.phuong_xa?.id
      if (wardId) {
        const ward = wards.value.find((w) => w.id === wardId)
        if (ward) {
          alertForm.ward = ward.value
          await fetchStreetsByWard(wardId)

          // Set street
          const street = streets.value.find((s) => s.id === alert.duong?.id)
          if (street) {
            alertForm.street = street.value
          }
        }
      }
    }
  } catch (error) {
    console.error('Lỗi khi tải chi tiết cảnh báo:', error)
    router.push('/alerts')
  }
}

// Watch for ward changes
watch(
  () => alertForm.ward,
  (newWard) => {
    alertForm.street = ''
    const selectedWard = wards.value.find((w) => w.value === newWard)
    if (selectedWard) {
      fetchStreetsByWard(selectedWard.id)
    } else {
      streets.value = []
    }
  },
)

const handleImageUpload = (event) => {
  const files = event.target.files
  alertForm.images = files

  // Preview
  imagePreview.value = []
  Array.from(files).forEach((file) => {
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value.push(e.target.result)
    }
    reader.readAsDataURL(file)
  })
}

const removeImage = (index) => {
  const dt = new DataTransfer()
  const files = Array.from(alertForm.images)
  files.splice(index, 1)
  files.forEach((file) => dt.items.add(file))
  alertForm.images = dt.files
  imagePreview.value.splice(index, 1)
}

const submitAlert = async () => {
  if (!alertForm.type || !alertForm.ward || !alertForm.street || !alertForm.severity) {
    errorMessage.value = 'Vui lòng điền đầy đủ thông tin bắt buộc!'
    showError.value = true
    setTimeout(() => {
      showError.value = false
    }, 2000)
    return
  }

  isSubmitting.value = true

  try {
    const formData = new FormData()

    const selectedWard = wards.value.find((w) => w.value === alertForm.ward)
    const selectedStreet = streets.value.find((s) => s.value === alertForm.street)

    if (!selectedWard || !selectedStreet) {
      throw new Error('Không tìm thấy thông tin phường/đường')
    }

    formData.append('loai_canh_bao', alertForm.type)
    formData.append('muc_do', alertForm.severity)
    formData.append('khu_vuc_id', selectedStreet.khu_vuc_id)
    formData.append('duong_id', selectedStreet.id)
    formData.append('mo_ta', alertForm.description || '')
    formData.append('_method', 'PUT') // Laravel method spoofing

    if (alertForm.images && alertForm.images.length > 0) {
      Array.from(alertForm.images).forEach((file, index) => {
        formData.append(`images[${index}]`, file)
      })
    }

    const response = await fetch(`http://127.0.0.1:8000/api/alerts/${route.params.id}`, {
      method: 'POST', // Use POST with _method=PUT
      body: formData,
    })

    const responseText = await response.text()
    let result
    try {
      const jsonMatch = responseText.match(/\{[\s\S]*\}/)
      if (jsonMatch) {
        result = JSON.parse(jsonMatch[0])
      } else {
        throw new Error('Invalid response format')
      }
    } catch (parseError) {
      console.error('Response text:', responseText)
      throw new Error('Không thể xử lý phản hồi từ server')
    }

    if (result.success) {
      isSubmitting.value = false
      showSuccess.value = true

      setTimeout(() => {
        showSuccess.value = false
        router.push('/alerts')
      }, 2000)
    } else {
      throw new Error(result.message || 'Có lỗi xảy ra')
    }
  } catch (error) {
    isSubmitting.value = false
    errorMessage.value = error.message || 'Không thể cập nhật cảnh báo. Vui lòng thử lại!'
    showError.value = true
    setTimeout(() => {
      showError.value = false
    }, 2000)
  }
}

onMounted(() => {
  fetchAlertDetail()
})
</script>

<template>
  <div class="alert-page">
    <div class="alert-container">
      <header class="page-header">
        <div class="header-content">
          <h1>✏️ Chỉnh Sửa Cảnh Báo</h1>
          <p>Cập nhật thông tin cảnh báo giao thông</p>
        </div>
      </header>

      <div class="form-wrapper">
        <form @submit.prevent="submitAlert" class="alert-form">
          <!-- Alert Type -->
          <div class="form-section">
            <label class="section-label">
              <span class="label-icon">📋</span>
              Loại sự cố <span class="required">*</span>
            </label>
            <div class="type-grid">
              <label
                v-for="type in alertTypes"
                :key="type.value"
                class="type-card"
                :class="{ active: alertForm.type === type.value }"
              >
                <input type="radio" :value="type.value" v-model="alertForm.type" hidden />
                <span class="type-icon">{{ type.icon }}</span>
                <span class="type-label">{{ type.label }}</span>
              </label>
            </div>
          </div>

          <!-- Location -->
          <div class="form-section">
            <label class="section-label">
              <span class="label-icon">📍</span>
              Vị trí <span class="required">*</span>
            </label>
            <div class="location-grid">
              <div class="select-wrapper">
                <select v-model="alertForm.ward" class="form-select" :disabled="isLoadingData">
                  <option value="">
                    {{ isLoadingData ? 'Đang tải dữ liệu...' : 'Chọn phường/xã...' }}
                  </option>
                  <option v-for="ward in wards" :key="ward.value" :value="ward.value">
                    {{ ward.label }}
                  </option>
                </select>
                <span class="select-icon">🏘️</span>
              </div>

              <div class="select-wrapper">
                <select
                  v-model="alertForm.street"
                  class="form-select"
                  :disabled="!alertForm.ward || isLoadingStreets"
                >
                  <option value="">
                    {{
                      !alertForm.ward
                        ? 'Chọn phường/xã trước...'
                        : isLoadingStreets
                          ? 'Đang tải đường...'
                          : streets.length === 0
                            ? 'Không có đường nào'
                            : 'Chọn đường...'
                    }}
                  </option>
                  <option v-for="street in streets" :key="street.value" :value="street.value">
                    {{ street.label }}
                  </option>
                </select>
                <span class="select-icon">🛣️</span>
              </div>
            </div>
            <div v-if="locationText" class="location-preview">
              <span class="preview-icon">📌</span>
              <span class="preview-text">{{ locationText }}</span>
            </div>
          </div>

          <!-- Severity -->
          <div class="form-section">
            <label class="section-label">
              <span class="label-icon">⚠️</span>
              Mức độ nghiêm trọng <span class="required">*</span>
            </label>
            <div class="severity-grid">
              <label
                v-for="level in severityLevels"
                :key="level.value"
                class="severity-card"
                :class="{ active: alertForm.severity === level.value }"
                :style="{ '--severity-color': level.color }"
              >
                <input type="radio" :value="level.value" v-model="alertForm.severity" hidden />
                <span class="severity-label">{{ level.label }}</span>
              </label>
            </div>
          </div>

          <!-- Description -->
          <div class="form-section">
            <label class="section-label">
              <span class="label-icon">📝</span>
              Mô tả chi tiết
            </label>
            <textarea
              v-model="alertForm.description"
              class="form-textarea"
              placeholder="Mô tả tình trạng giao thông..."
              rows="4"
            ></textarea>
          </div>

          <!-- Existing Images -->
          <div v-if="existingImages.length > 0" class="form-section">
            <label class="section-label">
              <span class="label-icon">🖼️</span>
              Ảnh hiện tại
            </label>
            <div class="image-preview-grid">
              <div v-for="img in existingImages" :key="img.id" class="preview-item">
                <img :src="img.duong_dan" :alt="img.ten_file" />
              </div>
            </div>
            <p class="help-text">* Upload ảnh mới sẽ thay thế tất cả ảnh cũ</p>
          </div>

          <!-- Image Upload -->
          <div class="form-section">
            <label class="section-label">
              <span class="label-icon">📷</span>
              Upload ảnh mới (Tùy chọn)
            </label>
            <div class="upload-area">
              <input
                type="file"
                id="imageUpload"
                accept="image/*"
                multiple
                @change="handleImageUpload"
                class="file-input"
              />
              <label for="imageUpload" class="upload-label">
                <span class="upload-icon">📁</span>
                <span class="upload-text">Chọn ảnh (tối đa 5 ảnh)</span>
              </label>
            </div>

            <div v-if="imagePreview.length > 0" class="image-preview-grid">
              <div v-for="(preview, index) in imagePreview" :key="index" class="preview-item">
                <img :src="preview" alt="Preview" />
                <button type="button" @click="removeImage(index)" class="remove-btn">✕</button>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="form-actions">
            <button type="button" @click="router.push('/alerts')" class="cancel-btn">Hủy</button>
            <button
              type="submit"
              class="submit-btn"
              :disabled="isSubmitting"
              :class="{ submitting: isSubmitting }"
            >
              <span v-if="!isSubmitting">💾 Lưu Thay Đổi</span>
              <span v-else>⏳ Đang lưu...</span>
            </button>
          </div>
        </form>

        <!-- Success Message -->
        <transition name="fade">
          <div v-if="showSuccess" class="success-message">
            <div class="success-icon">✅</div>
            <h3>Cập nhật thành công!</h3>
            <p>Cảnh báo đã được cập nhật</p>
          </div>
        </transition>

        <!-- Error Message -->
        <transition name="fade">
          <div v-if="showError" class="error-message">
            <div class="error-icon">⚠️</div>
            <h3>Thiếu thông tin!</h3>
            <p>{{ errorMessage }}</p>
            <div class="popup-buttons">
              <button @click="showError = false" class="popup-btn popup-btn-secondary">Đóng</button>
              <button @click="showError = false" class="popup-btn popup-btn-primary">
                Hiểu rồi
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Sử dụng lại CSS từ AlertView.vue */
.alert-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  padding: 2rem;
  overflow-y: auto;
}

.alert-container {
  max-width: 800px;
  margin: 0 auto;
}

.page-header {
  text-align: center;
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
}

.form-wrapper {
  position: relative;
}

.alert-form {
  background: white;
  border-radius: 24px;
  padding: 2.5rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  animation: fadeIn 0.6s ease;
}

.form-section {
  margin-bottom: 2.5rem;
}

.section-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1rem;
}

.label-icon {
  font-size: 1.5rem;
}

.required {
  color: #ef4444;
}

.type-grid,
.severity-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.type-card,
.severity-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 1.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: white;
}

.type-card:hover,
.severity-card:hover {
  border-color: #667eea;
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2);
}

.type-card.active,
.severity-card.active {
  border-color: #667eea;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
}

.type-icon {
  font-size: 2.5rem;
}

.type-label,
.severity-label {
  font-weight: 600;
  color: #374151;
}

.location-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.select-wrapper {
  position: relative;
}

.form-select {
  width: 100%;
  padding: 1rem 3rem 1rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  appearance: none;
  background: white;
  cursor: pointer;
}

.form-select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.select-icon {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.25rem;
  pointer-events: none;
}

.location-preview {
  margin-top: 1rem;
  padding: 1rem;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.preview-icon {
  font-size: 1.5rem;
}

.preview-text {
  font-weight: 600;
  color: #667eea;
}

.form-textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  resize: vertical;
  transition: all 0.3s ease;
}

.form-textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.help-text {
  margin-top: 0.5rem;
  font-size: 0.9rem;
  color: #f59e0b;
  font-style: italic;
}

.upload-area {
  margin-bottom: 1rem;
}

.file-input {
  display: none;
}

.upload-label {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
  border: 2px dashed #d1d5db;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #f9fafb;
}

.upload-label:hover {
  border-color: #667eea;
  background: rgba(102, 126, 234, 0.05);
}

.upload-icon {
  font-size: 3rem;
}

.upload-text {
  font-weight: 600;
  color: #6b7280;
}

.image-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
}

.preview-item {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  aspect-ratio: 1;
}

.preview-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-btn {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 2rem;
  height: 2rem;
  border: none;
  border-radius: 50%;
  background: rgba(239, 68, 68, 0.9);
  color: white;
  font-size: 1.25rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.remove-btn:hover {
  background: #dc2626;
  transform: scale(1.1);
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.cancel-btn,
.submit-btn {
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-btn {
  background: #f3f4f6;
  color: #374151;
}

.cancel-btn:hover {
  background: #e5e7eb;
}

.submit-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(102, 126, 234, 0.6);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Success/Error Messages - Reuse from AlertView */
.success-message,
.error-message {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 3rem;
  border-radius: 32px;
  box-shadow: 0 30px 90px rgba(0, 0, 0, 0.3);
  text-align: center;
  z-index: 1000;
  min-width: 400px;
}

.success-icon,
.error-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
}

.success-message h3 {
  font-size: 1.75rem;
  font-weight: 800;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 0.75rem;
}

.error-message h3 {
  font-size: 1.75rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 0.75rem;
}

.success-message p,
.error-message p {
  color: #6b7280;
  font-size: 1.1rem;
  font-weight: 500;
  margin-bottom: 2rem;
}

.popup-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 2rem;
}

.popup-btn {
  padding: 0.875rem 2rem;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 120px;
}

.popup-btn-primary {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.popup-btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.5);
}

.popup-btn-secondary {
  background: #f3f4f6;
  color: #374151;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.popup-btn-secondary:hover {
  background: #e5e7eb;
  transform: translateY(-2px);
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

.fade-enter-active,
.fade-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translate(-50%, -50%) scale(0.9);
}

/* Responsive */
@media (max-width: 768px) {
  .alert-form {
    padding: 1.5rem;
  }

  .type-grid,
  .severity-grid,
  .location-grid {
    grid-template-columns: 1fr;
  }

  .success-message,
  .error-message {
    min-width: 90%;
    padding: 2rem;
  }
}
</style>
