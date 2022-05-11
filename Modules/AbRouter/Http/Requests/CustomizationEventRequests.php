<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;

class CustomizationEventRequests extends FormRequest
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
                Rule::in('display_user_events')
            ],
            'data.attributes.event_name' => 'required|string',
            'data.relationships.user.data.id' => 'required|string',
            'data.relationships.user.data.type' => 'required|in:users',
        ];
    }
}
