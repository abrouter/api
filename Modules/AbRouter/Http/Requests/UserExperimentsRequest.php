<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;

class UserExperimentsRequest extends FormRequest
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
                Rule::in('experiment_users')
            ],
            'data.attributes.user_signature' => 'required|string',
            'data.relationships.experiments.data.id' => 'required|string',
            'data.relationships.experiments.data.type' => ['required', Rule::in('experiments')],
            'data.relationships.branches.data.id' => 'required|string',
            'data.relationships.branches.data.type' => ['required', Rule::in('experiment_branches')],
        ];
    }
}
