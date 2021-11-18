<?php

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\AbRouter\Http\Requests\RelatedUserRequest;
use Modules\AbRouter\Http\Transformers\RelatedUser\RelatedUserTransformer;
use Modules\AbRouter\Services\RelatedUser\RelatedUserCreator;
use Modules\AbRouter\Http\Resources\RelatedUser\RelatedUserResource;
use Modules\Auth\Exposable\AuthDecorator;

class RelatedUserController extends Controller
{
    public function create(
        RelatedUserRequest $request,
        RelatedUserTransformer $transformer,
        RelatedUserCreator $creator,
        AuthDecorator $authDecorator
    ) {
        
        $ownerId = $authDecorator->get()->getId();
        $relatedUserDTO = $transformer->transform($ownerId, $request);
        $createRelatedUser = $creator->create($relatedUserDTO);

        return new RelatedUserResource($createRelatedUser);
    }
}
