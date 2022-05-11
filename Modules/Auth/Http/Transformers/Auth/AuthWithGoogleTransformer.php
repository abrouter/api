<?php
declare(strict_types=1);

namespace Modules\Auth\Http\Transformers\Auth;

use Modules\Auth\Http\Requests\Auth\AuthWithGoogleRequest;
use Modules\Core\Http\Transformers\BaseTransformer;
use Modules\Auth\Services\Auth\DTO\AuthWithGoogleRequestDTO;

/**
 * Class AuthWithGoogleTransformer
 * @package Modules\Auth\Http\Transformers\Auth
 * @property AuthWithGoogleRequest $request
 */
class AuthWithGoogleTransformer extends BaseTransformer
{
    /**
     * @param AuthWithGoogleRequest $request
     * @return AuthWithGoogleRequestDTO
     */
    public function transform($request): AuthWithGoogleRequestDTO
    {
        return new AuthWithGoogleRequestDTO(
            $request->getAttribute('id_token')
        );
    }
}
