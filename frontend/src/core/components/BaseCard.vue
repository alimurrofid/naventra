<template>
  <Card class="h-full">
    <template #title v-if="$slots.title || title">
      <slot name="title">
        <h3 class="m-0 text-xl font-semibold">{{ title }}</h3>
      </slot>
    </template>
    
    <template #subtitle v-if="$slots.subtitle || subtitle">
      <slot name="subtitle">
        <span class="text-surface-500">{{ subtitle }}</span>
      </slot>
    </template>

    <template #content>
      <slot name="content">
        <slot></slot>
      </slot>
    </template>

    <template #footer v-if="$slots.footer">
      <div class="pt-4 border-t border-surface-200 mt-2">
        <slot name="footer"></slot>
      </div>
    </template>
  </Card>
</template>

<script setup lang="ts">
import Card from 'primevue/card';

interface Props {
  title?: string;
  subtitle?: string;
}

withDefaults(defineProps<Props>(), {});
</script>

<style scoped>
.h-full { height: 100%; }
.m-0 { margin: 0; }
.mt-2 { margin-top: 0.5rem; }
.pt-4 { padding-top: 1rem; }
.text-xl { font-size: 1.125rem; line-height: 1.5rem; }
.font-semibold { font-weight: 600; }
.text-surface-500 { color: var(--erp-text-muted); }
.border-t { border-top-width: 1px; border-top-style: solid; }
.border-surface-200 { border-color: var(--erp-border); }

/* Force PrimeVue internal wrappers to respect flex bounds to prevent child tables from bleeding out */
:deep(.p-card-body) {
  flex: 1 1 0%;
  display: flex;
  flex-direction: column;
  min-height: 0;
}
:deep(.p-card-content) {
  flex: 1 1 0%;
  display: flex;
  flex-direction: column;
  min-height: 0;
}
</style>
