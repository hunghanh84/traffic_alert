<script setup>
import { ref, onMounted } from 'vue'

// Placeholder data for demonstration
const incidents = ref([
  { id: 1, type: 'accident', location: 'Nguyễn Văn Linh', time: '10:30', severity: 'high' },
  { id: 2, type: 'traffic', location: 'Điện Biên Phủ', time: '11:15', severity: 'medium' },
])

const weatherInfo = ref({
  temperature: '28°C',
  condition: 'Mây rải rác',
  rainfall: '20%'
})
</script>

<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <h1>Bảng điều khiển</h1>
      <div class="weather-info">
        <span class="temp">{{ weatherInfo.temperature }}</span>
        <span class="condition">{{ weatherInfo.condition }}</span>
        <span class="rainfall">Khả năng mưa: {{ weatherInfo.rainfall }}</span>
      </div>
    </header>

    <div class="dashboard-grid">
      <!-- Map Section -->
      <section class="map-section">
        <h2>Bản đồ cảnh báo thời gian thực</h2>
        <div class="map-placeholder">
          [Bản đồ sẽ được hiển thị ở đây]
        </div>
      </section>

      <!-- Recent Incidents -->
      <section class="incidents-section">
        <h2>Sự cố gần đây</h2>
        <div class="incidents-list">
          <div v-for="incident in incidents" :key="incident.id" 
               class="incident-card" :class="incident.severity">
            <div class="incident-icon">
              {{ incident.type === 'accident' ? '🚨' : '🚗' }}
            </div>
            <div class="incident-details">
              <h3>{{ incident.type === 'accident' ? 'Tai nạn' : 'Tắc đường' }}</h3>
              <p>{{ incident.location }}</p>
              <time>{{ incident.time }}</time>
            </div>
          </div>
        </div>
      </section>

      <!-- Quick Stats -->
      <section class="stats-section">
        <h2>Thống kê nhanh</h2>
        <div class="stats-grid">
          <div class="stat-card">
            <h3>Tổng số sự cố hôm nay</h3>
            <div class="stat-value">12</div>
          </div>
          <div class="stat-card">
            <h3>Đang xử lý</h3>
            <div class="stat-value">3</div>
          </div>
          <div class="stat-card">
            <h3>Tuyến đường ảnh hưởng</h3>
            <div class="stat-value">5</div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  padding: 1.5rem;
  background: #f3f4f6;
  height: 100%;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  font-size: 1.875rem;
  font-weight: 600;
  color: #111827;
}

.weather-info {
  background: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 1rem;
  align-items: center;
}

.weather-info .temp {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a1c23;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
  flex: 1;
  min-height: 0; /* Important for nested scrolling */
}

.map-section {
  grid-column: 1;
  grid-row: 1 / 3;
}

.map-placeholder {
  background: white;
  border-radius: 12px;
  height: calc(100vh - 13rem); /* Adjust based on header height */
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  font-size: 1.125rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

section {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

section h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1rem;
}

.incidents-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.incident-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 8px;
  background: #f9fafb;
  border-left: 4px solid;
}

.incident-card.high { border-color: #ef4444; }
.incident-card.medium { border-color: #f59e0b; }
.incident-card.low { border-color: #10b981; }

.incident-icon {
  font-size: 1.5rem;
}

.incident-details h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
}

.incident-details p {
  color: #4b5563;
  font-size: 0.875rem;
}

.incident-details time {
  color: #6b7280;
  font-size: 0.75rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.stat-card {
  background: #f9fafb;
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
}

.stat-card h3 {
  font-size: 0.875rem;
  color: #4b5563;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
}

@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .map-section {
    grid-column: auto;
    grid-row: auto;
  }

  .map-placeholder {
    height: 400px;
  }
}
</style>