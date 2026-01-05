<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'

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

const severityLevels = ref([])
const isLoadingSeverity = ref(true)

const isSubmitting = ref(false)
const showSuccess = ref(false)
const showError = ref(false)
const errorMessage = ref('')
const imagePreview = ref([])

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

const fetchSeverityLevels = async () => {
  try {
    isLoadingSeverity.value = true
    const response = await fetch('http://127.0.0.1:8000/api/locations/severity-levels')
    const result = await response.json()
    if (result.success) {
      severityLevels.value = result.data
    }
  } catch (error) {
    console.error('Lỗi khi tải dữ liệu mức độ:', error)
  } finally {
    isLoadingSeverity.value = false
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

// Watch for ward changes
watch(
  () => alertForm.ward,
  (newWard) => {
    // Reset street when ward changes
    alertForm.street = ''

    // Find ward ID from the selected ward value
    const selectedWard = wards.value.find((w) => w.value === newWard)
    if (selectedWard) {
      fetchStreetsByWard(selectedWard.id)
    } else {
      streets.value = []
    }
  },
)

onMounted(() => {
  fetchLocationData()
  fetchSeverityLevels()
})

const handleImageUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach((file) => {
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value.push(e.target.result)
    }
    reader.readAsDataURL(file)
  })
  alertForm.images = files
}

const removeImage = (index) => {
  imagePreview.value.splice(index, 1)
  const dt = new DataTransfer()
  const files = Array.from(alertForm.images)
  files.splice(index, 1)
  files.forEach((file) => dt.items.add(file))
  alertForm.images = dt.files
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
    // Prepare FormData for file upload
    const formData = new FormData()

    // Find ward and street IDs
    const selectedWard = wards.value.find((w) => w.value === alertForm.ward)
    const selectedStreet = streets.value.find((s) => s.value === alertForm.street)

    if (!selectedWard || !selectedStreet) {
      throw new Error('Không tìm thấy thông tin phường/đường')
    }

    // Find khu_vuc_id from street
    formData.append('loai_canh_bao', alertForm.type)
    formData.append('muc_do', alertForm.severity)
    formData.append('khu_vuc_id', selectedStreet.khu_vuc_id) // Using street's khu_vuc_id
    formData.append('duong_id', selectedStreet.id)
    formData.append('mo_ta', alertForm.description || '')

    // Append images
    if (alertForm.images && alertForm.images.length > 0) {
      Array.from(alertForm.images).forEach((file, index) => {
        formData.append(`images[${index}]`, file)
      })
    }

    const response = await fetch('http://127.0.0.1:8000/api/alerts', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('access_token') || localStorage.getItem('token')}`
      },
      body: formData,
    })

    console.log('Response Status:', response.status);

    if (response.status === 401) {
      throw new Error('Phiên làm việc hết hạn. Vui lòng đăng nhập lại.');
    }

    // Get response as text first to handle PHP notices
    const responseText = await response.text()

    // Try to extract JSON from response (remove PHP notices/warnings)
    let result
    try {
      // Find the JSON part (starts with { and ends with })
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

      // Reset form
      Object.assign(alertForm, {
        type: '',
        ward: '',
        street: '',
        severity: '',
        description: '',
        images: [],
      })
      imagePreview.value = []

      // Redirect to alerts list after showing success message
      setTimeout(() => {
        showSuccess.value = false
        router.push('/alerts')
      }, 2000)
    } else {
      throw new Error(result.message || 'Có lỗi xảy ra')
    }
  } catch (error) {
    isSubmitting.value = false
    errorMessage.value = error.message || 'Không thể gửi cảnh báo. Vui lòng thử lại!'
    showError.value = true
    setTimeout(() => {
      showError.value = false
    }, 2000)
  }
}
</script>

<template>
  <div class="alert-page">
    <div class="alert-container">
      <header class="page-header">
        <div class="header-content">
          <h1>📢 Gửi Cảnh Báo Giao Thông</h1>
          <p>Giúp cộng đồng cập nhật tình hình giao thông thời gian thực</p>
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
              <!-- Phường/Xã - Chọn trước -->
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

              <!-- Đường - Load sau khi chọn phường -->
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
              <span class="label-icon">⚡</span>
              Mức độ nghiêm trọng <span class="required">*</span>
            </label>
            <div class="severity-grid">
              <div v-if="isLoadingSeverity" class="loading-severity">
                Đang tải mức độ...
              </div>
              <label
                v-else
                v-for="level in severityLevels"
                :key="level.value"
                class="severity-card"
                :class="{ active: alertForm.severity === level.value }"
                :style="{ '--severity-color': level.color }"
              >
                <input type="radio" :value="level.value" v-model="alertForm.severity" hidden />
                <span class="severity-dot"></span>
                <span class="severity-label">{{ level.label }}</span>
              </label>
            </div>
          </div>

          <!-- Description -->
          <div class="form-section">
            <label class="section-label">
              <span class="label-icon">✍️</span>
              Mô tả chi tiết
            </label>
            <textarea
              v-model="alertForm.description"
              placeholder="Mô tả tình hình giao thông, số lượng phương tiện, thời gian ước tính..."
              class="form-textarea"
              rows="4"
            ></textarea>
          </div>

          <!-- Image Upload -->
          <div class="form-section">
            <label class="section-label">
              <span class="label-icon">📷</span>
              Hình ảnh
            </label>
            <div class="upload-area">
              <input
                type="file"
                @change="handleImageUpload"
                accept="image/*"
                multiple
                id="imageUpload"
                hidden
              />
              <label for="imageUpload" class="upload-label">
                <span class="upload-icon">📸</span>
                <span class="upload-text">Chọn ảnh hoặc kéo thả vào đây</span>
                <span class="upload-hint">Hỗ trợ: JPG, PNG, GIF (Tối đa 5 ảnh)</span>
              </label>
            </div>

            <!-- Image Preview -->
            <div v-if="imagePreview.length > 0" class="image-preview-grid">
              <div v-for="(image, index) in imagePreview" :key="index" class="preview-item">
                <img :src="image" alt="Preview" />
                <button type="button" @click="removeImage(index)" class="remove-btn">✕</button>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="form-actions">
            <button
              type="submit"
              class="submit-btn"
              :disabled="isSubmitting"
              :class="{ submitting: isSubmitting }"
            >
              <span v-if="!isSubmitting">🚀 Gửi Cảnh Báo</span>
              <span v-else>⏳ Đang gửi...</span>
            </button>
          </div>
        </form>

        <!-- Success Message -->
        <transition name="fade">
          <div v-if="showSuccess" class="success-message">
            <div class="success-icon">✅</div>
            <h3>Gửi cảnh báo thành công!</h3>
            <p>Cảm ơn bạn đã đóng góp thông tin cho cộng đồng</p>
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
.alert-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  padding: 2rem;
  overflow-y: auto;
  position: relative;
}

.alert-page::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background:
    radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(240, 147, 251, 0.3) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
}

.alert-container {
  max-width: 1000px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

.page-header {
  text-align: center;
  margin-bottom: 3rem;
  animation: slideDown 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.header-content h1 {
  font-size: 3rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.75rem;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  letter-spacing: -1px;
}

.header-content p {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.95);
  font-weight: 500;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.form-wrapper {
  position: relative;
}

.alert-form {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  border-radius: 32px;
  padding: 3rem;
  box-shadow:
    0 30px 90px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.3) inset;
  animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.form-section {
  margin-bottom: 2.5rem;
}

.section-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1.25rem;
}

.label-icon {
  font-size: 1.5rem;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.required {
  color: #ef4444;
  font-weight: 700;
}

/* Type Selection */
.type-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.type-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 2rem;
  border: 3px solid transparent;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
  position: relative;
  overflow: hidden;
  min-height: 160px;
}

.type-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  opacity: 0;
  transition: opacity 0.4s ease;
}

.type-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 20px 40px rgba(102, 126, 234, 0.25);
  border-color: #667eea;
}

.type-card:hover::before {
  opacity: 1;
}

.type-card.active {
  border-color: transparent;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow:
    0 20px 50px rgba(102, 126, 234, 0.5),
    0 0 0 4px rgba(102, 126, 234, 0.2);
  transform: translateY(-8px) scale(1.05);
}

.type-card.active::before {
  opacity: 0;
}

.type-icon {
  font-size: 3.5rem;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
  transition: transform 0.4s ease;
}

.type-card:hover .type-icon {
  transform: scale(1.1) rotate(5deg);
}

.type-card.active .type-icon {
  transform: scale(1.15);
  filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3));
}

.type-label {
  font-size: 1.15rem;
  font-weight: 600;
  text-align: center;
  position: relative;
  z-index: 1;
}

/* Location Selection */
.location-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.select-wrapper {
  position: relative;
}

.form-select {
  width: 100%;
  padding: 1.25rem 3rem 1.25rem 1.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 16px;
  font-size: 1.05rem;
  transition: all 0.3s ease;
  background: #f9fafb;
  font-weight: 500;
  cursor: pointer;
  appearance: none;
}

.form-select:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow:
    0 0 0 4px rgba(102, 126, 234, 0.1),
    0 8px 16px rgba(102, 126, 234, 0.15);
  transform: translateY(-2px);
}

.select-icon {
  position: absolute;
  right: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.5rem;
  pointer-events: none;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.location-preview {
  margin-top: 1rem;
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border: 2px solid rgba(102, 126, 234, 0.2);
}

.preview-icon {
  font-size: 1.5rem;
}

.preview-text {
  font-size: 1.05rem;
  font-weight: 600;
  color: #667eea;
}

/* Severity Selection */
.severity-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 1rem;
}

.loading-severity {
  grid-column: 1 / -1;
  padding: 1rem;
  text-align: center;
  color: #6b7280;
  background: #f3f4f6;
  border-radius: 12px;
  font-weight: 500;
}

.severity-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem 1.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #f9fafb;
  position: relative;
  overflow: hidden;
}

.severity-card::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: var(--severity-color);
  transform: scaleY(0);
  transition: transform 0.3s ease;
}

.severity-card:hover {
  border-color: var(--severity-color);
  transform: translateX(4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  background: white;
}

.severity-card:hover::before {
  transform: scaleY(1);
}

.severity-card.active {
  border-color: var(--severity-color);
  background: var(--severity-color);
  color: white;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
  transform: translateX(4px) scale(1.02);
}

.severity-card.active::before {
  width: 100%;
  transform: scaleY(1);
}

.severity-dot {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: var(--severity-color);
  box-shadow: 0 0 12px var(--severity-color);
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.severity-card.active .severity-dot {
  background: white;
  box-shadow: 0 0 16px white;
  transform: scale(1.2);
}

.severity-label {
  font-weight: 600;
  font-size: 1rem;
}

/* Textarea */
.form-textarea {
  width: 100%;
  padding: 1.25rem 1.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 16px;
  font-size: 1.05rem;
  font-family: inherit;
  resize: vertical;
  transition: all 0.3s ease;
  background: #f9fafb;
  font-weight: 500;
  line-height: 1.6;
}

.form-textarea:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow:
    0 0 0 4px rgba(102, 126, 234, 0.1),
    0 8px 16px rgba(102, 126, 234, 0.15);
}

/* Upload Area */
.upload-area {
  margin-bottom: 1.5rem;
}

.upload-label {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 3rem;
  border: 3px dashed #cbd5e1;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.4s ease;
  background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
  position: relative;
  overflow: hidden;
}

.upload-label::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.4s ease;
}

.upload-label:hover {
  border-color: #667eea;
  border-style: solid;
  transform: scale(1.01);
  box-shadow: 0 12px 24px rgba(102, 126, 234, 0.15);
}

.upload-label:hover::before {
  opacity: 1;
}

.upload-icon {
  font-size: 4rem;
  transition: transform 0.4s ease;
}

.upload-label:hover .upload-icon {
  transform: scale(1.1) rotate(5deg);
}

.upload-text {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  position: relative;
  z-index: 1;
}

.upload-hint {
  font-size: 0.95rem;
  color: #6b7280;
  position: relative;
  z-index: 1;
}

/* Image Preview */
.image-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 1.25rem;
}

.preview-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  transition: transform 0.3s ease;
}

.preview-item:hover {
  transform: scale(1.05);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
}

.preview-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.preview-item:hover img {
  transform: scale(1.1);
}

.remove-btn {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  width: 32px;
  height: 32px;
  background: rgba(239, 68, 68, 0.95);
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
  z-index: 2;
}

.remove-btn:hover {
  background: #dc2626;
  transform: scale(1.15) rotate(90deg);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.6);
}

/* Submit Button */
.form-actions {
  margin-top: 3rem;
}

.submit-btn {
  width: 100%;
  padding: 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 20px;
  font-size: 1.3rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow:
    0 12px 28px rgba(102, 126, 234, 0.5),
    0 0 0 0 rgba(102, 126, 234, 0.4);
  position: relative;
  overflow: hidden;
  letter-spacing: 0.5px;
}

.submit-btn::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  transform: translate(-50%, -50%);
  transition:
    width 0.6s,
    height 0.6s;
}

.submit-btn:hover:not(:disabled)::before {
  width: 400px;
  height: 400px;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-4px);
  box-shadow:
    0 20px 40px rgba(102, 126, 234, 0.6),
    0 0 0 4px rgba(102, 126, 234, 0.2);
}

.submit-btn:active:not(:disabled) {
  transform: translateY(-2px);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.submit-btn.submitting {
  animation: pulse 1.5s infinite;
}

.submit-btn span {
  position: relative;
  z-index: 1;
}

/* Success Message */
.success-message {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 3rem;
  border-radius: 32px;
  box-shadow:
    0 30px 90px rgba(0, 0, 0, 0.3),
    0 0 0 1px rgba(255, 255, 255, 0.5) inset;
  text-align: center;
  z-index: 1000;
  min-width: 400px;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.success-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
  animation: bounce 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  filter: drop-shadow(0 8px 16px rgba(16, 185, 129, 0.3));
}

.success-message h3 {
  font-size: 1.75rem;
  font-weight: 800;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.75rem;
}

.success-message p {
  color: #6b7280;
  font-size: 1.1rem;
  font-weight: 500;
}

/* Error Message */
.error-message {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 3rem;
  border-radius: 32px;
  box-shadow:
    0 30px 90px rgba(0, 0, 0, 0.3),
    0 0 0 1px rgba(255, 255, 255, 0.5) inset;
  text-align: center;
  z-index: 1000;
  min-width: 400px;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.error-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
  animation: shake 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  filter: drop-shadow(0 8px 16px rgba(239, 68, 68, 0.3));
}

.error-message h3 {
  font-size: 1.75rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.75rem;
}

.error-message p {
  color: #6b7280;
  font-size: 1.1rem;
  font-weight: 500;
  margin-bottom: 2rem;
}

/* Popup Buttons */
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

.popup-btn-primary:active {
  transform: translateY(0);
}

.popup-btn-secondary {
  background: #f3f4f6;
  color: #374151;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.popup-btn-secondary:hover {
  background: #e5e7eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.popup-btn-secondary:active {
  transform: translateY(0);
}

/* Animations */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(50px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.9;
    transform: scale(0.98);
  }
}

@keyframes bounce {
  0%,
  100% {
    transform: scale(1) translateY(0);
  }
  25% {
    transform: scale(1.1) translateY(-10px);
  }
  50% {
    transform: scale(1.05) translateY(0);
  }
  75% {
    transform: scale(1.08) translateY(-5px);
  }
}

@keyframes shake {
  0%,
  100% {
    transform: translateX(0);
  }
  10%,
  30%,
  50%,
  70%,
  90% {
    transform: translateX(-10px);
  }
  20%,
  40%,
  60%,
  80% {
    transform: translateX(10px);
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from {
  opacity: 0;
  transform: translate(-50%, -50%) scale(0.9);
}

.fade-leave-to {
  opacity: 0;
  transform: translate(-50%, -50%) scale(1.1);
}

/* Responsive */
@media (max-width: 768px) {
  .alert-page {
    padding: 1.5rem;
  }

  .alert-form {
    padding: 2rem;
    border-radius: 24px;
  }

  .header-content h1 {
    font-size: 2rem;
  }

  .header-content p {
    font-size: 1rem;
  }

  .type-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .type-card {
    min-height: 140px;
    padding: 1.5rem;
  }

  .type-icon {
    font-size: 3rem;
  }

  .location-grid {
    grid-template-columns: 1fr;
  }

  .severity-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .success-message {
    min-width: 320px;
    padding: 2rem;
  }

  .submit-btn {
    font-size: 1.1rem;
  }
}

@media (max-width: 480px) {
  .severity-grid {
    grid-template-columns: 1fr;
  }
}
</style>
