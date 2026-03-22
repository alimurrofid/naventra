import { defineStore } from 'pinia';
import { ref } from 'vue';
import { TKbkAPI } from '../api/tkbk.api';
import type { TKbk, TKbkFilters, TKbkPayload } from '../types/tkbk.type';

export const useTKbkStore = defineStore('tkbk', () => {
  const items = ref<TKbk[]>([]);
  const currentItem = ref<TKbk | null>(null);
  
  const loading = ref(false);
  const totalRecords = ref(0);
  const error = ref<string | null>(null);

  async function fetchAll(filters?: TKbkFilters, page = 1, perPage = 10) {
    loading.value = true;
    error.value = null;
    try {
      const response = await TKbkAPI.getAll(filters, page, perPage);
      items.value = response.data?.data || response.data || [];
      totalRecords.value = response.data?.total || response.meta?.total || items.value.length || 0;
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Failed to fetch Kas/Bank Keluar data';
    } finally {
      loading.value = false;
    }
  }

  async function fetchById(id: number) {
    loading.value = true;
    try {
      const response = await TKbkAPI.getById(id);
      currentItem.value = response.data;
      return response.data;
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Failed to fetch Kas/Bank Keluar details';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  async function create(payload: TKbkPayload) {
    loading.value = true;
    try {
      const response = await TKbkAPI.create(payload);
      return response.data;
    } catch (e: any) {
      throw new Error(e.response?.data?.message || 'Failed to create Kas/Bank Keluar');
    } finally {
      loading.value = false;
    }
  }

  async function changeStatus(id: number, status: string) {
    loading.value = true;
    try {
      await TKbkAPI.updateStatus(id, status);
    } catch (e: any) {
      throw new Error(e.response?.data?.message || `Failed to change status to ${status}`);
    } finally {
      loading.value = false;
    }
  }

  async function bulkChangeStatus(ids: number[], status: string) {
    loading.value = true;
    try {
      await TKbkAPI.bulkUpdateStatus(ids, status);
    } catch (e: any) {
      throw new Error(e.response?.data?.message || `Failed to bulk change status to ${status}`);
    } finally {
      loading.value = false;
    }
  }

  async function remove(id: number) {
    loading.value = true;
    try {
      await TKbkAPI.delete(id);
    } catch (e: any) {
      throw new Error(e.response?.data?.message || 'Failed to delete Kas/Bank Keluar');
    } finally {
      loading.value = false;
    }
  }

  return {
    items,
    currentItem,
    loading,
    totalRecords,
    error,
    fetchAll,
    fetchById,
    create,
    changeStatus,
    bulkChangeStatus,
    remove
  };
});
