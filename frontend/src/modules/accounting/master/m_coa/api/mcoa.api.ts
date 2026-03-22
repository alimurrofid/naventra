import { api } from '@/core/api';
import type { MCoa, MCoaPayload, MCoaFilters } from '../types/mcoa.type';

export const MCoaAPI = {
  getAll: async (filters?: MCoaFilters, page = 1, perPage = 10) => {
    const params = { ...filters, page, per_page: perPage };
    const { data } = await api.get('/accounting/master/mcoa', { params });
    return data;
  },

  getTree: async () => {
    const { data } = await api.get('/accounting/master/mcoa/tree');
    return data;
  },

  getById: async (id: number): Promise<{ data: MCoa }> => {
    const { data } = await api.get(`/accounting/master/mcoa/${id}`);
    return data;
  },

  create: async (payload: MCoaPayload): Promise<{ data: MCoa }> => {
    const { data } = await api.post('/accounting/master/mcoa', payload);
    return data;
  },

  update: async (id: number, payload: MCoaPayload): Promise<{ data: MCoa }> => {
    const { data } = await api.put(`/accounting/master/mcoa/${id}`, payload);
    return data;
  },

  delete: async (id: number) => {
    const { data } = await api.delete(`/accounting/master/mcoa/${id}`);
    return data;
  }
};
