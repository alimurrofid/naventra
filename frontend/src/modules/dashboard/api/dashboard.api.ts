import { api } from '@/core/api';
import type { DashboardData } from '../types/dashboard.type';

export const DashboardAPI = {
  /**
   * Fetch complete dashboard overview data
   */
  getOverview: async (): Promise<DashboardData> => {
    const { data } = await api.get('/dashboard');
    return data;
  }
};
