<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Modules\AbRouter\Services\Experiment\DTO\RunExperimentDTO;
use Modules\AbRouter\Services\Experiment\DTO\SimpleRunDTO;
use Modules\Auth\Repositories\Auth\TokenRepository;

class SimpleRunService
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * @var RunService
     */
    private $runService;

    public function __construct(TokenRepository $tokenRepository, RunService $runService)
    {
        $this->tokenRepository = $tokenRepository;
        $this->runService = $runService;
    }

    public function run(SimpleRunDTO $simpleRunDTO): array
    {
        $token = $this->tokenRepository->find($simpleRunDTO->getToken());
        if (!$token) {
            return [
                'error' => true,
                'message' => 'Unauthorized',
            ];
        }
        $decision = $this->runService->run(
            new RunExperimentDTO(
                $token->getAttribute('user_id'),
                $simpleRunDTO->getUserId(),
                $simpleRunDTO->getExperimentId()
            )
        );

        return [
            'error' => false,
            'branch_uid' => $decision->experimentBranch->uid,
            'experiment_uid' => $decision->experiment->uid,
        ];
    }
}
