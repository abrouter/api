<?php

namespace Modules\AbRouter\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Illuminate\Routing\Controller;
use Modules\AbRouter\Http\Requests\RelatedUserRequest;
use Modules\AbRouter\Http\Resources2\RelatedUser\RelatedUserScheme;
use Modules\AbRouter\Http\Transformers\RelatedUser\RelatedUserTransformer;
use Modules\AbRouter\Http\Transformers\RelatedUser\AllRelatedUsersTransformer;
use Modules\AbRouter\Services\RelatedUser\RelatedUserCreator;
use Modules\AbRouter\Services\RelatedUser\AllRelatedUsersServices;
use Modules\Auth\Exposable\AuthDecorator;

class RelatedUserController extends Controller
{
    /**
     * @param RelatedUserRequest $request
     * @param RelatedUserTransformer $transformer
     * @param RelatedUserCreator $creator
     * @param AuthDecorator $authDecorator
     * @return RelatedUserScheme
     */
    public function create(
        RelatedUserRequest $request,
        RelatedUserTransformer $transformer,
        RelatedUserCreator $creator,
        AuthDecorator $authDecorator
    ) {
        
        $ownerId = $authDecorator->get()->getId();
        $relatedUserDTO = $transformer->transform($ownerId, $request);
        $createRelatedUser = $creator->create($relatedUserDTO);

        return new RelatedUserScheme(new SimpleDataProvider($createRelatedUser));
    }

    public function getAllRelatedUsers(
        AllRelatedUsersTransformer $transformer,
        AllRelatedUsersServices $allRelatedUsersServices,
        AuthDecorator $authDecorator,
        $id
    ) {
        $ownerId = $authDecorator->get()->getId();
        $allRelatedUsersDTO = $transformer->transform($ownerId, $id);
        $allRelatedUsers = $allRelatedUsersServices->getAllRelatedUsers($allRelatedUsersDTO);

        return new RelatedUserScheme(new SimpleDataProvider($allRelatedUsers));
    }
}
