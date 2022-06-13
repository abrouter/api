<?php

use Modules\Core\EntityId\EntityEncoder;

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
                    'uid' => $experimentName,
                    'alias' => $experimentName,
                    'is_enabled' => true,
                    'is_feature_toggle' => false,
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

        $experimentId = (new EntityEncoder())->decode($response['data']['id'], 'experiments');
        $alias = $response['data']['attributes']['alias'];
        $config = $response['data']['attributes']['config'];

        $recordExperiment = [
            'name' => $experimentName,
            'uid' => $experimentName,
            'alias' => $alias,
            'is_enabled' => true,
            'is_feature_toggle' => false,
            'owner_id' => $user['id']
        ];

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'experiments',
                'attributes' => [
                    'name' => $experimentName,
                    'alias' => $alias,
                    'uid' => $experimentName,
                    'config' => $config,
                    'is_enabled' => true,
                    'is_feature_toggle' => false
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
            $recordBranch = [
                'experiment_id' => $experimentId,
                'name' => $branches[$n],
                'uid' => $branches[$n],
                'percent' => $percents[$n]
            ];
            $I->seeRecord('experiment_branches', $recordBranch);
        }
        
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
                            'id' => $experiment['encode_experiment_id'],
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
                        "name" => 'button-color-blue',
                        "uid" => 'button-color-blue',
                        "percent" => 100
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

        $recordBranchUsers = [
            'experiment_id' => $experiment['experiment_id'],
            'experiment_branch_id' => $experimentBranches[1]['branch_id']
        ];

        $recordExperimentUsers = [
            'owner_id' => $user['id'],
            'user_signature' => $userSignature
        ];

        $I->seeRecord('experiment_user_branches', $recordBranchUsers);
        $I->seeRecord('experiment_users', $recordExperimentUsers);
    }

    public function updateExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $experimentBranches = [];

        $experimentBranches[] = $I
            ->haveExperimentBranch($experiment['experiment_id'], 'button-color-red', 50);
        $experimentBranches[] = $I
            ->haveExperimentBranch($experiment['experiment_id'], 'button-color-blue', 50);

        $experimentId = $experiment['encode_experiment_id'];
        $experimentName = 'experiment_' . uniqid();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPatch("/experiments/{$experimentId}", [
            'data' => [
                'id' => $experimentId,
                'type' => 'experiments',
                'attributes' => [
                    'name' => $experimentName,
                    'uid' => $experimentName,
                    'alias' => $experimentName,
                    'is_enabled' => true,
                    'is_feature_toggle' => false,
                    'config' => [],
                ],
                'relationships' => [
                    'branches' => [
                        'data' => [
                            [
                            'id' => $experimentBranches[0]['branch_id'],
                            'type' => 'experiment_branches',
                            ],
                            [
                                'id' => $experimentBranches[1]['branch_id'],
                                'type' => 'experiment_branches',
                            ],
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
                    'id' => $experimentBranches[0]['encode_branch_id'],
                    'type' => 'experiment_branches',
                    'attributes' => [
                        'name' => 'button-color-red',
                        'percent' => 50,
                        'config' => [],
                        'uid' => 'button-color-red',
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'id' => $experimentId,
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
                    'id' => $experimentBranches[1]['encode_branch_id'],
                    'type' => 'experiment_branches',
                    'attributes' => [
                        'name' => 'button-color-blue',
                        'percent' => 50,
                        'config' => [],
                        'uid' => 'button-color-blue',
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'id' => $experimentId,
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

        $alias = $response['data']['attributes']['alias'];
        $config = $response['data']['attributes']['config'];
        $recordBranch = [];

        $recordExperiment = [
            'name' => $experimentName,
            'uid' => $experimentName,
            'alias' => $alias,
            'is_enabled' => true,
            'is_feature_toggle' => false,
            'owner_id' => $user['id']
        ];

        $recordBranch[] = [
            'experiment_id' => $experiment['experiment_id'],
            'name' => 'button-color-red',
            'uid' => 'button-color-red',
            'percent' => 50
        ];

        $recordBranch[] = [
            'experiment_id' => $experiment['experiment_id'],
            'name' => 'button-color-blue',
            'uid' => 'button-color-blue',
            'percent' => 50
        ];

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'experiments',
                'attributes' => [
                    'name' => $experimentName,
                    'uid' => $experimentName,
                    'alias' => $alias,
                    'config' => $config,
                    'is_enabled' => true,
                    'is_feature_toggle' => false
                ],
                'relationships' => [
                    'owner' => [
                        'data' => [
                            'id' => $user['encodeId'],
                            'type' => 'users'
                        ]
                    ],
                    'branches' => [
                        'data' => [
                            [
                                'id' => $experimentBranches[0]['encode_branch_id'],
                                'type' => 'experiment_branches',
                            ],
                            [
                                'id' => $experimentBranches[1]['encode_branch_id'],
                                'type' => 'experiment_branches',
                            ],
                        ]
                    ],
                ]
            ],
        ]);

        $I->seeRecord('experiments', $recordExperiment);
        $I->seeRecord('experiment_branches', $recordBranch[0]);
        $I->seeRecord('experiment_branches', $recordBranch[1]);
    }

    public function deleteExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $experimentBranches = [];

        $experimentBranches[] = $I
            ->haveExperimentBranch($experiment['experiment_id'], 'button-color-red', 50);
        $experimentBranches[] = $I
            ->haveExperimentBranch($experiment['experiment_id'], 'button-color-blue', 50);

        $experimentId = $experiment['encode_experiment_id'];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendDelete("/experiments/{$experimentId}", [
            'data' => [
                'id' => $experimentId,
                'type' => 'experiments',
                'attributes' => [
                    'name' => $experiment['name'],
                ],
                'relationships' => [
                    'branches' => [
                        'data' => [
                            [
                            'id' => $experimentBranches[0]['branch_id'],
                            'type' => 'experiment_branches',
                            ],
                            [
                                'id' => $experimentBranches[1]['branch_id'],
                                'type' => 'experiment_branches',
                            ]
                        ]
                    ],
                    'owner' => [
                        'data' => [
                            'id' => $user['encodeId'],
                            'type' => 'users',
                        ]
                    ]
                ]
            ]
        ]);

        $recordExperiment = [
            'name' => $experiment['name'],
            'owner_id' => $user['id']
        ];

        $recordBranch = ['experiment_id' => $experiment['experiment_id']];

        $I->seeResponseCodeIsSuccessful(204);

        $I->dontSeeRecord('experiments', $recordExperiment);
        $I->dontSeeRecord('experiment_branches', $recordBranch);
    }

    public function getExperimentsHaveUser(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);

        $experimentBranch = $I
            ->haveExperimentBranch($experiment['experiment_id'], 'button-color-red', 50);

        $userSignature = uniqid();

        $I->experimentsHaveUsers(
            $userSignature,
            $user['id'],
            $experiment['experiment_id'],
            $experimentBranch['branch_id']
        );

        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);
        $I->sendGet('experiments/have-user/' . $userSignature);

        $I->seeResponseCodeIsSuccessful(200);

        $I->seeResponseContainsJson([
            'data' => [
                [
                    'type' => 'experiment_branch_users',
                    'attributes' => [
                        'run-uid' => $experiment['alias'] . '-' . 'button-color-red',
                        'branch-uid' => 'button-color-red',
                        'experiment-uid' => $experiment['alias'] ,
                    ],
                ]
            ]
        ]);
    }

    public function addUserToExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);

        $experimentBranch = $I
            ->haveExperimentBranch($experiment['experiment_id'], 'button-color-red', 50);

        $userSignature = uniqid();

        $I->experimentsHaveUsers(
            $userSignature,
            $user['id'],
            $experiment['experiment_id'],
            $experimentBranch['branch_id']
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('experiments/add-user', [
            'data' => [
                'type' => 'experiment_users',
                'attributes' => [
                    'user_signature' => $userSignature,
                ],
                'relationships' => [
                    'experiments' => [
                        'data' => [
                            'id' => $experiment['encode_experiment_id'],
                            'type' => 'experiments'
                        ]
                    ],
                    'branches' => [
                        'data' => [
                            'id' => $experimentBranch['encode_branch_id'],
                            'type' => 'experiment_branches'
                        ]
                    ]
                ]
            ]
        ]);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(200);

        $I->seeResponseContainsJson([
            'data' => [
                'type' => 'experiment_branch_users',
                'id' => $response['data']['id'],
                'attributes' => [
                    'run-uid' => $experiment['name'] . '-' . 'button-color-red',
                    'branch-uid' => 'button-color-red',
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
                            'id' => $experiment['encode_experiment_id']
                        ]
                    ],
                    'experiment_branch_id' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $experimentBranch['encode_branch_id']
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'type' => 'experiment_branches',
                    'id' => $experimentBranch['encode_branch_id'],
                    'attributes' => [
                        'name' => 'button-color-red',
                        'uid' => 'button-color-red',
                        'percent' => 50
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $experiment['encode_experiment_id']
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}
