<script setup>
import AdminSidebar from '../components/AdminSidebar.vue'
</script>

<template>
  <div class="admin-layout">
    <AdminSidebar />
    <div class="admin-content">
      <router-view />
    </div>
  </div>
</template>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #f3f4f6;
}

.admin-content {
  flex: 1;
  min-height: 100vh;
  position: relative;
  z-index: 1;
  padding-left: 260px;
}
</style>
