import { useAuthStore } from '@/core/store/auth.store';

export function usePermission() {
  const authStore = useAuthStore();

  /**
   * Check if user has a specific permission
   */
  const can = (permission: string): boolean => {
    return authStore.hasPermission(permission);
  };

  /**
   * Check if user has AT LEAST ONE of the given permissions
   */
  const canAny = (permissions: string[]): boolean => {
    return permissions.some(permission => authStore.hasPermission(permission));
  };

  /**
   * Check if user has ALL of the given permissions
   */
  const canAll = (permissions: string[]): boolean => {
    return permissions.every(permission => authStore.hasPermission(permission));
  };

  return {
    can,
    canAny,
    canAll
  };
}
