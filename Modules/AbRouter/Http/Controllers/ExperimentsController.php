<?php

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AbRouter\Http\Requests\ExperimentRequest;
use Modules\AbRouter\Http\Requests\ExperimentRunRequest;
use Modules\AbRouter\Http\Resources\Experiment\ExperimentResource;
use Modules\AbRouter\Http\Resources\ExperimentBranchUser\ExperimentBranchUserResource;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentDeleteTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\RunExperimentTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\SimpleRunTransformer;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Services\Experiment\ExperimentService;
use Modules\AbRouter\Services\Experiment\ExperimentDeleteService;
use Modules\AbRouter\Services\Experiment\RunService;
use Modules\AbRouter\Services\Experiment\SimpleRunService;
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
     * @param ExperimentRequest $request
     * @param ExperimentTransformer $experimentTransformer
     * @param ExperimentService $experimentService
     * @return ExperimentResource
     */
    public function createOrUpdate(
        ExperimentRequest $request,
        ExperimentTransformer $experimentTransformer,
        ExperimentService $experimentService
    ) {
        $experiment = $experimentService->createOrUpdate($experimentTransformer->transform($request));
        return new ExperimentResource($experiment);
    }

    /**
     * @param ExperimentDeleteTransformer $experimentDeleteTransformer
     * @param ExperimentDeleteService $experimentDeleteService
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(
        ExperimentDeleteTransformer $experimentDeleteTransformer,
        ExperimentDeleteService $experimentDeleteService,
        Request $request
    ) {
        $experimentDeleteService->delete($experimentDeleteTransformer->transform($request));
        return response()->noContent();
    }

    public function runSimple(
        SimpleRunTransformer $simpleRunTransformer,
        SimpleRunService $simpleRunService,
        Request $request
    ) {
        return $simpleRunService->run($simpleRunTransformer->transform($request));
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
        return (new ExperimentCollection(
            $experiment->newQuery()->where('owner_id', $userId)->get()->all()
        ));
    }
}
