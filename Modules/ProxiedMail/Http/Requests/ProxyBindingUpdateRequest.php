<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\FormRequest;
use Modules\ProxiedMail\Models\ProxyBinding;

class ProxyBindingUpdateRequest extends FormRequest
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
                Rule::in(ProxyBinding::getType())
            ],
            'data.id' => 'required',
            'data.attributes.proxy_address' => 'required|string|email|regex:/^.+\@proxiedmail.com$/i',
            'data.attributes.real_addresses' => 'required|array',
        ];
    }
}
