import { defineStore } from 'pinia';
import { ref } from 'vue';
import { MCoaAPI } from '../api/mcoa.api';
import type { MCoa, MCoaFilters, MCoaPayload } from '../types/mcoa.type';

export const useMCoaStore = defineStore('mcoa', () => {
  const items = ref<MCoa[]>([]);
  const treeItems = ref<MCoa[]>([]);
  const currentItem = ref<MCoa | null>(null);
  
  const loading = ref(false);
  const totalRecords = ref(0);
  const error = ref<string | null>(null);

  async function fetchAll(filters?: MCoaFilters, page = 1, perPage = 10) {
    loading.value = true;
    error.value = null;
    try {
      const response = await MCoaAPI.getAll(filters, page, perPage);
      items.value = response.data?.data || response.data || [];
      totalRecords.value = response.data?.total || response.meta?.total || items.value.length || 0;
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Failed to fetch COA data';
    } finally {
      loading.value = false;
    }
  }

  async function fetchTree() {
    loading.value = true;
    try {
      const response = await MCoaAPI.getTree();
      treeItems.value = response.data;
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Failed to fetch COA tree';
    } finally {
      loading.value = false;
    }
  }

  async function fetchById(id: number) {
    loading.value = true;
    try {
      const response = await MCoaAPI.getById(id);
      currentItem.value = response.data;
      return response.data;
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Failed to fetch COA details';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  async function create(payload: MCoaPayload) {
    loading.value = true;
    try {
      const response = await MCoaAPI.create(payload);
      return response.data;
    } catch (e: any) {
      throw new Error(e.response?.data?.message || 'Failed to create COA');
    } finally {
      loading.value = false;
    }
  }

  async function update(id: number, payload: MCoaPayload) {
    loading.value = true;
    try {
      const response = await MCoaAPI.update(id, payload);
      return response.data;
    } catch (e: any) {
      throw new Error(e.response?.data?.message || 'Failed to update COA');
    } finally {
      loading.value = false;
    }
  }

  async function remove(id: number) {
    loading.value = true;
    try {
      await MCoaAPI.delete(id);
    } catch (e: any) {
      throw new Error(e.response?.data?.message || 'Failed to delete COA');
    } finally {
      loading.value = false;
    }
  }

  return {
    items,
    treeItems,
    currentItem,
    loading,
    totalRecords,
    error,
    fetchAll,
    fetchTree,
    fetchById,
    create,
    update,
    remove
  };
});
