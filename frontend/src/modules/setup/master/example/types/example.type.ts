export interface ExampleDetailPayload {
  item_name: string;
  qty: number;
  price: number;
}

export interface ExamplePayload {
  id?: number;
  code: string;
  description: string;
  transaction_date: string;
  details: ExampleDetailPayload[];
}
