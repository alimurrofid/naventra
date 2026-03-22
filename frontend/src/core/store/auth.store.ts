import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { api } from '@/core/api';

export interface User {
  id: number;
  name: string;
  email: string;
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(localStorage.getItem('token'));
  const roles = ref<string[]>([]);
  const permissions = ref<string[]>([]);
  const isAuthenticated = computed(() => !!token.value && !!user.value);

  function setToken(newToken: string) {
    token.value = newToken;
    localStorage.setItem('token', newToken);
  }

  function clearAuth() {
    user.value = null;
    token.value = null;
    roles.value = [];
    permissions.value = [];
    localStorage.removeItem('token');
    localStorage.removeItem('refresh_token');
  }

  function hasPermission(permission: string): boolean {
    return permissions.value.includes(permission);
  }

  async function fetchMe() {
    try {
      if (!token.value) return false;
      // Using mock endpoint logic - adjust when real backend is ready
      const { data } = await api.get('/me');
      user.value = data.user;
      roles.value = data.roles;
      permissions.value = data.permissions;
      return true;
    } catch (e) {
      clearAuth();
      return false;
    }
  }

  async function login(credentials: any) {
    const { data } = await api.post('/login', credentials);
    if (!data.access_token) {
      throw new Error('No access token received');
    }
    setToken(data.access_token);
    if (data.refresh_token) {
      localStorage.setItem('refresh_token', data.refresh_token);
    }
    await fetchMe();
  }

  function logout() {
    clearAuth();
  }

  return {
    user,
    token,
    roles,
    permissions,
    isAuthenticated,
    login,
    logout,
    fetchMe,
    hasPermission,
    setToken
  };
});
