<?php

namespace Modules\AbRouter\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Illuminate\Routing\Controller;
use Modules\AbRouter\Http\Requests\CustomizationEventRequests;
use Modules\AbRouter\Http\Requests\CustomizationEventUpdateRequests;
use Modules\AbRouter\Http\Resources2\CustomizationEvent\CustomizationEventSchema;
use Modules\AbRouter\Http\Resources2\DisplayUserEvent\DisplayUserEventSchema;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\AbRouter\Http\Transformers\CustomizationEvents\CustomizationEventTransformer;
use Modules\AbRouter\Http\Transformers\CustomizationEvents\CustomizationEventUpdateTransformer;
use Modules\AbRouter\Services\CustomizationEvent\CustomizationEventCreator;
use Modules\AbRouter\Services\CustomizationEvent\CustomizationEventUpdater;
use Modules\AbRouter\Services\CustomizationEvent\CustomizationEventDeleterService;
use Modules\AbRouter\Models\CustomizationEvent\DisplayUserEvent;

class CustomizationEventController extends Controller
{
    /**
     * @param AuthDecorator $authDecorator
     * @param DisplayUserEvent $displayUserEvent
     * @return DisplayUserEventSchema
     */
    public function index(AuthDecorator $authDecorator, DisplayUserEvent $displayUserEvent)
    {
        $userId = $authDecorator->get()->getId();

        return (new DisplayUserEventSchema(
            new SimpleDataProvider($displayUserEvent
                ->newQuery()
                ->where('user_id', $userId)
                ->get()
                ->all()
            )
        ));
    }

    /**
     * @param CustomizationEventRequests $request
     * @param CustomizationEventTransformer $transformer
     * @param CustomizationEventCreator $customizationEventCreator
     * @return CustomizationEventSchema
     */
    public function create(
        CustomizationEventRequests $request,
        CustomizationEventTransformer $transformer,
        CustomizationEventCreator $customizationEventCreator
    ) {
        $customizationEventDTO = $transformer->transform($request);
        $createCustomEvent = $customizationEventCreator->create($customizationEventDTO);
        
        return new CustomizationEventSchema(new SimpleDataProvider($createCustomEvent));
    }

    /**
     * Update the specified resource in storage.
     * @param CustomizationEventUpdateRequests $request
     * @param CustomizationEventUpdateTransformer $transformer
     * @param CustomizationEventUpdater $customizationEventUpdater
     * @return CustomizationEventSchema
     */
    public function update(
        CustomizationEventUpdateRequests $request,
        CustomizationEventUpdateTransformer $transformer,
        CustomizationEventUpdater $customizationEventUpdater
    ) {
        $customizationEventUpdateDTO = $transformer->transform($request);
        $updateCustomEvent = $customizationEventUpdater->update($customizationEventUpdateDTO);
        
        return new CustomizationEventSchema(new SimpleDataProvider($updateCustomEvent));
    }

    /**
     * Remove the specified resource from storage.
     * @param CustomizationEventUpdateRequests $request
     * @param CustomizationEventUpdateTransformer $transformer
     * @param CustomizationEventDeleterService $customizationEventDeleter
     */
    public function delete(
        CustomizationEventUpdateRequests $request,
        CustomizationEventUpdateTransformer $transformer,
        CustomizationEventDeleterService $customizationEventDeleter
    ): void {
        $customizationEventDeleteDTO = $transformer->transform($request);
        $customizationEventDeleter->delete($customizationEventDeleteDTO);
    }
}
