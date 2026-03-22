<template>
  <div class="flex items-center gap-3 border-l border-surface-200 pl-3 ml-1">
    <div class="flex items-center gap-1.5 font-medium text-surface-600 text-sm bg-surface-100 px-2 py-1 rounded-md">
      <span class="text-primary-600 font-bold">{{ selectionCount }}</span>
      <span class="text-xs">Selected</span>
    </div>

    <div class="flex gap-2 items-center">
      <BaseButton 
        v-for="action in availableActions" 
        :key="action.key"
        :icon="action.icon"
        :severity="action.severity"
        :permission="action.permission"
        @click="handleAction(action)"
        :title="action.label"
        class="w-8 h-8 p-0 flex items-center justify-center rounded-md"
      />
      
      <span v-if="availableActions.length === 0" class="text-surface-400 text-sm italic whitespace-nowrap">
        No actions for current status.
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import BaseButton from './BaseButton.vue';
import { useWorkflow, type WorkflowAction } from '@/core/composables/useWorkflow';

interface Props {
  selectedRows: any[];
  actions: WorkflowAction[];
  statusField?: string;
}

const props = withDefaults(defineProps<Props>(), {
  selectedRows: () => [],
  actions: () => [],
  statusField: 'status'
});

const emit = defineEmits(['action']);

const { getAvailableActions, getAvailableBulkActions } = useWorkflow();

const selectionCount = computed(() => props.selectedRows.length);

const availableActions = computed(() => {
  if (selectionCount.value === 0) return [];
  
  if (selectionCount.value === 1) {
    // Single item selected - get actions for its status
    const item = props.selectedRows[0];
    const status = item[props.statusField];
    return getAvailableActions(status, props.actions);
  } else {
    // Multiple items - get bulk actions
    const statuses = props.selectedRows.map(item => item[props.statusField]);
    return getAvailableBulkActions(statuses, props.actions);
  }
});

const handleAction = (action: WorkflowAction) => {
  emit('action', {
    action: action.key,
    items: props.selectedRows
  });
};
</script>

<style scoped>
.flex { display: flex; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.gap-1\.5 { gap: 0.375rem; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.pl-3 { padding-left: 0.75rem; }
.ml-1 { margin-left: 0.25rem; }
.px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
.py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
.p-0 { padding: 0 !important; }
.w-8 { width: 2rem !important; }
.h-8 { height: 2rem !important; }
.rounded-md { border-radius: 0.375rem; }
.border-l { border-left-width: 1px; border-left-style: solid; }
.border-surface-200 { border-color: var(--erp-border); }
.font-medium { font-weight: 500; }
.font-bold { font-weight: 700; }
.text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.text-xs { font-size: 0.75rem; line-height: 1rem; }
.italic { font-style: italic; }
.whitespace-nowrap { white-space: nowrap; }

.bg-surface-100 { background-color: var(--p-surface-100); }
.text-primary-600 { color: var(--p-primary-600); }
.text-surface-600 { color: var(--erp-text-main); }
.text-surface-400 { color: var(--erp-text-muted); }
</style>
