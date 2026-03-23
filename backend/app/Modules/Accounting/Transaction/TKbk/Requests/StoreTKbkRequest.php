<?php

namespace App\Modules\Accounting\Transaction\TKbk\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTKbkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('accounting.t_kbk.create') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'transaction_date'      => ['required', 'date'],
            'transaction_type'      => ['required', 'string', 'in:cash_in,cash_out,bank_in,bank_out'],
            'description'           => ['nullable', 'string', 'max:500'],
            'total_amount'          => ['required', 'numeric', 'min:0'],
            'details'               => ['required', 'array', 'min:1'],
            'details.*.coa_id'      => ['required', 'integer', 'exists:m_coa,id'],
            'details.*.description' => ['nullable', 'string', 'max:500'],
            'details.*.debit'       => ['required', 'numeric', 'min:0'],
            'details.*.credit'      => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'details.min'                => 'At least one transaction detail is required.',
            'details.*.coa_id.exists'    => 'Selected account (:input) does not exist.',
            'details.*.coa_id.required'  => 'Chart of Account is required for each detail line.',
        ];
    }
}
