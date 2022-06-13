<?php

use Modules\Core\EntityId\EntityEncoder;

class RuningExperimentsByAliasCest
{
    public function _before(ApiTester $I)
    {
        
    }

    public function runExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $experimentBranches = [];

        $experimentBranches[] = $I
            ->haveExperimentBranch($experiment['experiment_id'], 'button-color-red', 0);
        $experimentBranches[] = $I
            ->haveExperimentBranch($experiment['experiment_id'], 'button-color-blue', 100);

        $userSignature = 'user_' . uniqid();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/experiment/run', [
            'data' => [
                'type' => 'experiment-run',
                'attributes' => [
                    'userSignature' => $userSignature
                ],
                'relationships' => [
                    'experiment' => [
                        'data' => [
                            'id' => $experiment['alias'],
                            'type' => 'experiments'
                        ]
                    ]
                ]
            ]                
        ]);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'type' => 'experiment_branch_users',
                'id' => $response['data']['id'],
                'attributes' => [
                    'run-uid' => $experiment['name'] . '-' . 'button-color-blue',
                    'branch-uid' => 'button-color-blue',
                    'experiment-uid' => $experiment['name']
                ],
                'relationships' => [
                    'experiment_user' => [
                        'data' => [
                            'type' => 'experiment_user',
                            'id' => $response['data']['relationships']['experiment_user']['data']['id']
                        ]
                    ],
                    'experiment_id' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $response['data']['relationships']['experiment_id']['data']['id']
                        ]
                    ],
                    'experiment_branch_id' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $response['data']['relationships']['experiment_branch_id']['data']['id']
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'type' => 'experiment_branches',
                    'id' => $response['included'][0]['id'],
                    'attributes' => [
                        'name' => 'button-color-blue',
                        'uid' => 'button-color-blue',
                        'percent' => 100
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $response['included'][0]['relationships']['experiment']['data']['id']
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $recordExperimentUsers = [
            'owner_id' => $user['id'],
            'user_signature' => $userSignature
        ];

        $I->seeRecord('experiment_users', $recordExperimentUsers);

        $recordBranchUsers = [
            'experiment_id' => $experiment['experiment_id'],
            'experiment_branch_id' => $experimentBranches[1]['branch_id']
        ];

        $I->seeRecord('experiment_user_branches', $recordBranchUsers);
    }
}
