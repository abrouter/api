<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\ProxiedMail\Http\Requests\ProxyBindingUpdateRequest;
use Modules\ProxiedMail\Repositories\ProxyBindingRepository;
use Modules\ProxiedMail\Http\Requests\ProxyBindingCreateRequest;
use Modules\ProxiedMail\Http\Resources\ProxyBinding\ProxyBindingCollection;
use Modules\ProxiedMail\Http\Resources\ProxyBinding\ProxyBindingResource;
use Modules\ProxiedMail\Http\Transformers\ProxyBinding\ProxyBindingCreateTransformer;
use Modules\ProxiedMail\Services\ProxyBindings\CreatorService;
use Modules\ProxiedMail\Services\ProxyBindings\DeleterService;
use Modules\ProxiedMail\Services\ProxyBindings\UpdaterService;
use Throwable;

class ProxyBindingsController extends Controller
{
    /**
     * @param ProxyBindingCreateRequest $bindingCreateRequest
     * @param ProxyBindingCreateTransformer $transformer
     * @param CreatorService $creator
     * @return ProxyBindingResource
     * @throws Throwable
     */
    public function create(
        ProxyBindingCreateRequest $bindingCreateRequest,
        ProxyBindingCreateTransformer $transformer,
        CreatorService $creator
    ) {
        $proxyBinding = $creator->create($transformer->transform($bindingCreateRequest));
        return new ProxyBindingResource($proxyBinding);
    }

    /**
     * @param ProxyBindingRepository $proxyBindingRepository
     * @param AuthDecorator $authDecorator
     * @return ProxyBindingCollection
     * @throws BindingResolutionException
     */
    public function index(ProxyBindingRepository $proxyBindingRepository, AuthDecorator $authDecorator)
    {
        return new ProxyBindingCollection($proxyBindingRepository->allByUserId(
            $authDecorator->get()->getId()
        ));
    }

    public function patch(
        ProxyBindingUpdateRequest $proxyBindingUpdateRequest,
        ProxyBindingCreateTransformer $transformer,
        AuthDecorator $authDecorator,
        UpdaterService $updaterService,
        string $id
    ) {
        $proxyBinding = $updaterService->update(
            $authDecorator->get()->getId(),
            $id,
            $transformer->transform($proxyBindingUpdateRequest)
        );

        return new ProxyBindingResource($proxyBinding);
    }

    /**
     * @param AuthDecorator $authDecorator
     * @param DeleterService $deleterService
     * @param string $id
     * @return ResponseFactory|Response
     */
    public function delete(
        AuthDecorator $authDecorator,
        DeleterService $deleterService,
        string $id
    ) {
        $deleterService->delete($authDecorator->get()->getId(), $id);
        return response([], Response::HTTP_NO_CONTENT);
    }
}
