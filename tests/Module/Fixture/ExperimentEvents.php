<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use ApiTester;
use Codeception\Module\Laravel;
use Modules\Core\EntityId\EntityEncoder;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;

class ExperimentEvents extends Module implements DependsOnModule
{   
    public const TABLE_EXPERIMENTS = 'experiments';
    public const TABLE_EXPERIMENT_BRANCHES = 'experiment_branches';
    public const TABLE_EXPERIMENT_USER_BRANCHES = 'experiment_user_branches';
    public const TABLE_EXPERIMENT_USERS = 'experiment_users';

    /**
     * @var Laravel
     */

    private $laravel;

    public function _inject(Laravel $laravel)
    {
        $this->laravel = $laravel;
    }

    public function haveExperimentWithThreeBranch(int $owner)
    {
        $experimentName = 'experiment_' . uniqid();
        $config = '[]';
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

        $branchName = ['branch_first', 'branch_second', 'branch_third'];
        $percent = [50, 25, 25];
        $idBranch = [];

        for ($i = 0; $i < 3; $i++) { 
            $recordBranch = [
                'experiment_id' => $experimentId,
                'name' => $branchName[$i],
                'config' => $config,
                'percent' => $percent[$i],
                'uid' => $branchName[$i],
                'created_at' => $date,
                'updated_at' => $date
            ];
    
            $idBranch[] = $this->laravel->haveRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
            $this->laravel->seeRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
        }
        
        return [
            'alias' => $experimentName,
            'experimentId' => $experimentId,
            'idBranch' => $idBranch,
            'branchName' => $branchName
        ];
    }

    public function haveExperimentWithTwoBranch(int $owner)
    {
        $experimentName = 'experiment_' . uniqid();
        $config = '[]';
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
        $branchName = ['branch_first', 'branch_second'];
        $percent = 50;
        $encodeExperimentBranchId = [];

        for ($i = 0; $i < 2; $i++) { 
            $recordBranch = [
                'experiment_id' => $experimentId,
                'name' => $branchName[$i],
                'config' => $config,
                'percent' => $percent,
                'uid' => $branchName[$i],
                'created_at' => $date,
                'updated_at' => $date
            ];
    
            $branchId[] = $this->laravel->haveRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
            $encodeExperimentBranchId[] = (new EntityEncoder())->encode($branchId[$i], 'experiment_branches');
            $this->laravel->seeRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
        }
        
        return [
            'alias' => $experimentName,
            'experimentId' => $encodeExperimentId,
            'decodeExperimentId' => $experimentId,
            'idBranch' => $encodeExperimentBranchId,
            'decodeBranchId' => $branchId,
            'branchName' => $branchName
        ];
    }

    public function haveConductedExperiments(
        int $ownerId,
        int $experimentId,
        int $countBranches,
        array $experimentBranchesIds,
        array $users
    ) {
        $i = 0;
        $n = 0;
        $usersCount = count($users);
        $countUserToBranch = intval($usersCount/$countBranches);

        foreach ($users as $user) {
            if ($i === $countUserToBranch) {
                $n++;
                $i = 0;
            }

            if ($n === $countBranches) {
                $n = 0;
            }

            $recordExperimentUsers = [
                'user_signature' => $user,
                'owner_id' => $ownerId,
                'config' => '{}'
            ];

            $userId = $this->laravel->haveRecord(self::TABLE_EXPERIMENT_USERS, $recordExperimentUsers);

            $recordExperimentUserBranches = [
                'experiment_user_id' => $userId,
                'experiment_id' => $experimentId,
                'experiment_branch_id' => $experimentBranchesIds[$n]
            ];

            $this->laravel->haveRecord(self::TABLE_EXPERIMENT_USER_BRANCHES, $recordExperimentUserBranches);

            $i++;
        }
    }

    public function experimentsHaveUsers(
        string $userSignature,
        int $ownerId,
        int $experimentId,
        int $branchId
    ) {
        $date = (new \DateTime())->format('Y-m-d');

        $recordExperimentUsers = [
            'owner_id' => $ownerId,
            'user_signature' => $userSignature,
            'config' => '{}',
            'created_at' => $date,
            'updated_at' => $date
        ];

        $userId = $this
            ->laravel
            ->haveRecord(
                self::TABLE_EXPERIMENT_USERS,
                $recordExperimentUsers
            );

        $recordExperimentBranchUser = [
            'experiment_user_id' => $userId,
            'experiment_id' => $experimentId,
            'experiment_branch_id' => $branchId,
            'created_at' => $date,
            'updated_at' => $date
        ];

        $this
            ->laravel
            ->haveRecord(
                self::TABLE_EXPERIMENT_USER_BRANCHES,
                $recordExperimentBranchUser
            );

        $this->laravel->seeRecord(
            self::TABLE_EXPERIMENT_USERS,
            $recordExperimentUsers
        );

        $this->laravel->seeRecord(
            self::TABLE_EXPERIMENT_USER_BRANCHES,
            $recordExperimentBranchUser
        );
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
