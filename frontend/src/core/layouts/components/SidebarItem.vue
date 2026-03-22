<template>
  <div class="sidebar-item-wrapper">
    <!-- If it has a route, it's a leaf node -->
    <router-link 
      v-if="item.route"
      :to="item.route"
      class="nav-item"
      active-class="is-active"
      :title="!isOpen ? item.label : ''"
      :style="{ paddingLeft: `${level * 16 + 12}px` }"
    >
      <i :class="item.icon" class="nav-icon"></i>
      <span v-if="isOpen" class="nav-label">{{ item.label }}</span>
    </router-link>

    <!-- If it has children, it's a folder -->
    <div v-else class="nav-folder">
      <div 
        class="nav-item nav-folder-toggle" 
        @click="toggle"
        :title="!isOpen ? item.label : ''"
        :style="{ paddingLeft: `${level * 16 + 12}px` }"
      >
        <i :class="item.icon" class="nav-icon"></i>
        <span v-if="isOpen" class="nav-label flex-1 truncate">{{ item.label }}</span>
        <i v-if="isOpen" class="pi transition-transform duration-200" :class="expanded ? 'pi-chevron-down' : 'pi-chevron-right'" style="font-size: 0.75rem"></i>
      </div>
      
      <!-- Children wrapper -->
      <div v-if="expanded && isOpen" class="folder-children flex flex-col gap-1 mt-1">
        <SidebarItem 
          v-for="child in item.children" 
          :key="child.label"
          :item="child"
          :is-open="isOpen"
          :level="level + 1"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
  item: any;
  isOpen: boolean;
  level: number;
}>();

const expanded = ref(false);

const toggle = () => {
  expanded.value = !expanded.value;
};
</script>

<style scoped>
.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 8px;
  text-decoration: none;
  color: var(--erp-text-muted, #64748b);
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.2s ease;
  white-space: nowrap;
  cursor: pointer;
  user-select: none;
}

.nav-item:hover {
  background-color: #f1f5f9;
  color: var(--erp-text-main, #1e293b);
}

.nav-item.is-active {
  background-color: #eff6ff;
  color: #2563eb;
  font-weight: 600;
}

.nav-icon {
  font-size: 1.1rem;
  width: 24px;
  display: flex;
  justify-content: center;
  flex-shrink: 0;
}
</style>
