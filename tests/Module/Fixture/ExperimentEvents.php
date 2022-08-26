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
            'is_feature_toggle' => false,
            'uid' => $experimentName,
            'created_at' => $date,
            'updated_at' => $date
        ];

        $experimentId = $this->laravel->haveRecord(self::TABLE_EXPERIMENTS, $recordExperiment);
        $this->laravel->seeRecord(self::TABLE_EXPERIMENTS, $recordExperiment);
        
        $encodeExperimentId = (new EntityEncoder())->encode($experimentId, 'experiments');
        $branchName = ['branch_one', 'branch_two', 'branch_three'];
        $percent = [50, 25, 25];
        $encodeExperimentBranchId = [];

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
    
            $idBranch = $this->laravel->haveRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
            $encodeExperimentBranchId[] = (new EntityEncoder())->encode($idBranch, 'experiment_branches');
            $this->laravel->seeRecord(self::TABLE_EXPERIMENT_BRANCHES, $recordBranch);
        }
        
        return [
            'alias' => $experimentName,
            'experimentId' => $encodeExperimentId,
            'idBranch' => $encodeExperimentBranchId,
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
            'is_feature_toggle' => false,
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

    public function runExperiments(ApiTester $I, string $token, string $experimentAlias, array $users)
    {
        foreach ($users as $userKey => $user) {

            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($token);

            $payload = [
                'data' => [
                    'type' => 'experiment-run',
                    'attributes' => [
                        'userSignature' => $user
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'id' => $experimentAlias,
                                'type' => 'experiments'
                            ]
                        ]
                    ]
                ]
            ];

            $I->sendPost('/experiment/run', $payload);

            $response = json_decode($I->grabResponse(), true);
            $branch = $response['included'][0]['attributes']['name'];
            $userId = $response['data']['relationships']['experiment_user']['data']['id'];

            $I->seeResponseCodeIsSuccessful(201);

            if(empty($branchesId[$response['included'][0]['id']])) {
                $branchesId[$response['included'][0]['id']] = $response['included'][0]['id'];
            }
        }
        return $branchesId;
    }

    public function haveConductedExperiments(
        int $ownerId,
        int $experimentId,
        array $experimentBranchesIds,
        array $users
    ) {
        $i = 0;
        $usersCount = count($users);

        foreach ($users as $userKey => $user) {
            if ($userKey === $usersCount/2) {
                $i++;
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
                'experiment_branch_id' => $experimentBranchesIds[$i]
            ];

            $this->laravel->haveRecord(self::TABLE_EXPERIMENT_USER_BRANCHES, $recordExperimentUserBranches);
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

        $experimentBranchUserId = $this
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
