<?php

use Modules\Core\EntityId\Encoder;

class ExperimentCest
{
    public function _before(ApiTester $I)
    {
    }

    public function createExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experimentName = 'experiment_' . uniqid();
        $branchNameFirst = 'branch_' . uniqid();
        $branchNameSecond = 'branch_' . uniqid();
        $branchNameThird = 'branch_' . uniqid();
        $percentFirst = random_int(1,100);
        $percentSecond = random_int(1,100);
        $percentThird = random_int(1,100);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/experiments', [
            'data' => [
                'type' => 'experiments',
                'attributes' => [
                    'name' => $experimentName,
                    'is_enabled' => true,
                    'config' => [],
                ],
                'relationships' => [
                    'branches' => [
                        'data' => [
                            'id' => null,
                            'type' => 'experiment_branches',
                        ]
                    ],
                    'owner' => [
                        'data' => [
                            'id' => $user['encodeId'],
                            'type' => 'users',
                        ]
                    ]
                ]
            ],
            'included' => [
                [ 
                    'id' => null,
                    'type' => 'experiment_branches',
                    'attributes' => [
                        'name' => $branchNameFirst,
                        'percent' => $percentFirst,
                        'config' => [],
                        'uid' => $branchNameFirst,
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'id' => null,
                                'type' => 'experiments',
                            ],
                        ],
                        'owner' => [
                            'data' => [
                                'id' => $user['encodeId'],
                                'type' => 'users',
                            ]
                        ]
                    ]
                ],
                [ 
                    'id' => null,
                    'type' => 'experiment_branches',
                    'attributes' => [
                        'name' => $branchNameSecond,
                        'percent' => $percentSecond,
                        'config' => [],
                        'uid' => $branchNameSecond,
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'id' => null,
                                'type' => 'experiments',
                            ],
                        ],
                        'owner' => [
                            'data' => [
                                'id' => $user['encodeId'],
                                'type' => 'users',
                            ]
                        ]
                    ]
                ],
                [ 
                    'id' => null,
                    'type' => 'experiment_branches',
                    'attributes' => [
                        'name' => $branchNameThird,
                        'percent' => $percentThird,
                        'config' => [],
                        'uid' => $branchNameThird,
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'id' => null,
                                'type' => 'experiments',
                            ],
                        ],
                        'owner' => [
                            'data' => [
                                'id' => $user['encodeId'],
                                'type' => 'users',
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    
        $response = json_decode($I->grabResponse(), true);

        $experimentId = (new Encoder())->decode($response['data']['id'], 'experiments');
        $alias = $response['data']['attributes']['alias'];
        $config = $response['data']['attributes']['config'];
        $isEnabled = $response['data']['attributes']['is_enabled'];
        $recordExperiment = ['name' => $experimentName, 'alias' => $alias, 'is_enabled' => $isEnabled, 'owner_id' => $user['id']];

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'experiments',
                'attributes' => [
                    'name' => $experimentName,
                    'alias' => $alias,
                    'config' => $config,
                    'is_enabled' => $isEnabled
                ],
                'relationships' => [
                    'owner' => [
                        'data' => [
                            'id' => $response['data']['relationships']['owner']['data']['id'],
                            'type' => 'users'
                        ]
                    ],
                    'branches' => [
                        'data' => [
                            [
                                'id' => $response['data']['relationships']['branches']['data'][0]['id'],
                                'type' => 'experiment_branches'
                            ],
                            [
                                'id' => $response['data']['relationships']['branches']['data'][1]['id'],
                                'type' => 'experiment_branches'
                            ],
                            [
                                'id' => $response['data']['relationships']['branches']['data'][2]['id'],
                                'type' => 'experiment_branches'
                            ]
                        ]
                    ]
                ]
            ],
        ]);

        $I->seeRecord('experiments', $recordExperiment);

        $branches = [$branchNameFirst, $branchNameSecond, $branchNameThird];
        $percents = [$percentFirst, $percentSecond, $percentThird];
        
        for ($n = 0; $n < count($branches); $n++) { 
            $recordBranch = ['experiment_id' => $experimentId,'name' => $branches[$n], 'uid' => $branches[$n], 'percent' => $percents[$n]];
            $I->seeRecord('experiment_branches', $recordBranch);
        }
        
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
                            'id' => $experiment['encodeExperimentId'],
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

        $experimentUserId = (new Encoder())->decode($response['data']['relationships']['experiment_user']['data']['id'], 'experiment_users');
        $experimentId = $experiment['experimentId'];
        $experimentBranchId = (new Encoder())->decode($response['data']['relationships']['experiment_branch_id']['data']['id'], 'experiment_branches');
        $recordBranchUsers = ['experiment_user_id' => $experimentUserId, 'experiment_id' => $experimentId, 'experiment_branch_id' => $experimentBranchId];
        $recordExperimentUsers = ['owner_id' => $user['id'], 'user_signature' => $userSignature];

        $I->seeRecord('experiment_user_branches', $recordBranchUsers);
        $I->seeRecord('experiment_users', $recordExperimentUsers);
    }
}
