<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Carbon\Carbon;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Services\Experiment\DTO\BranchDTO;
use Modules\AbRouter\Services\Experiment\DTO\ExperimentDTO;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Core\EntityId\EntityEncoder;

class ExperimentService
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
            $experiment->fill([
                'start_experiment_day' => Carbon::now()
            ]);
        }

        $experiment->fill([
            'owner_id' => $this->authDecorator->get()->getId(),
            'name' => $experimentDTO->getName(),
            'uid' => $experimentDTO->getName(),
            'alias' => $experimentDTO->getAlias(),
            'config' => json_encode($experimentDTO->getConfig()),
            'is_enabled' => $experimentDTO->getIsEnabled(),
            'is_feature_toggle' => $experimentDTO->getIsFeatureToggle(),
        ]);

        $experiment->save();

        if ($experiment->wasChanged('is_enabled')) {
            $experiment->is_enabled === true
                ? $experiment->fill(['start_experiment_day' => Carbon::now()])->save()
                : $experiment->fill(['start_experiment_day' => null])->save();
        }

        $experiment->refresh();

        $this->deleteBranches($experimentDTO, $experiment);
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
                'name' => $branchDTO->getName(),
                'uid' => $branchDTO->getUid(),
                'config' => json_encode($branchDTO->getConfig()),
                'percent' => $branchDTO->getPercent(),
            ]);
            $branchModel->save();
            $models[] = $branchModel;
        }

        return $models;
    }

    private function deleteBranches(ExperimentDTO $experimentDTO, Experiment $experiment): void
    {
        $branchesIds = array_reduce($experimentDTO->getBranches(), function (array $acc, BranchDTO $branchDTO) {
            try {
                $acc[] = $this->encoder->decode($branchDTO->getId(), ExperimentBranches::getType());
            } catch (\Throwable $e) {
            }

            return $acc;
        }, []);
        
        if (!empty($branchesIds)) {
            $allBranchesExperiment = (new ExperimentBranches())->newQuery()->select('id')->where('experiment_id', $experiment->id)->get();
            
            foreach ($allBranchesExperiment as $branch) {
                $allBranches[] = $branch->id;            
            }
            
            $missingBranchesId = array_diff($allBranches, $branchesIds);

            if (!empty($missingBranchesId)) {
                foreach ($missingBranchesId as $branchId) {
                    if ($branchId === null) {
                        break;
                    }

                    $deleteBranch = (new ExperimentBranches())->newQuery()
                        ->where('id', $branchId)
                        ->where('experiment_id', $experiment->id)
                        ->delete();
                }
            }
        }
    }
}
