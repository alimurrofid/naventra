<?php

namespace App\Modules\Accounting\Master\MCoa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMCoaRequest extends FormRequest
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
        $id = $this->route('mcoa');

        return [
            'code'        => ['sometimes', 'required', 'string', 'max:20', "unique:m_coa,code,{$id}"],
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'parent_id'   => ['nullable', 'integer', 'exists:m_coa,id'],
            'level'       => ['sometimes', 'required', 'integer', 'min:1', 'max:10'],
            'type'        => ['sometimes', 'required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'is_active'   => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
