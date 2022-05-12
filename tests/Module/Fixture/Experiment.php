<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Codeception\Module\Laravel;
use Modules\Core\EntityId\EntityEncoder;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;

class Experiment extends Module implements DependsOnModule
{   
    public const TABLE_EXPERIMENTS = 'experiments';
    public const TABLE_EXPERIMENT_BRANCHES = 'experiment_branches';

    /**
     * @var Laravel
     */

    private $laravel;

    public function _inject(Laravel $laravel)
    {
        $this->laravel = $laravel;
    }

    public function haveExperiment(int $owner)
    {
        $experimentName = 'experiment_' . uniqid();
        $branchName = 'branch_' . uniqid();
        $config = '[]';
        $percent = random_int(1, 100);
        $date = (new \DateTime())->format('Y-m-d');
        $recordExperiment = [
            'owner_id' => $owner,
            'name' => $experimentName,
            'alias' => $experimentName,
            'config' => $config,
            'is_enabled' => true,
            'is_feature_toggle' => true,
            'uid' => $experimentName,
            'created_at' => $date,
            'updated_at' => $date
        ];

        $experimentId = $this->laravel->haveRecord(self::TABLE_EXPERIMENTS, $recordExperiment);
        $this->laravel->seeRecord(self::TABLE_EXPERIMENTS, $recordExperiment);
        
        $encodeExperimentId = (new EntityEncoder())->encode($experimentId, 'experiments');

        $recordBranch = [
            'experiment_id' => $experimentId,
            'name' => $branchName,
            'config' => $config,
            'percent' => $percent,
            'uid' => $branchName,
            'created_at' => $date,
            'updated_at' => $date
        ];

        $idBranch = $this->laravel->haveRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
        $encodeExperimentBranchId = (new EntityEncoder())->encode($idBranch, 'experiment_branches');
        $this->laravel->seeRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
        
        return [
            'encodeExperimentId' => $encodeExperimentId,
            'experimentId' => $experimentId,
            'name' => $experimentName,
            'alias' => $experimentName,
            'idBranch' => $encodeExperimentBranchId,
            'decodeBranchId' => $idBranch
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function _depends(): array
    {
        return [
            Laravel::class => sprintf('%s is mandatory dependency', Laravel::class),
        ];
    }
}
