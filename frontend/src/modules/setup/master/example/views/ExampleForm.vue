<template>
  <div class="flex flex-col gap-4">
    <BaseCard title="Form Example Transaction">
      <template #content>
        <form @submit.prevent="submitForm" class="flex flex-col gap-4">
          
          <!-- HEADER SECTION -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            <div class="flex flex-col gap-2">
              <label for="code">Kode <span class="text-red-500">*</span></label>
              <InputText id="code" v-model="header.code" placeholder="trx 1" required />
            </div>
            <div class="flex flex-col gap-2">
              <label for="desc">Nama / Description <span class="text-red-500">*</span></label>
              <InputText id="desc" v-model="header.description" placeholder="Deskripsi" required />
            </div>
            <div class="flex flex-col gap-2">
              <label for="status">Status</label>
              <InputText id="status" value="Active" disabled class="opacity-70" />
            </div>
          </div>

          <!-- DETAIL SECTION -->
          <div class="flex flex-col gap-2 mb-4">
            <div class="flex items-center gap-4 mb-2">
              <label class="font-semibold text-lg m-0">Detail Wilayah / Transaction Details</label>
              <Button type="button" label="+ Add To List" class="p-button-success p-button-sm" @click="showItemModal = true" />
            </div>
            
            <DataTable :value="header.details" class="p-datatable-sm p-datatable-gridlines">
              <template #empty>
                <div class="text-center p-4 text-surface-500 font-medium">No data to show</div>
              </template>
              <Column header="No" style="width: 4rem">
                <template #body="{ index }">
                  {{ index + 1 }}
                </template>
              </Column>
              <Column field="item_name" header="Item Name / Kota"></Column>
              <Column field="qty" header="Qty"></Column>
              <Column field="price" header="Price">
                <template #body="{ data }">
                  {{ formatIDR(data.price) }}
                </template>
              </Column>
              <Column header="Action" style="width: 5rem; text-align: center">
                <template #body="{ index }">
                  <Button icon="pi pi-trash" severity="danger" text @click="removeDetail(index)" />
                </template>
              </Column>
            </DataTable>
            <div class="text-right text-xs text-surface-500 italic">
              Tekan CTRL + S untuk shortcut Save Data
            </div>
          </div>

          <!-- FOOTER BUTTONS -->
          <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-surface-200">
            <Button type="button" label="Reset" icon="pi pi-times" severity="danger" @click="resetForm" />
            <Button type="submit" label="Simpan" icon="pi pi-save" severity="success" :loading="exampleStore.loading" :disabled="header.details.length === 0" />
          </div>
        </form>
      </template>
    </BaseCard>

    <!-- DIALOG POPUP (Mencari Data Detail) -->
    <Dialog v-model:visible="showItemModal" header="Pilih Data" :modal="true" :style="{ width: '60rem' }" :breakpoints="{ '960px': '75vw', '640px': '90vw' }">
      <div class="flex flex-col gap-4">
        <!-- Toolbar Modal -->
        <div class="flex justify-between items-center mb-2">
          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText v-model="searchItem" placeholder="(Ctrl+F)" class="p-inputtext-sm w-64" />
          </span>
          <div class="flex items-center gap-2">
            <Button icon="pi pi-refresh" text />
          </div>
        </div>

        <!-- DataTable Modal Multiple Selection -->
        <DataTable 
          :value="filteredAvailableItems" 
          v-model:selection="selectedItems" 
          selectionMode="multiple" 
          dataKey="kode"
          class="p-datatable-sm"
          :paginator="true"
          :rows="10"
        >
          <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
          <Column field="kode" header="Kode" sortable></Column>
          <Column field="nama" header="Nama" sortable></Column>
          <Column field="price" header="Price">
            <template #body="{ data }">
              {{ formatIDR(data.price) }}
            </template>
          </Column>
        </DataTable>

        <!-- Modal Actions -->
        <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-surface-200">
          <Button type="button" label="Reset" icon="pi pi-times" severity="danger" text @click="selectedItems = []" />
          <Button type="button" label="Simpan" icon="pi pi-check" severity="success" @click="confirmItemsSelection" />
        </div>
      </div>
    </Dialog>

  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useExampleStore } from '../store/example.store';
import type { ExamplePayload } from '../types/example.type';
import { useToast } from 'primevue/usetoast';

import BaseCard from '@/core/components/BaseCard.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';

const router = useRouter();
const toast = useToast();
const exampleStore = useExampleStore();

// Form State
const header = ref<ExamplePayload>({
  code: '',
  description: '',
  transaction_date: new Date().toISOString().split('T')[0],
  details: []
});

// Modal State
const showItemModal = ref(false);
const searchItem = ref('');
const selectedItems = ref<any[]>([]);

// Mockup Data untuk katalog master detail
const availableItemsList = [
  { kode: '00001', nama: 'Aceh Tengah', price: 10000 },
  { kode: '00002', nama: 'Agam', price: 15000 },
  { kode: '00003', nama: 'Asahan', price: 20000 },
  { kode: '00004', nama: 'Badung', price: 25000 },
  { kode: '00005', nama: 'Bali', price: 30000 },
  { kode: '00006', nama: 'Bandung', price: 35000 },
  { kode: '00007', nama: 'Banggai', price: 40000 },
  { kode: '00008', nama: 'Bangkalan', price: 45000 },
  { kode: '00009', nama: 'Bangli', price: 50000 },
  { kode: '00010', nama: 'Banjar', price: 55000 },
  { kode: '00011', nama: 'Banjarmasin', price: 60000 },
];

const filteredAvailableItems = computed(() => {
  if (!searchItem.value) return availableItemsList;
  const q = searchItem.value.toLowerCase();
  return availableItemsList.filter(i => 
    i.kode.toLowerCase().includes(q) || i.nama.toLowerCase().includes(q)
  );
});

// Aksi Modal
const confirmItemsSelection = () => {
  if (selectedItems.value.length === 0) {
    showItemModal.value = false;
    return;
  }

  // Push checked items into header.details
  selectedItems.value.forEach(item => {
    // Hindari duplikasi sederhana (Opsional)
    const exists = header.value.details.find(d => d.item_name === item.nama);
    if (!exists) {
      header.value.details.push({
        item_name: item.nama,
        qty: 1, // Default qty
        price: item.price
      });
    }
  });

  selectedItems.value = [];
  searchItem.value = '';
  showItemModal.value = false;
};

// Aksi Form Standar
const removeDetail = (index: number) => {
  header.value.details.splice(index, 1);
};

const resetForm = () => {
  header.value = {
    code: '',
    description: '',
    transaction_date: new Date().toISOString().split('T')[0],
    details: []
  };
  selectedItems.value = [];
};

const formatIDR = (val: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);
};

const submitForm = async () => {
  if (header.value.details.length === 0) {
    toast.add({ severity: 'error', summary: 'Validation Error', detail: 'Minimum 1 detail is required.', life: 3000 });
    return;
  }

  try {
    await exampleStore.storeExample(header.value);
    toast.add({ severity: 'success', summary: 'Success', detail: 'Data berhasil disimpan!', life: 3000 });
    router.push('/setup/master/examples');
  } catch (err: any) {
    toast.add({ 
      severity: 'error', 
      summary: 'Gagal', 
      detail: err.response?.data?.message || 'Terjadi kesalahan sistem', 
      life: 5000 
    });
  }
};
</script>
