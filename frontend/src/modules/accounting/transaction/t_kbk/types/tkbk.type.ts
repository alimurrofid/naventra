import type { MCoa } from '../../../master/m_coa/types/mcoa.type';

export interface TKbkDetail {
  id?: number;
  t_kbk_id?: number;
  m_coa_id: number;
  m_coa?: MCoa;
  description: string | null;
  debit: number;
  credit: number;
}

export interface TKbk {
  id: number;
  transaction_number: string;
  transaction_date: string;
  transaction_type: string;
  description: string | null;
  total_amount: number;
  status: 'DRAFT' | 'POST' | 'APPROVAL' | 'APPROVED' | 'REJECTED';
  created_by?: number;
  created_at: string;
  updated_at: string;
  details?: TKbkDetail[];
}

export interface TKbkPayload {
  transaction_date: string;
  transaction_type: string;
  description: string | null;
  details: {
    m_coa_id: number;
    description: string | null;
    debit: number;
    credit: number;
  }[];
}

export interface TKbkFilters {
  search?: string;
  start_date?: string;
  end_date?: string;
  status?: string;
}
