<template>
  <div class="flex flex-col gap-4">
    <BaseCard :title="id ? `View Kas/Bank Keluar` : `Create Kas/Bank Keluar`">
      <template #content>
        <Message v-if="error" severity="error" class="mb-4" :closable="false">{{ error }}</Message>

        <form @submit.prevent="save" class="flex flex-col gap-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left Col Header -->
            <div class="flex flex-col gap-4">
              <div class="flex flex-col gap-2">
                <label for="transaction_date" class="font-medium">Date <span class="text-red-500">*</span></label>
                <input 
                  type="date" 
                  id="transaction_date" 
                  v-model="form.transaction_date" 
                  class="p-inputtext p-component" 
                  required 
                  :disabled="isReadOnly"
                />
              </div>

              <div class="flex flex-col gap-2">
                <label for="transaction_type" class="font-medium">Type <span class="text-red-500">*</span></label>
                <Dropdown 
                  id="transaction_type" 
                  v-model="form.transaction_type" 
                  :options="typeOptions" 
                  optionLabel="label" 
                  optionValue="value" 
                  :disabled="isReadOnly" 
                />
              </div>
            </div>

            <!-- Right Col Header -->
            <div class="flex flex-col gap-4">
              <div class="flex flex-col gap-2">
                <label for="description" class="font-medium">Description</label>
                <Textarea 
                  id="description" 
                  v-model="form.description" 
                  rows="4" 
                  :disabled="isReadOnly" 
                />
              </div>
            </div>
          </div>

          <Divider />

          <!-- Details Lines section -->
          <div>
            <div class="flex justify-between items-center mb-4">
              <h3 class="m-0 text-lg font-semibold text-surface-900 dark:text-surface-0">Transaction Details</h3>
              <Button v-if="!isReadOnly" label="Add Line" icon="pi pi-plus" size="small" outlined @click="addLine" />
            </div>

            <div class="overflow-x-auto w-full border border-surface-200 dark:border-surface-800 rounded-lg">
              <table class="w-full text-left border-collapse">
                <thead class="bg-surface-50 dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800">
                  <tr>
                    <th class="p-3 font-semibold w-1/3">Account (COA)</th>
                    <th class="p-3 font-semibold">Description</th>
                    <th class="p-3 font-semibold text-right w-48">Debit</th>
                    <th class="p-3 font-semibold text-right w-48">Credit</th>
                    <th v-if="!isReadOnly" class="p-3 font-semibold text-center w-16">Act</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="form.details.length === 0">
                    <td :colspan="isReadOnly ? 4 : 5" class="p-4 text-center text-surface-500 italic">No details added yet.</td>
                  </tr>
                  <tr v-for="(line, index) in form.details" :key="index" class="border-b border-surface-200 dark:border-surface-800">
                    <td class="p-2">
                      <Dropdown 
                        v-model="line.m_coa_id" 
                        :options="coaOptions" 
                        optionLabel="name" 
                        optionValue="id" 
                        filter 
                        placeholder="Select COA" 
                        class="w-full"
                        :disabled="isReadOnly"
                      >
                       <template #value="slotProps">
                          <div v-if="slotProps.value">
                             {{ getCoaLabel(slotProps.value) }}
                          </div>
                          <span v-else>
                              {{ slotProps.placeholder }}
                          </span>
                       </template>
                       <template #option="slotProps">
                          <div>{{ slotProps.option.code }} - {{ slotProps.option.name }}</div>
                       </template>
                      </Dropdown>
                    </td>
                    <td class="p-2">
                      <InputText v-model="line.description" class="w-full" :disabled="isReadOnly" />
                    </td>
                    <td class="p-2">
                      <InputNumber v-model="line.debit" class="w-full" mode="currency" currency="IDR" locale="id-ID" :disabled="isReadOnly" @input="line.credit = 0" />
                    </td>
                    <td class="p-2">
                      <InputNumber v-model="line.credit" class="w-full" mode="currency" currency="IDR" locale="id-ID" :disabled="isReadOnly" @input="line.debit = 0" />
                    </td>
                    <td v-if="!isReadOnly" class="p-2 text-center">
                      <Button icon="pi pi-times" severity="danger" text rounded @click="removeLine(Number(index))" />
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-surface-50 dark:bg-surface-900 font-semibold">
                  <tr>
                    <td colspan="2" class="p-3 text-right">Totals:</td>
                    <td class="p-3 text-right" :class="{ 'text-red-500': totalDebit !== totalCredit }">
                      {{ formatCurrency(totalDebit) }}
                    </td>
                    <td class="p-3 text-right" :class="{ 'text-red-500': totalDebit !== totalCredit }">
                      {{ formatCurrency(totalCredit) }}
                    </td>
                    <td v-if="!isReadOnly"></td>
                  </tr>
                </tfoot>
              </table>
              <div v-if="totalDebit !== totalCredit && !isReadOnly" class="p-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-sm text-center font-medium">
                Warning: Total Debit and Credit must be balanced before saving.
              </div>
            </div>
          </div>

          <div class="flex justify-between items-center mt-4">
            <Button label="Back" icon="pi pi-arrow-left" text @click="$router.push('/accounting/t_kbk')" />
            <Button 
              v-if="!isReadOnly" 
              type="submit" 
              label="Save Transaction" 
              icon="pi pi-save" 
              :loading="loading" 
              :disabled="totalDebit !== totalCredit || totalDebit === 0" 
            />
          </div>
        </form>
      </template>
    </BaseCard>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useTKbkStore } from '../store/tkbk.store';
import { useMCoaStore } from '../../../master/m_coa/store/mcoa.store';
import BaseCard from '@/core/components/BaseCard.vue';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import Message from 'primevue/message';
import Divider from 'primevue/divider';

const router = useRouter();
const route = useRoute();
const tkbkStore = useTKbkStore();
const mcoaStore = useMCoaStore();

const id = route.params.id ? Number(route.params.id) : null;
const isReadOnly = ref(!!id); // If ID exists, it's view mode. To edit, we would need a separate flow/permission

const loading = ref(false);
const error = ref<string | null>(null);

const form = ref<{
  transaction_date: string;
  transaction_type: string;
  description: string | null;
  details: any[];
}>({
  transaction_date: new Date().toISOString().split('T')[0],
  transaction_type: 'PAYMENT',
  description: '',
  details: []
});

const typeOptions = [
  { label: 'Payment', value: 'PAYMENT' },
  { label: 'Receipt', value: 'RECEIPT' },
  { label: 'Transfer', value: 'TRANSFER' }
];

const coaOptions = ref<any[]>([]);

const addLine = () => {
  form.value.details.push({
    m_coa_id: null,
    description: '',
    debit: 0,
    credit: 0
  });
};

const removeLine = (index: number) => {
  form.value.details.splice(index, 1);
};

const totalDebit = computed(() => {
  return form.value.details.reduce((sum: number, line: any) => sum + (line.debit || 0), 0);
});

const totalCredit = computed(() => {
  return form.value.details.reduce((sum: number, line: any) => sum + (line.credit || 0), 0);
});

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val || 0);
};

const getCoaLabel = (id: number) => {
  const coa = coaOptions.value.find(c => c.id === id);
  return coa ? `${coa.code} - ${coa.name}` : '';
};

onMounted(async () => {
  try {
    loading.value = true;
    
    // Load COAs for dropdown (only detail accounts are selectable)
    await mcoaStore.fetchAll({ is_active: true, type: 'DETAIL' }, 1, 1000); // 1000 to get all without pagination for dropdown
    coaOptions.value = mcoaStore.items;

    if (id) {
      const data = await tkbkStore.fetchById(id);
      form.value = {
        transaction_date: data.transaction_date.split('T')[0], // strip timestamp if present
        transaction_type: data.transaction_type,
        description: data.description,
        details: data.details || []
      };
    } else {
       // Add empty initial line
       addLine();
    }
  } catch (e: any) {
    error.value = e.message || 'Error loading data';
  } finally {
    loading.value = false;
  }
});

const save = async () => {
  if (totalDebit.value !== totalCredit.value) {
    error.value = 'Debit and Credit totals must be balanced.';
    return;
  }
  if (totalDebit.value === 0) {
    error.value = 'Transaction cannot have zero amount.';
    return;
  }

  loading.value = true;
  error.value = null;
  
  try {
    // Only Create is supported for transactions to prevent auditing nightmares
    await tkbkStore.create(form.value);
    router.push('/accounting/t_kbk');
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
.justify-between { justify-content: space-between; }
.items-center { align-items: center; }
.gap-2 { gap: 0.5rem; }
.gap-4 { gap: 1rem; }
.gap-6 { gap: 1.5rem; }
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.mt-4 { margin-top: 1rem; }
.p-2 { padding: 0.5rem; }
.p-3 { padding: 0.75rem; }
.p-4 { padding: 1rem; }
.m-0 { margin: 0; }
.mb-4 { margin-bottom: 1rem; }
.w-full { width: 100%; }
.w-1\/3 { width: 33.333333%; }
.w-48 { width: 12rem; }
.w-16 { width: 4rem; }
.text-left { text-align: left; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.text-lg { font-size: 1.125rem; line-height: 1.75rem; }
.text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.italic { font-style: italic; }
.overflow-x-auto { overflow-x: auto; }
.border-collapse { border-collapse: collapse; }
.rounded-lg { border-radius: 0.5rem; }
.border { border-width: 1px; border-style: solid; }
.border-b { border-bottom-width: 1px; border-bottom-style: solid; }

.text-red-500 { color: #ef4444; }
.text-red-600 { color: #dc2626; }
.bg-red-50 { background-color: #fef2f2; }

.bg-surface-50 { background-color: var(--p-surface-50); }
.text-surface-500 { color: var(--p-surface-500); }
.border-surface-200 { border-color: var(--p-surface-200); }
.text-surface-900 { color: var(--p-surface-900); }

@media (min-width: 768px) {
  .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

:root[class="p-dark"] .dark\:border-surface-800 { border-color: var(--p-surface-800); }
:root[class="p-dark"] .dark\:bg-surface-900 { background-color: var(--p-surface-900); }
:root[class="p-dark"] .dark\:text-surface-0 { color: var(--p-surface-0); }
:root[class="p-dark"] .dark\:text-red-400 { color: #f87171; }
:root[class="p-dark"] .dark\:bg-red-900\/20 { background-color: rgba(127, 29, 29, 0.2); }
</style>
