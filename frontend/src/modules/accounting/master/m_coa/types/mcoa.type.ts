export interface MCoa {
  id: number;
  code: string;
  name: string;
  type: string;
  balance_type: 'DEBIT' | 'CREDIT';
  is_active: boolean;
  parent_id: number | null;
  created_at: string;
  updated_at: string;
  children?: MCoa[]; // For tree view
}

export interface MCoaPayload {
  code: string;
  name: string;
  type: string;
  balance_type: 'DEBIT' | 'CREDIT';
  is_active: boolean;
  parent_id: number | null;
}

export interface MCoaFilters {
  search?: string;
  type?: string;
  is_active?: boolean;
}
