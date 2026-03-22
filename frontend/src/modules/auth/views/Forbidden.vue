<template>
  <div class="h-screen w-screen flex flex-col items-center justify-center bg-surface-50 dark:bg-surface-950 gap-4">
    <i class="pi pi-lock text-primary" style="font-size: 4rem"></i>
    <h1 class="text-4xl font-bold text-surface-900 m-0">403 Forbidden</h1>
    <p class="text-surface-500 m-0">You don't have permission to access this resource.</p>
    <Button label="Return to Dashboard" icon="pi pi-home" @click="router.push('/')" class="mt-4" />
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import Button from 'primevue/button';

const router = useRouter();
</script>

<style scoped>
.h-screen { height: 100vh; }
.w-screen { width: 100vw; }
.flex { display: flex; }
.flex-col { flex-direction: column; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.gap-4 { gap: 1rem; }
.mt-4 { margin-top: 1rem; }
.m-0 { margin: 0; }
.text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
.font-bold { font-weight: 700; }
.text-primary { color: var(--p-primary-500); }
.text-surface-500 { color: var(--p-surface-500); }
.text-surface-900 { color: var(--p-surface-900); }
.bg-surface-50 { background-color: var(--p-surface-50); }
:root[class="p-dark"] .dark\:bg-surface-950 { background-color: var(--p-surface-950); }
</style>
