import { api } from '@/core/api';
import type { TKbk, TKbkPayload, TKbkFilters } from '../types/tkbk.type';

export const TKbkAPI = {
  getAll: async (filters?: TKbkFilters, page = 1, perPage = 10) => {
    const params = { ...filters, page, per_page: perPage };
    const { data } = await api.get('/accounting/transaction/tkbk', { params });
    return data;
  },

  getById: async (id: number): Promise<{ data: TKbk }> => {
    const { data } = await api.get(`/accounting/transaction/tkbk/${id}`);
    return data;
  },

  create: async (payload: TKbkPayload): Promise<{ data: TKbk }> => {
    const { data } = await api.post('/accounting/transaction/tkbk', payload);
    return data;
  },

  updateStatus: async (id: number, status: string): Promise<{ data: TKbk }> => {
    const { data } = await api.patch(`/accounting/transaction/tkbk/${id}/status`, { status });
    return data;
  },

  bulkUpdateStatus: async (ids: number[], status: string) => {
    const { data } = await api.post('/accounting/transaction/tkbk/bulk-status', { ids, status });
    return data;
  },

  delete: async (id: number) => {
    const { data } = await api.delete(`/accounting/transaction/tkbk/${id}`);
    return data;
  }
};
