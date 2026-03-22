<template>
  <header class="app-topbar">
    <div class="topbar-left">
      <Button icon="pi pi-bars" text rounded @click="$emit('toggle')" class="menu-btn" />
      <Breadcrumb :model="breadcrumbs" class="custom-breadcrumb">
        <template #item="{ item }">
          <span class="breadcrumb-item" :class="{ 'font-semibold': item.label === route.name }">{{ item.label }}</span>
        </template>
      </Breadcrumb>
    </div>
    
    <div class="topbar-right">
      <div class="user-profile">
        <div class="avatar">{{ userInitials }}</div>
        <div class="user-info">
          <span class="user-name">{{ authStore.user?.name }}</span>
          <span class="user-role">{{ userRoleTitle }}</span>
        </div>
      </div>
      <Button icon="pi pi-sign-out" text rounded severity="secondary" @click="handleLogout" title="Logout" />
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/core/store/auth.store';
import Button from 'primevue/button';
import Breadcrumb from 'primevue/breadcrumb';

defineEmits(['toggle']);

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};

const userInitials = computed(() => {
  return authStore.user?.name?.charAt(0).toUpperCase() || 'U';
});

const userRoleTitle = computed(() => {
  return authStore.roles?.[0] || 'User';
});

// Dynamic Breadcrumb based on route
const breadcrumbs = computed(() => {
  if (route.name === 'Dashboard') return [{ label: 'Dashboard' }];
  
  const segments = route.path.split('/').filter(Boolean);
  return segments.map(s => ({ 
    label: s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) 
  }));
});
</script>

<style scoped>
.app-topbar {
  height: 64px;
  background-color: #ffffff;
  border-bottom: 1px solid var(--erp-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px 0 16px;
  flex-shrink: 0;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.menu-btn {
  color: var(--erp-text-muted) !important;
}

/* Customizing Breadcrumb softly */
:deep(.p-breadcrumb) {
  background: transparent;
  border: none;
  padding: 0;
}

:deep(.p-breadcrumb ol) {
  margin: 0;
  padding: 0;
}

:deep(.p-breadcrumb .p-menuitem-text) {
  color: var(--erp-text-muted);
  font-size: 0.875rem;
}

.breadcrumb-item {
  color: var(--erp-text-muted);
}
.font-semibold {
  font-weight: 600;
  color: var(--erp-text-main);
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 24px;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  background-color: #eff6ff;
  color: #2563eb;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  font-size: 0.875rem;
  color: var(--erp-text-main);
  line-height: 1.2;
}

.user-role {
  font-size: 0.75rem;
  color: var(--erp-text-muted);
  text-transform: capitalize;
}
</style>
