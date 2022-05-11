<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Requests\User;

use Modules\Core\Http\Requests\FormRequest;
use Modules\Auth\Models\User\User;
use Illuminate\Validation\Rule;

class UserCreateRequest extends FormRequest
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
                Rule::in(User::getType())
            ],
            'data.attributes.username' => 'required|string|email',
            'data.attributes.password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'data.attributes.username.email' => 'Email is not valid',
        ];
    }
}
