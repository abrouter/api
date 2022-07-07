<?php

namespace Modules\AbRouter\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AbRouter\Http\Requests\ExperimentRequest;
use Modules\AbRouter\Http\Requests\ExperimentRunRequest;
use Modules\AbRouter\Http\Requests\UserExperimentsRequest;
use Modules\AbRouter\Http\Resources2\Experiment\ExperimentScheme;
use Modules\AbRouter\Http\Resources2\ExperimentBranchUser\ExperimentBranchUserScheme;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\ExperimentDeleteTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\RunExperimentTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\SimpleRunTransformer;
use Modules\AbRouter\Http\Transformers\Experiments\UserExperimentsTransformer;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Repositories\Experiments\ExperimentBranchUserRepository;
use Modules\AbRouter\Services\Experiment\ExperimentService;
use Modules\AbRouter\Services\Experiment\ExperimentDeleteService;
use Modules\AbRouter\Services\Experiment\RunService;
use Modules\AbRouter\Services\Experiment\SimpleRunService;
use Modules\AbRouter\Services\Experiment\UserExperimentsService;
use Modules\Auth\Exposable\AuthDecorator;

class ExperimentsController extends Controller
{
    /**
     * @param ExperimentRunRequest $request
     * @param RunExperimentTransformer $runExperimentTransformer
     * @param RunService $runService
     * @return ExperimentBranchUserScheme
     */
    public function run(
        ExperimentRunRequest $request,
        RunExperimentTransformer $runExperimentTransformer,
        RunService $runService
    ) {
        $run = $runService->run($runExperimentTransformer->transform($request));
        return (new ExperimentBranchUserScheme(new SimpleDataProvider($run)))->addInclude('experiment_branch_user');
    }

    /**
     * @param ExperimentRequest $request
     * @param ExperimentTransformer $experimentTransformer
     * @param ExperimentService $experimentService
     * @return ExperimentScheme
     */
    public function createOrUpdate(
        ExperimentRequest $request,
        ExperimentTransformer $experimentTransformer,
        ExperimentService $experimentService
    ) {
        $experiment = $experimentService->createOrUpdate($experimentTransformer->transform($request));
        return new ExperimentScheme(new SimpleDataProvider($experiment));
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
     * @return ExperimentScheme
     */
    public function index(AuthDecorator $authDecorator, Experiment $experiment)
    {
        $userId = $authDecorator->get()->getId();

        return (new ExperimentScheme(
            new SimpleDataProvider(
                $experiment
                    ->newQuery()
                    ->where('owner_id', $userId)
                    ->get()
            )
        ))->addMeta([
            'token' => (new Token())->newQuery()->where('user_id', $authDecorator->get()->getId())->first()->id,
        ]);
    }

    /**
     * Wrong name. Todo: rename
     * @param AuthDecorator $authDecorator
     * @param ExperimentBranchUserRepository $experimentsRepository
     * @param string $userId
     * @return ExperimentBranchUserScheme
     */
    public function getUserExperiments(
        AuthDecorator $authDecorator,
        ExperimentBranchUserRepository $experimentsRepository,
        string $userId
    ): ExperimentBranchUserScheme
    {
        $owner = $authDecorator->get()->getId();

        return (new ExperimentBranchUserScheme(
            new SimpleDataProvider(
                $experimentsRepository->getExperimentsBranchesByUserSignature($owner, $userId)
            )
        ));
    }

    /**
     * @param UserExperimentsRequest $request
     * @param UserExperimentsTransformer $addUserToExperimentTransformer
     * @param UserExperimentsService $experimentService
     * @return ExperimentBranchUserScheme
     */
    public function addUserToExperiment(
        UserExperimentsRequest $request,
        UserExperimentsTransformer $addUserToExperimentTransformer,
        UserExperimentsService $experimentService
    ) {
        $addUserToExperimentDTO = $addUserToExperimentTransformer->transform($request);
        $experimentUser = $experimentService->addUserToExperiment($addUserToExperimentDTO);

        return (new ExperimentBranchUserScheme(new SimpleDataProvider($experimentUser)))
            ->addInclude('experiment_branch_user');
    }

    public function deleteUserFromExperiment(
        UserExperimentsRequest $request,
        UserExperimentsTransformer $addUserToExperimentTransformer,
        UserExperimentsService $experimentService
    ) {
        $addUserToExperimentDTO = $addUserToExperimentTransformer->transform($request);
        $experimentService->deleteUserFromExperiment($addUserToExperimentDTO);

        return response()->noContent();
    }
}
