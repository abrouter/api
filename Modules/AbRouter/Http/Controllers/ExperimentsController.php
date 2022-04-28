<?php

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AbRouter\Http\Requests\ExperimentRequest;
use Modules\AbRouter\Http\Requests\ExperimentRunRequest;
use Modules\AbRouter\Http\Requests\AddUserToExperimentRequest;
use Modules\AbRouter\Http\Resources\Experiment\ExperimentResource;
use Modules\AbRouter\Http\Resources\ExperimentBranchUser\ExperimentBranchUserResource;
use Modules\AbRouter\Http\Resources\AddUserToExperiment\AddUserToExperimentResource;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentDeleteTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\RunExperimentTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\SimpleRunTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\AddUserToExperimentTransformer;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Repositories\Experiments\ExperimentsRepository;
use Modules\AbRouter\Services\Experiment\ExperimentService;
use Modules\AbRouter\Services\Experiment\ExperimentDeleteService;
use Modules\AbRouter\Services\Experiment\RunService;
use Modules\AbRouter\Services\Experiment\SimpleRunService;
use Modules\AbRouter\Services\Experiment\AddUserToExperimentService;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\AbRouter\Http\Resources\Experiment\ExperimentCollection;
use Modules\AbRouter\Http\Resources\ExperimentsHaveUsers\ExperimentsHaveUsersCollection;

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

    /**
     * @param AuthDecorator $authDecorator
     * @param ExperimentsRepository $experimentsRepository
     * @param $id
     * @return ExperimentsHaveUsersCollection
     * @throws BindingResolutionException
     */
    public function getExperimentsHaveUsers(
        AuthDecorator $authDecorator,
        ExperimentsRepository $experimentsRepository,
        string $userId
    ) {
        $owner = $authDecorator->get()->getId();

        return (new ExperimentsHaveUsersCollection(
            $experimentsRepository->getExperimentsWhichHaveUser($owner, $userId)
        ));
    }

    /**
     * @param AddUserToExperimentRequest $request
     * @param AddUserToExperimentTransformer $addUserToExperimentTransformer
     * @param AddUserToExperimentService $experimentService
     * @return AddUserToExperimentResource
     */
    public function addUserToExperiment(
        AddUserToExperimentRequest $request,
        AddUserToExperimentTransformer $addUserToExperimentTransformer,
        AddUserToExperimentService $experimentService
    ) {
        $addUserToExperimentDTO = $addUserToExperimentTransformer->transform($request);
        $experimentUser = $experimentService->addUserToExperiment($addUserToExperimentDTO);

        return new AddUserToExperimentResource($experimentUser);
    }
}
