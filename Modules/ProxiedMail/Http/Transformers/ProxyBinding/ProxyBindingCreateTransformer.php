<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Transformers\ProxyBinding;

use Modules\Auth\Exposable\AuthDecorator;
use Modules\Core\Http\Requests\FormRequest;
use Modules\Core\Http\Transformers\BaseTransformer;
use Modules\ProxiedMail\Services\ProxyBindings\DTO\ProxyBindingDTO;

class ProxyBindingCreateTransformer extends BaseTransformer
{
    /**
     * @var AuthDecorator
     */
    private $authDecorator;

    public function __construct(AuthDecorator $authDecorator)
    {
        $this->authDecorator = $authDecorator;
    }

    /**
     * @param FormRequest $request
     * @return ProxyBindingDTO
     */
    public function transform($request)
    {
        return new ProxyBindingDTO(
            $this->authDecorator->get(),
            $request->getAttribute('proxy_address'),
            array_unique($request->getAttribute('real_addresses'))
        );
    }
}
