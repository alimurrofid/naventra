<template>
  <aside 
    class="app-sidebar"
    :class="{ 'is-collapsed': !isOpen }"
  >
    <div class="sidebar-header">
      <div class="logo-box">
        <i class="pi pi-box logo-icon"></i>
        <span v-if="isOpen" class="logo-text">Naventra ERP</span>
      </div>
    </div>
    
    <nav class="sidebar-nav">
      <SidebarItem 
        v-for="item in menuStore.filteredMenu" 
        :key="item.label"
        :item="item"
        :is-open="isOpen"
        :level="0"
      />
    </nav>
  </aside>
</template>

<script setup lang="ts">
import { useMenuStore } from '@/core/store/menu.store';
import SidebarItem from './SidebarItem.vue';

defineProps<{ isOpen: boolean }>();

const menuStore = useMenuStore();
</script>

<style scoped>
.app-sidebar {
  width: 260px;
  background-color: #f8fafc;
  border-right: 1px solid var(--erp-border);
  display: flex;
  flex-direction: column;
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  z-index: 10;
}

.app-sidebar.is-collapsed {
  width: 72px; /* enough space for icons and padding */
}

.sidebar-header {
  height: 64px;
  display: flex;
  align-items: center;
  padding: 0 20px;
  border-bottom: 1px solid transparent;
  flex-shrink: 0;
}

.logo-box {
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--erp-text-main);
  overflow: hidden;
  width: 100%;
}

.logo-icon {
  font-size: 1.5rem;
  color: #3b82f6; 
  flex-shrink: 0;
  width: 32px;
  display: flex;
  justify-content: center;
}

.logo-text {
  font-weight: 700;
  font-size: 1.125rem;
  white-space: nowrap;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  padding: 16px 12px;
  gap: 4px;
  flex: 1;
  overflow-y: auto;
}
</style>
