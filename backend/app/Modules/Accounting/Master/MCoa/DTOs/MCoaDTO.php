<?php

namespace App\Modules\Accounting\Master\MCoa\DTOs;

use App\Common\DTOs\BaseDTO;
use Illuminate\Http\Request;

class MCoaDTO extends BaseDTO
{
    public function __construct(
        public readonly ?int    $id,
        public readonly string  $code,
        public readonly string  $name,
        public readonly ?int    $parent_id,
        public readonly int     $level,
        public readonly string  $type,
        public readonly bool    $is_active,
        public readonly ?string $description,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new static(
            id: $request->route('mcoa'),
            code: $request->input('code'),
            name: $request->input('name'),
            parent_id: $request->input('parent_id') ? (int) $request->input('parent_id') : null,
            level: (int) $request->input('level', 1),
            type: $request->input('type'),
            is_active: (bool) $request->input('is_active', true),
            description: $request->input('description'),
        );
    }

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            code: $data['code'],
            name: $data['name'],
            parent_id: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
            level: (int) ($data['level'] ?? 1),
            type: $data['type'],
            is_active: (bool) ($data['is_active'] ?? true),
            description: $data['description'] ?? null,
        );
    }
}
