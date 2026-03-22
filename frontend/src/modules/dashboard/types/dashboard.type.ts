export interface DashboardStats {
  revenue: number;
  transactions: number;
  inventory: number;
  receivables: number;
}

export interface ChartData {
  labels: string[];
  datasets: {
    label: string;
    data: number[];
    backgroundColor?: string | string[];
    borderColor?: string | string[];
    borderWidth?: number;
    tension?: number;
    fill?: boolean;
  }[];
}

export interface DashboardCharts {
  revenue: ChartData;
}

export interface RecentTransaction {
  id: number;
  transaction_number: string;
  transaction_date: string;
  total_amount: number;
  status: string;
  description: string;
}

export interface DashboardData {
  stats: DashboardStats;
  charts: DashboardCharts;
  recent_transactions: RecentTransaction[];
}
