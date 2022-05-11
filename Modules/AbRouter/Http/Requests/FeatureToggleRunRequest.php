<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;

class FeatureToggleRunRequest extends FormRequest
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
                Rule::in('feature-toggles-run')
            ],
            'data.relationships.feature-toggle.data.id' => 'required|string',
            'data.relationships.feature-toggle.data.type' => 'required|in:feature-toggles',
        ];
    }
}
