<?php

namespace App\Modules\Accounting\Master\MCoa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMCoaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implement RBAC authorization
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code'        => ['required', 'string', 'max:20', 'unique:m_coa,code'],
            'name'        => ['required', 'string', 'max:255'],
            'parent_id'   => ['nullable', 'integer', 'exists:m_coa,id'],
            'level'       => ['required', 'integer', 'min:1', 'max:10'],
            'type'        => ['required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'is_active'   => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'code'      => 'account code',
            'name'      => 'account name',
            'parent_id' => 'parent account',
            'type'      => 'account type',
        ];
    }
}
