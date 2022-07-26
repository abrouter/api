<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;
use Modules\Core\Rules\SumPercent;

class ExperimentRequest extends FormRequest
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
                Rule::in('experiments')
            ],
            'data.attributes.name' => 'required|string',
            'data.attributes.alias' => 'required|string',
            'included' => ['array', 'min:1', new SumPercent],
            'included.*.type' => ['required', Rule::in('experiment_branches')],
            'included.*.attributes.name' => 'required|string|distinct',
            'included.*.attributes.percent' => 'required|integer|between:0,100',
            'included.*.attributes.uid' => 'required|string|distinct'
        ];
    }
}
