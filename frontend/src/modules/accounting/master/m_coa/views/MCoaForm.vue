<template>
  <form @submit.prevent="save" class="flex flex-col gap-4">
    <Message v-if="error" severity="error" :closable="false">{{ error }}</Message>

    <div class="flex flex-col gap-2">
      <label for="code">Account Code <span class="text-red-500">*</span></label>
      <InputText id="code" v-model="form.code" required autofocus />
    </div>

    <div class="flex flex-col gap-2">
      <label for="name">Account Name <span class="text-red-500">*</span></label>
      <InputText id="name" v-model="form.name" required />
    </div>

    <div class="flex flex-col gap-2">
      <label for="type">Account Type</label>
      <Dropdown id="type" v-model="form.type" :options="typeOptions" optionLabel="label" optionValue="value" />
    </div>

    <div class="flex flex-col gap-2">
      <label for="balance_type">Normal Balance</label>
      <Dropdown id="balance_type" v-model="form.balance_type" :options="balanceOptions" optionLabel="label" optionValue="value" />
    </div>

    <div class="flex flex-col gap-2">
      <label for="parent_id">Parent Account</label>
      <Dropdown 
        id="parent_id" 
        v-model="form.parent_id" 
        :options="parentOptions" 
        optionLabel="name" 
        optionValue="id" 
        placeholder="Select Parent Account" 
        showClear
        filter
      >
        <template #option="slotProps">
          <div :style="{'padding-left': slotProps.option.depth * 10 + 'px'}">
            {{ slotProps.option.code }} - {{ slotProps.option.name }}
          </div>
        </template>
      </Dropdown>
    </div>

    <div class="flex items-center gap-2 mt-2">
      <Checkbox v-model="form.is_active" inputId="is_active" :binary="true" />
      <label for="is_active">Active</label>
    </div>

    <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-surface-200 dark:border-surface-800">
      <Button label="Cancel" icon="pi pi-times" text @click="$emit('cancel')" />
      <Button type="submit" label="Save" icon="pi pi-check" :loading="loading" />
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useMCoaStore } from '../store/mcoa.store';
import type { MCoaPayload } from '../types/mcoa.type';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import Message from 'primevue/message';

interface Props {
  id?: number | null;
}

const props = defineProps<Props>();
const emit = defineEmits(['saved', 'cancel']);

const mcoaStore = useMCoaStore();
const loading = ref(false);
const error = ref<string | null>(null);

const form = ref<MCoaPayload>({
  code: '',
  name: '',
  type: 'DETAIL',
  balance_type: 'DEBIT',
  is_active: true,
  parent_id: null
});

const typeOptions = [
  { label: 'Header (Group)', value: 'HEADER' },
  { label: 'Detail (Transaction)', value: 'DETAIL' }
];

const balanceOptions = [
  { label: 'Debit', value: 'DEBIT' },
  { label: 'Credit', value: 'CREDIT' }
];

const parentOptions = ref<any[]>([]);

onMounted(async () => {
  try {
    // Load parent options from tree
    await mcoaStore.fetchTree();
    
    // Flatten tree to linear list for Dropdown
    const flatten = (items: any[], depth = 0): any[] => {
      let result: any[] = [];
      items.forEach(item => {
        result.push({ ...item, depth });
        if (item.children && item.children.length > 0) {
          result = result.concat(flatten(item.children, depth + 1));
        }
      });
      return result;
    };
    
    // Only allow HEADERs as parents
    parentOptions.value = flatten(mcoaStore.treeItems).filter(item => item.type === 'HEADER' && item.id !== props.id);

    // If edit mode, load details
    if (props.id) {
      const data = await mcoaStore.fetchById(props.id);
      form.value = {
        code: data.code,
        name: data.name,
        type: data.type,
        balance_type: data.balance_type,
        is_active: data.is_active,
        parent_id: data.parent_id
      };
    }
  } catch (e: any) {
    error.value = e.message || 'Error loading form data';
  }
});

const save = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    if (props.id) {
      await mcoaStore.update(props.id, form.value);
    } else {
      await mcoaStore.create(form.value);
    }
    emit('saved');
  } catch (e: any) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.flex { display: flex; }
.flex-col { flex-direction: column; }
.justify-end { justify-content: flex-end; }
.items-center { align-items: center; }
.gap-2 { gap: 0.5rem; }
.gap-4 { gap: 1rem; }
.mt-2 { margin-top: 0.5rem; }
.mt-4 { margin-top: 1rem; }
.pt-4 { padding-top: 1rem; }
.text-red-500 { color: #ef4444; }
.border-t { border-top-width: 1px; border-top-style: solid; }
.border-surface-200 { border-color: var(--p-surface-200); }

:root[class="p-dark"] .dark\:border-surface-800 { border-color: var(--p-surface-800); }
</style>
