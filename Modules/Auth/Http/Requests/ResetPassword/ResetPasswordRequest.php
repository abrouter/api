<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Requests\ResetPassword;

use Modules\Core\Http\Requests\FormRequest;
use Modules\Auth\Models\User\User;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
            'data.attributes.email' => 'required|string|email',
            'data.attributes.password' => 'required|string',
            'data.attributes.token' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'data.attributes.email' => 'Email is not valid',
        ];
    }
}
