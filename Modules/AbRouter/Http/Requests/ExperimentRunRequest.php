<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;

class ExperimentRunRequest extends FormRequest
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
                Rule::in('experiment-run')
            ],
            'data.attributes.userSignature' => 'required|string',
            'data.relationships.experiment.data.id' => 'required|string',
            'data.relationships.experiment.data.type' => 'required|in:experiments',
        ];
    }
}
