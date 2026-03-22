<template>
  <!-- v-if based on permission. NEVER disabled. Hides completely if user lacks permission. -->
  <Button
    v-if="hasAccess"
    :label="label"
    :icon="icon"
    :severity="severity"
    :loading="loading"
    :text="text"
    :outlined="outlined"
    :rounded="rounded"
    @click="$emit('click', $event)"
  />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Button from 'primevue/button';
import { usePermission } from '@/core/composables/usePermission';

interface Props {
  permission?: string;
  label?: string;
  icon?: string;
  severity?: 'success' | 'info' | 'warn' | 'danger' | 'secondary' | 'contrast';
  loading?: boolean;
  text?: boolean;
  outlined?: boolean;
  rounded?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  text: false,
  outlined: false,
  rounded: false
});

defineEmits(['click']);

const { can } = usePermission();

const hasAccess = computed(() => {
  if (!props.permission) return true;
  return can(props.permission);
});
</script>
