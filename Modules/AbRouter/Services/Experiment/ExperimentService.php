<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Services\Experiment\DTO\BranchDTO;
use Modules\AbRouter\Services\Experiment\DTO\ExperimentDTO;
use Modules\AbRouter\Services\Experiment\CreateAliasExperiments;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Core\EntityId\Encoder;

class ExperimentService
{
    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * @var AuthDecorator
     */
    private $authDecorator;

    public function __construct(Encoder $encoder, AuthDecorator $authDecorator, CreateAliasExperiments $createAlias)
    {
        $this->encoder = $encoder;
        $this->authDecorator = $authDecorator;
        $this->createAlias = $createAlias;
    }

    public function createOrUpdate(ExperimentDTO $experimentDTO)
    {
        try {
            $experimentId = $this->encoder->decode($experimentDTO->getId(), Experiment::getType());
        } catch (\Throwable $e) {
            $experimentId = '';
        }
        $experiment = (new Experiment())
            ->newQuery()
            ->where('id', $experimentId)
            ->where('owner_id', $this->authDecorator->get()->getId())
            ->first();
        if (empty($experiment)) {
            $experiment = new Experiment();
        }
        $experiment->fill([
            'owner_id' => $this->authDecorator->get()->getId(),
            'name' => $experimentDTO->getName(),
            'alias' => $this->createAlias->create($experimentDTO->getName()),
            'config' => json_encode($experimentDTO->getConfig()),
            'is_enabled' => true,
            'is_feature_toggle' => true,
            'uid' => $experimentDTO->getName(),
        ]);
        $experiment->save();
        $experiment->refresh();

        $this->updateBranches($experimentDTO, $experiment);


        return $experiment;
    }

    private function updateBranches(ExperimentDTO $experimentDTO, Experiment $experiment): array
    {
        $branchesIds = array_reduce($experimentDTO->getBranches(), function (array $acc, BranchDTO $branchDTO) {
            try {
                $acc[$branchDTO->getId()] = $this->encoder->decode($branchDTO->getId(), ExperimentBranches::getType());
            } catch (\Throwable $e) {
            }

            return $acc;
        }, []);
        $branchModels = (new ExperimentBranches())->newQuery()->whereIn('id', $branchesIds)->get();
        $toUpdate = $experimentDTO->getBranches();

        $models = [];
        foreach ($toUpdate as $key => $branchDTO) {
            /**
             * @var ExperimentDTO $branchDTO
             */
            /**
             * @var ExperimentBranches $branchModel
             */
            $branchModel = $branchModels->where('id', $branchesIds[$branchDTO->getId()] ?? 0)->first();
            if ($branchModel === null) {
                $branchModel = new ExperimentBranches();
            }

            $branchModel->fill([
                'experiment_id' => $experiment->id,
                'name' => $branchDTO->getUid(),
                'uid' => $branchDTO->getUid(),
                'config' => json_encode($branchDTO->getConfig()),
                'percent' => $branchDTO->getPercent(),
            ]);
            $branchModel->save();
            $models[] = $branchModel;
        }

        return $models;
    }
}
