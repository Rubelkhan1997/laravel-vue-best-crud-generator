/**
 * Payment Types
 */

export type PaymentMethod = 'cash' | 'card' | 'mobile_banking' | 'bank_transfer' | 'online';
export type PaymentStatus = 'pending' | 'completed' | 'failed' | 'refunded' | 'cancelled';

export interface Payment {
    id: number;
    reference: string;
    amount: number;
    method: PaymentMethod;
    status: PaymentStatus;
    transaction_id?: string;
    payment_date: string;
    notes?: string;
    created_at?: string;
    updated_at?: string;
}

export interface PaymentTransaction {
    id: number;
    payment_id: number;
    type: 'charge' | 'refund' | 'adjustment';
    amount: number;
    balance_after: number;
    description?: string;
    created_at: string;
}

export interface Invoice {
    id: number;
    invoice_number: string;
    reservation_id?: number;
    guest_id: number;
    subtotal: number;
    tax: number;
    discount: number;
    total: number;
    paid: number;
    due: number;
    status: 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled';
    issue_date: string;
    due_date: string;
    notes?: string;
    created_at?: string;
    updated_at?: string;
}

export interface InvoiceItem {
    id: number;
    invoice_id: number;
    description: string;
    quantity: number;
    unit_price: number;
    total: number;
    type: 'room_charge' | 'service' | 'product' | 'other';
}
