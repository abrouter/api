<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;

class AuthUserRequest extends FormRequest
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
                Rule::in('auth-request')
            ],
            'data.attributes.username' => 'required|string',
            'data.attributes.password' => 'required|string',
        ];
    }
}
