<?php

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AbRouter\Http\Requests\ExperimentRunRequest;
use Modules\AbRouter\Http\Resources\Experiment\ExperimentResource;
use Modules\AbRouter\Http\Resources\ExperimentBranchUser\ExperimentBranchUserResource;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\RunExperimentTransformer;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Services\Experiment\ExperimentService;
use Modules\AbRouter\Services\Experiment\RunService;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\AbRouter\Http\Resources\Experiment\ExperimentCollection;

class ExperimentsController extends Controller
{
    /**
     * @param ExperimentRunRequest $request
     * @param RunExperimentTransformer $runExperimentTransformer
     * @param RunService $runService
     * @return ExperimentBranchUserResource
     */
    public function run(
        ExperimentRunRequest $request,
        RunExperimentTransformer $runExperimentTransformer,
        RunService $runService
    ) {
        $run = $runService->run($runExperimentTransformer->transform($request));
        return new ExperimentBranchUserResource($run);
    }

    /**
     * @param ExperimentTransformer $experimentTransformer
     * @param ExperimentService $experimentService
     * @param Request $request
     * @return ExperimentResource
     */
    public function createOrUpdate(
        ExperimentTransformer $experimentTransformer,
        ExperimentService $experimentService,
        Request $request
    ) {
        $experiment = $experimentService->createOrUpdate($experimentTransformer->transform($request));
        return new ExperimentResource($experiment);
    }

    /**
     * @param AuthDecorator $authDecorator
     * @param Experiment $experiment
     * @return ExperimentCollection
     * @throws BindingResolutionException
     */
    public function index(AuthDecorator $authDecorator, Experiment $experiment)
    {
        $userId = $authDecorator->get()->getId();
        return new ExperimentCollection(
            $experiment->newQuery()->where('owner_id', $userId)->get()->all()
        );
    }
}
