import type { RouteLocationNormalized } from 'vue-router';
import { useAuthStore } from './../store/auth.store';

export async function permissionGuard(
  to: RouteLocationNormalized,
  _from: RouteLocationNormalized
) {
  const authStore = useAuthStore();
  
  // Public routes
  if (to.meta.public) {
    return true;
  }

  // Ensure user is loaded if token exists
  if (authStore.token && !authStore.isAuthenticated) {
    const success = await authStore.fetchMe();
    if (!success) {
      return '/login';
    }
  }

  // Check auth
  if (!authStore.isAuthenticated && to.path !== '/login') {
    return '/login';
  }

  // Check permissions config
  if (to.meta.permission) {
    const requiredPermission = to.meta.permission as string;
    if (!authStore.hasPermission(requiredPermission)) {
      return '/403'; // or redirect to dashboard with error
    }
  }

  return true;
}
