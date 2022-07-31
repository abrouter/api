<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;
use Modules\Core\Rules\SumPercent;

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
            'data.attributes.name' => 'required|string',
            'data.attributes.alias' => 'required|string',
            'included' => ['array', 'size:2', new SumPercent],
            'included.*.type' => ['required', Rule::in('experiment_branches')],
            'included.*.attributes.name' => 'required|string|distinct',
            'included.*.attributes.percent' => 'required|integer|between:0,100',
            'included.*.attributes.uid' => 'required|string|distinct',
            'included.0.attributes.percent' => 'required|different:included.1.attributes.percent|in: 0, 100',
            'included.1.attributes.percent' => 'required|different:included.0.attributes.percent|in: 0, 100'
        ];
    }
}
