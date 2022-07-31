<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Transformers\Auth;

use Modules\Auth\Services\Auth\CheckIdToken;
use Modules\Core\Http\Transformers\BaseTransformer;
use Modules\Auth\Services\Auth\DTO\GooglePayloadDTO;

/**
 * Class AuthWithGoogleTransformer
 * @package Modules\Auth\Http\Transformers\Auth
 * @property CheckIdToken $request
 */
class GooglePayloadTransformer extends BaseTransformer
{
    /**
     * @param array $request
     * @return GooglePayloadDTO
     */
    public function transform($request): GooglePayloadDTO
    {
        return new GooglePayloadDTO(
            $request['sub'],
            $request['email']
        );
    }
}
