<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Core\Http\Requests\FormRequest;
use Modules\Core\Rules\SumPercent;
use Modules\Core\EntityId\EntityEncoder;

class ExperimentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->input('data.id')) {
            $id = (new EntityEncoder())->decode($this->input('data.id'), 'experiments');
        }

        return [
            'data.type' => [
                'required',
                Rule::in('experiments')
            ],
            'data.attributes.name' => 'required|string',
            'data.attributes.alias' => ['required', 'string',
                Rule::unique('experiments', 'alias')
                    ->ignore($id ?? null)
                    ->where(function ($query) {
                        /**
                         * @var AuthDecorator $authDecorator
                         */
                        $authDecorator = app()->make(AuthDecorator::class);
                        $query->where('owner_id', $authDecorator->get()->getUser()->id);
                    })
            ],
            'included' => ['array', 'min:1', new SumPercent],
            'included.*.type' => ['required', Rule::in('experiment_branches')],
            'included.*.attributes.name' => 'required|string|distinct',
            'included.*.attributes.percent' => 'required|integer|between:0,100',
            'included.*.attributes.uid' => 'required|string|distinct'
        ];
    }

    public function messages(): array
    {
        return [
            'unique' => 'Experiment uid already exist.',
        ];
    }
}
