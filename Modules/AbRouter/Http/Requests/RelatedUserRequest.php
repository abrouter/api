<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;

class RelatedUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.type' => [
                'required',
                Rule::in('related_users')
            ],
            'data.attributes.exist_user_id' => 'required|string',
            'data.attributes.user_id' => 'required|string',
            'data.relationships.owner.data.id' => 'required|string',
            'data.relationships.owner.data.type' => 'required|in:users',
        ];
    }
}
