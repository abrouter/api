<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;

class FeatureToggleCreateRequest extends FormRequest
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
                Rule::in('feature-toggles')
            ],
            'included.0.attributes.percent' => 'required|different:included.1.attributes.percent|in: 0, 100',
            'included.1.attributes.percent' => 'required|different:included.0.attributes.percent|in: 0, 100'
        ];
    }
}
