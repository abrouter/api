<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Services\Experiment\DTO\ExperimentDeleteDTO;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Core\EntityId\EntityEncoder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExperimentDeleteService
{
    /**
     * @var EntityEncoder
     */
    private $encoder;

    /**
     * @var AuthDecorator
     */
    private $authDecorator;

    public function __construct(EntityEncoder $encoder, AuthDecorator $authDecorator)
    {
        $this->encoder = $encoder;
        $this->authDecorator = $authDecorator;
    }

    public function delete(ExperimentDeleteDTO $experimentDeleteDTO)
    {
        try {
            $experimentId = $this->encoder->decode($experimentDeleteDTO->getId(), Experiment::getType());
        } catch (\Throwable $e) {
            throw new NotFoundHttpException('Failed to find an experiment');
        }
        $experiment = (new Experiment())
            ->where('id', $experimentId)
            ->where('owner_id', $this->authDecorator->get()->getId())
            ->delete();
        
        $this->deleteBranches($experimentId);

        return $experiment;
    }

    private function deleteBranches($experimentId)
    {
        $branchModels = (new ExperimentBranches())->where('experiment_id', $experimentId)->delete();

        return $branchModels;
    }
}
