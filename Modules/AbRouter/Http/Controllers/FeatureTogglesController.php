<?php

namespace Modules\AbRouter\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\AbRouter\Http\Requests\FeatureToggleRunRequest;
use Modules\AbRouter\Http\Requests\FeatureToggleCreateRequest;
use Modules\AbRouter\Http\Resources2\Experiment\ExperimentScheme;
use Modules\AbRouter\Http\Resources2\FeatureToggle\FeatureToggleScheme;
use Modules\AbRouter\Http\Transformers\FeatureToggles\RunFeatureToggleTransformer;
use Modules\AbRouter\Http\Transformers\FeatureToggles\FeatureToggleCreateTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentDeleteTransformer;
use Modules\AbRouter\Services\Experiment\RunService;
use Modules\AbRouter\Services\Experiment\ExperimentService;
use Modules\AbRouter\Services\Experiment\ExperimentDeleteService;

class FeatureTogglesController extends Controller
{
    /**
     * @param FeatureToggleRunRequest $request
     * @param RunFeatureToggleTransformer $runFeatureToggleTransformer
     * @param RunService $runService
     * @return FeatureToggleScheme
     */
    public function run(
        FeatureToggleRunRequest $request,
        RunFeatureToggleTransformer $runFeatureToggleTransformer,
        RunService $runService
    ) {
        $run = $runService->run($runFeatureToggleTransformer->transform($request));

        return (new FeatureToggleScheme(new SimpleDataProvider($run)))
            ->addInclude('experiment_branches');
    }

    public function createOrUpdate(
        FeatureToggleCreateRequest $request,
        FeatureToggleCreateTransformer $transformer,
        ExperimentService $experimentService
    ) {
        $featureToggle = $experimentService->createOrUpdate($transformer->transform($request));

        return new ExperimentScheme(new SimpleDataProvider($featureToggle));
    }

    public function delete(
        ExperimentDeleteTransformer $experimentDeleteTransformer,
        ExperimentDeleteService $experimentDeleteService,
        Request $request
    ) {
        $experiment = $experimentDeleteService->delete($experimentDeleteTransformer->transform($request));
        return response()->noContent();
    }
}
