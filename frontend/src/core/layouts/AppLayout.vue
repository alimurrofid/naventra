<template>
  <div class="layout-wrapper">
    <AppSidebar :isOpen="isSidebarOpen" />
    
    <div class="layout-main">
      <AppTopbar @toggle="toggleSidebar" />
      
      <main class="layout-content">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useMenuStore } from '@/core/store/menu.store';
import AppSidebar from './components/AppSidebar.vue';
import AppTopbar from './components/AppTopbar.vue';

const menuStore = useMenuStore();
const isSidebarOpen = ref(true);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

onMounted(async () => {
  await menuStore.fetchMenu();
  if (window.innerWidth < 1024) {
    isSidebarOpen.value = false;
  }
});
</script>

<style scoped>
.layout-wrapper {
  display: flex;
  height: 100vh;
  width: 100vw;
  overflow: hidden;
  background-color: var(--erp-bg);
}

.layout-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  overflow: hidden;
}

.layout-content {
  flex: 1;
  overflow-y: auto;
  padding: 24px 32px; 
}

@media (max-width: 768px) {
  .layout-content {
    padding: 16px;
  }
}

/* Global fade transition for pages */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
