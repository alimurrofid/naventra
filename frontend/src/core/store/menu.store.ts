import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '@/core/api';
import { useAuthStore } from './auth.store';

export interface MenuItem {
  label: string;
  icon?: string;
  route?: string;
  permission?: string;
  children?: MenuItem[];
}

export const useMenuStore = defineStore('menu', () => {
  const menuItems = ref<MenuItem[]>([]);
  const filteredMenu = ref<MenuItem[]>([]);
  const authStore = useAuthStore();

  async function fetchMenu() {
    try {
      const { data } = await api.get('/menu');
      menuItems.value = data;
      filterMenu();
    } catch (e) {
      console.error('Failed to fetch menu', e);
    }
  }

  function filterMenu() {
    const filterItems = (items: MenuItem[]): MenuItem[] => {
      return items.reduce((acc: MenuItem[], item) => {
        // If it requires permission and user doesn't have it, skip entirely
        if (item.permission && !authStore.hasPermission(item.permission)) {
          return acc;
        }

        const newItem = { ...item };

        if (newItem.children) {
          newItem.children = filterItems(newItem.children);
          // If it had children but all were filtered out, skip the parent too unless it has its own route
          if (newItem.children.length === 0 && !newItem.route) {
            return acc;
          }
        }

        acc.push(newItem);
        return acc;
      }, []);
    };

    filteredMenu.value = filterItems(menuItems.value);
  }

  return {
    menuItems,
    filteredMenu,
    fetchMenu,
    filterMenu
  };
});
