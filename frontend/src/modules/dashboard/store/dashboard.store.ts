import { defineStore } from 'pinia';
import { ref } from 'vue';
import { DashboardAPI } from '../api/dashboard.api';
import type { DashboardData } from '../types/dashboard.type';

export const useDashboardStore = defineStore('dashboard', () => {
  const data = ref<DashboardData | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function fetchDashboard() {
    loading.value = true;
    error.value = null;
    try {
      data.value = await DashboardAPI.getOverview();
    } catch (e: any) {
      error.value = e.message || 'Failed to fetch dashboard data';
      console.error(e);
    } finally {
      loading.value = false;
    }
  }

  return {
    data,
    loading,
    error,
    fetchDashboard
  };
});
