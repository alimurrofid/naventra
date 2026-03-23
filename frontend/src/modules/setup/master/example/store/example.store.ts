import { defineStore } from 'pinia';
import { api } from '@/core/api/axios';
import type { ExamplePayload } from '../types/example.type';

export const useExampleStore = defineStore('example', {
  state: () => ({
    items: [] as ExamplePayload[],
    loading: false,
  }),
  actions: {
    async fetchExamples() {
      this.loading = true;
      try {
        const { data } = await api.get('/setup/master/examples');
        this.items = data.data;
        return data.data;
      } finally {
        this.loading = false;
      }
    },
    async storeExample(payload: ExamplePayload) {
      this.loading = true;
      try {
        const { data } = await api.post('/setup/master/examples', payload);
        return data;
      } finally {
        this.loading = false;
      }
    },
    async deleteExample(id: number) {
      this.loading = true;
      try {
        await api.delete(`/setup/master/examples/${id}`);
      } finally {
        this.loading = false;
      }
    }
  }
});
