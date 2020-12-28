<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Requests;

use Modules\Core\Http\Requests\FormRequest;

class ReceivedEmailCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'To' => 'string|required',
        ];
    }
}
