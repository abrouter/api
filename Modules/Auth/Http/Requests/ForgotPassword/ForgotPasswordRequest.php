<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Requests\ForgotPassword;

use Modules\Core\Http\Requests\FormRequest;
use Modules\Auth\Models\User\User;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
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
        ];
    }

    public function messages()
    {
        return [
            'data.attributes.email' => 'Email is not valid',
        ];
    }
}
