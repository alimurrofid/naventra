import { usePermission } from './usePermission';

export interface WorkflowAction {
  key: string;
  label: string;
  permission?: string;
  statuses: string[];
  icon?: string;
  severity?: 'success' | 'info' | 'warn' | 'danger' | 'secondary' | 'contrast';
  isBulk?: boolean;
}

export function useWorkflow() {
  const { can } = usePermission();

  /**
   * Get available actions for a specific status and user permissions
   */
  const getAvailableActions = (currentStatus: string, actions: WorkflowAction[]): WorkflowAction[] => {
    return actions.filter(action => {
      // 1. Check if action supports current status
      const currentStatusStr = String(currentStatus || '').toUpperCase();
      const actionStatusesStr = action.statuses.map(s => String(s || '').toUpperCase());

      if (!actionStatusesStr.includes(currentStatusStr)) {
        return false;
      }

      // 2. Check if user has permission (if required)
      if (action.permission && !can(action.permission)) {
        return false;
      }

      return true;
    });
  };

  /**
   * Get available bulk actions for an array of statuses
   */
  const getAvailableBulkActions = (statuses: string[], actions: WorkflowAction[]): WorkflowAction[] => {
    // Bulk actions require all selected items to have the EXACT SAME status
    const uniqueStatuses = [...new Set(statuses)];
    
    if (uniqueStatuses.length !== 1) {
      return []; // Cannot do bulk action on mixed statuses
    }

    const currentStatus = uniqueStatuses[0];

    return actions.filter(action => {
      // Action must explicitly support bulk
      if (!action.isBulk) {
        return false;
      }
      
      // 1. Check if action supports current status
      const currentStatusStr = String(currentStatus || '').toUpperCase();
      const actionStatusesStr = action.statuses.map(s => String(s || '').toUpperCase());

      if (!actionStatusesStr.includes(currentStatusStr)) {
        return false;
      }

      // 2. Check if user has permission
      if (action.permission && !can(action.permission)) {
        return false;
      }

      return true;
    });
  };

  return {
    getAvailableActions,
    getAvailableBulkActions
  };
}
