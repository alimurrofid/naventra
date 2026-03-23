<template>
  <div class="flex flex-col h-full gap-4">
    <BaseCard title="Examples" class="flex-1 flex flex-col min-h-0">
      <template #content>
        <!-- Top Toolbar -->
        <div class="flex justify-between items-center bg-white p-3 rounded-t-lg border border-surface-200 border-b-0">
          <!-- Left: Search and Action Buttons -->
          <div class="flex items-center gap-2">
            <span class="p-input-icon-left">
              <i class="pi pi-search" />
              <InputText 
                id="search-input"
                v-model="searchQuery" 
                placeholder="(Ctrl+F)" 
                class="p-inputtext-sm w-[200px]" 
              />
            </span>
            
            <!-- Contextual Actions -->
            <transition name="fade">
              <div v-if="selectedRecords.length > 0" class="flex items-center gap-1 ml-2">
                <Button icon="pi pi-trash" severity="danger" class="w-8 h-8 p-0" @click="confirmDelete" title="Delete" />
                <Button icon="pi pi-eye" severity="success" class="w-8 h-8 p-0" title="View" />
                <Button icon="pi pi-pencil" severity="info" class="w-8 h-8 p-0" title="Edit" />
                <Button icon="pi pi-copy" severity="secondary" class="w-8 h-8 p-0" title="Copy" />
              </div>
            </transition>
          </div>

          <!-- Right: CSV, Refresh, Dropdown, Create New -->
          <div class="flex items-center gap-2">
            <Button icon="pi pi-file-excel" severity="secondary" text class="w-8 h-8 p-0" title="Export CSV" />
            <Button 
              :icon="`pi pi-refresh ${exampleStore.loading ? 'pi-spin' : ''}`" 
              severity="secondary" 
              text 
              class="w-8 h-8 p-0" 
              @click="refreshData" 
              title="Refresh Data"
            />
            <Dropdown 
              v-model="limit" 
              :options="[20, 25, 50, 100, 250]" 
              class="p-dropdown-sm w-20 mx-2" 
            />
            <Button label="Create New" icon="pi pi-plus" severity="success" class="p-button-sm" @click="goToCreate" />
          </div>
        </div>

        <!-- DataTable with Infinite Scroll (via scroll observer) -->
        <div class="flex-1 min-h-0 border border-surface-200 rounded-b-lg overflow-hidden bg-white">
          <DataTable
            ref="tableRef"
            :value="displayedRecords"
            v-model:selection="selectedRecords"
            selectionMode="multiple"
            dataKey="id"
            scrollable
            scrollHeight="flex"
            class="p-datatable-sm h-full flex flex-col"
            :loading="exampleStore.loading && displayedRecords.length === 0"
          >
            <template #empty>
              <div class="p-4 text-center text-surface-500">
                Tidak ada data.
              </div>
            </template>

            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column header="No" style="width: 4rem">
              <template #body="{ index }">
                {{ index + 1 }}
              </template>
            </Column>
            <Column field="code" header="Kode" sortable></Column>
            <Column field="description" header="Nama / Description" sortable></Column>
            <Column field="transaction_date" header="Transaction Date" sortable></Column>
            <Column header="Status">
              <template #body>
                <span class="text-green-600 font-bold">Active</span>
              </template>
            </Column>
          </DataTable>
          
          <!-- Sentinel element for IntersectionObserver -->
          <div ref="loadMoreSentinel" class="h-4 w-full flex items-center justify-center p-4">
             <i v-if="exampleStore.loading && displayedRecords.length > 0" class="pi pi-spin pi-spinner text-primary-600"></i>
          </div>
        </div>
      </template>
    </BaseCard>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useExampleStore } from '../store/example.store';
import type { ExamplePayload } from '../types/example.type';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

import BaseCard from '@/core/components/BaseCard.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const router = useRouter();
const exampleStore = useExampleStore();
const confirm = useConfirm();
const toast = useToast();

const loadMoreSentinel = ref<HTMLElement | null>(null);
const searchQuery = ref('');
const limit = ref(20);
const loadedChunks = ref(1);
const selectedRecords = ref<ExamplePayload[]>([]);

// Data computation
const filteredRecords = computed(() => {
  let data = exampleStore.items;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    data = data.filter(item => 
      item.code.toLowerCase().includes(q) || 
      item.description.toLowerCase().includes(q)
    );
  }
  return data;
});

const displayedRecords = computed(() => {
  const maxItems = limit.value * loadedChunks.value;
  return filteredRecords.value.slice(0, maxItems);
});

// Reset chunks when search or limit changes
watch([searchQuery, limit], () => {
  loadedChunks.value = 1;
});

// Infinite Scroll with IntersectionObserver
let observer: IntersectionObserver | null = null;

const setupObserver = () => {
  if (observer) observer.disconnect();
  
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      if (!exampleStore.loading && displayedRecords.value.length < filteredRecords.value.length) {
        loadMore();
      }
    }
  }, {
    rootMargin: '100px', // Trigger slightly before it hits the viewport
    threshold: 0.1
  });

  if (loadMoreSentinel.value) {
    observer.observe(loadMoreSentinel.value);
  }
};

const loadMore = () => {
  exampleStore.loading = true;
  // Small delay to show spinner and avoid too fast jumps
  setTimeout(() => {
    loadedChunks.value++;
    exampleStore.loading = false;
  }, 400);
};

const refreshData = async () => {
  loadedChunks.value = 1;
  selectedRecords.value = [];
  await exampleStore.fetchExamples();
};

const handleGlobalKeyDown = (e: KeyboardEvent) => {
  if (e.ctrlKey && e.key === 'f') {
    e.preventDefault();
    const input = document.getElementById('search-input');
    if (input) {
      input.focus();
    }
  }
};

onMounted(async () => {
  await refreshData();
  window.addEventListener('keydown', handleGlobalKeyDown);
  setupObserver();
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleGlobalKeyDown);
  if (observer) observer.disconnect();
});

const goToCreate = () => {
  router.push('/setup/master/examples/create');
};

const confirmDelete = () => {
  if (selectedRecords.value.length === 0) return;
  confirm.require({
    message: `Apakah Anda yakin ingin menghapus ${selectedRecords.value.length} baris data terpilih?`,
    header: 'Konfirmasi Hapus Beberapa Data',
    icon: 'pi pi-exclamation-triangle',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        for (const item of selectedRecords.value) {
          if (item.id) await exampleStore.deleteExample(item.id);
        }
        toast.add({ severity: 'success', summary: 'Sukses', detail: 'Data terpilih terhapus!', life: 3000 });
        refreshData();
      } catch (err) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Ada item yang gagal terhapus', life: 3000 });
      }
    }
  });
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
