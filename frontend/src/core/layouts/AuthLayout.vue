<template>
  <div class="h-screen w-screen flex items-center justify-center bg-surface-50 dark:bg-surface-950">
    <router-view />
  </div>
</template>

<script setup lang="ts">
// Auth Layout
</script>

<style scoped>
.h-screen { height: 100vh; }
.w-screen { width: 100vw; }
.flex { display: flex; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.bg-surface-50 { background-color: var(--p-surface-50); }
:root[class="p-dark"] .dark\:bg-surface-950 { background-color: var(--p-surface-950); }
</style>
