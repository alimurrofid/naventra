<?php

namespace App\Modules\Setup\Master\Example\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sesuaikan validasi permission jika ada
        return $this->user()->can('setup.master.example.create');
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'unique:examples,code'],
            'description' => ['required', 'string', 'max:500'],
            'transaction_date' => ['required', 'date'],
            
            // Nested validasi untuk details minimal 1 array item
            'details' => ['required', 'array', 'min:1'],
            'details.*.item_name' => ['required', 'string', 'max:255'],
            'details.*.qty' => ['required', 'integer', 'min:1'],
            'details.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
