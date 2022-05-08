<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Codeception\Module\Laravel;
use Modules\Core\EntityId\EntityEncoder;
use Modules\AbRouter\Services\Experiment\CreateAliasExperiments;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;

class FeatureToggle extends Module implements DependsOnModule
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

    public function haveFeatureToggle(int $owner)
    {
        $experimentAlias = (new CreateAliasExperiments())->create('feature-toggle-experiment');
        $config = '[]';
        $date = (new \DateTime())->format('Y-m-d');
        $recordExperiment = [
            'owner_id' => $owner,
            'name' => 'feature-toggle-experiment',
            'alias' => $experimentAlias,
            'config' => $config,
            'is_enabled' => true,
            'is_feature_toggle' => true,
            'uid' => 'feature-toggle-experiment',
            'created_at' => $date,
            'updated_at' => $date
        ];

        $experimentId = $this->laravel->haveRecord(self::TABLE_EXPERIMENTS, $recordExperiment);
        $this->laravel->seeRecord(self::TABLE_EXPERIMENTS, $recordExperiment);
        
        $encodeExperimentId = (new EntityEncoder())->encode($experimentId, 'experiments');

        $branchName = ['ON', 'OFF'];
        $percent = [100, 0];
        $encodeExperimentBranchId = [];

        for ($i = 0; $i < 2; $i++) { 
            $recordBranch = [
                'experiment_id' => $experimentId,
                'name' => $branchName[$i],
                'config' => $config,
                'percent' => $percent[$i],
                'uid' => $branchName[$i],
                'created_at' => $date,
                'updated_at' => $date
            ];
    
            $idBranch = $this->laravel->haveRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
            $encodeExperimentBranchId[] = (new EntityEncoder())->encode($idBranch, 'experiment_branches');
            $this->laravel->seeRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
        }
        
        return ['encodeExperimentId' => $encodeExperimentId, 'experimentId' => $experimentId, 'name' => 'feature-toggle-experiment', 'alias' => $experimentAlias, 'idBranch' => $encodeExperimentBranchId];
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
