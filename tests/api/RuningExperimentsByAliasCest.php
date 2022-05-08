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
                    'run-uid' => $response['data']['attributes']['run-uid'],
                    'branch-uid' => $response['data']['attributes']['branch-uid'],
                    'experiment-uid' => $response['data']['attributes']['experiment-uid']
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
                        "name" => $response['included'][0]['attributes']['name'],
                        "uid" => $response['included'][0]['attributes']['uid'],
                        "percent" => $response['included'][0]['attributes']['percent']
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

        $experimentUserId = (new EntityEncoder())->decode($response['data']['relationships']['experiment_user']['data']['id'], 'experiment_users');
        $experimentId = $experiment['experimentId'];
        $experimentBranchId = (new EntityEncoder())->decode($response['data']['relationships']['experiment_branch_id']['data']['id'], 'experiment_branches');
        $recordBranchUsers = ['experiment_user_id' => $experimentUserId, 'experiment_id' => $experimentId, 'experiment_branch_id' => $experimentBranchId];
        $recordExperimentUsers = ['owner_id' => $user['id'], 'user_signature' => $userSignature];

        $I->seeRecord('experiment_user_branches', $recordBranchUsers);
        $I->seeRecord('experiment_users', $recordExperimentUsers);
    }
}
