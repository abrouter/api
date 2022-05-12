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
        $isEnabled = $response['data']['attributes']['is_enabled'];
        $isFeatureToggle = $response['data']['attributes']['is_feature_toggle'];
        $recordExperiment = [
            'name' => $experimentName,
            'uid' => $experimentName,
            'alias' => $alias,
            'is_enabled' => $isEnabled,
            'is_feature_toggle' => $isFeatureToggle ,
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
                    'is_enabled' => $isEnabled,
                    'is_feature_toggle' => $isFeatureToggle
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

        $experimentUserId = (new EntityEncoder())->decode($response['data']['relationships']['experiment_user']['data']['id'], 'experiment_users');
        $experimentId = $experiment['experimentId'];
        $experimentBranchId = (new EntityEncoder())->decode($response['data']['relationships']['experiment_branch_id']['data']['id'], 'experiment_branches');
        $recordBranchUsers = ['experiment_user_id' => $experimentUserId, 'experiment_id' => $experimentId, 'experiment_branch_id' => $experimentBranchId];
        $recordExperimentUsers = ['owner_id' => $user['id'], 'user_signature' => $userSignature];

        $I->seeRecord('experiment_user_branches', $recordBranchUsers);
        $I->seeRecord('experiment_users', $recordExperimentUsers);
    }

    public function updateExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $experimentId = $experiment['encodeExperimentId'];
        $experimentName = 'experiment_' . uniqid();
        $branchName = 'branch_' . uniqid();
        $percent = random_int(1,100);

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
                            'id' => $experiment['idBranch'],
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
                    'id' => $experiment['idBranch'],
                    'type' => 'experiment_branches',
                    'attributes' => [
                        'name' => $branchName,
                        'percent' => $percent,
                        'config' => [],
                        'uid' => $branchName,
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
        $isEnabled = $response['data']['attributes']['is_enabled'];
        $isFeatureToggle = $response['data']['attributes']['is_feature_toggle'];
        $recordExperiment = [
            'name' => $experimentName,
            'uid' => $experimentName,
            'alias' => $alias,
            'is_enabled' => $isEnabled,
            'is_feature_toggle' => $isFeatureToggle,
            'owner_id' => $user['id']
        ];
        $recordBranch = [
            'experiment_id' => $experiment['experimentId'],
            'name' => $branchName,
            'uid' => $branchName,
            'percent' => $percent
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
                    'is_enabled' => $isEnabled,
                    'is_feature_toggle' => $isFeatureToggle
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
                                'id' => $experiment['idBranch'],
                                'type' => 'experiment_branches'
                            ]
                        ]
                    ]
                ]
            ],
        ]);

        $I->seeRecord('experiments', $recordExperiment);
        $I->seeRecord('experiment_branches', $recordBranch);
    }

    public function deleteExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $experimentId = $experiment['encodeExperimentId'];
        $experimentName = 'experiment_' . uniqid();
        $branchName = 'branch_' . uniqid();
        $percent = random_int(1,100);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendDelete("/experiments/{$experimentId}", [
            'data' => [
                'id' => $experimentId,
                'type' => 'experiments',
                'attributes' => [
                    'name' => $experimentName,
                ],
                'relationships' => [
                    'branches' => [
                        'data' => [
                            [
                            'id' => $experiment['idBranch'],
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
    
        $response = json_decode($I->grabResponse(), true);

        $recordExperiment = ['name' => $experimentName, 'owner_id' => $user['id']];
        $recordBranch = ['experiment_id' => $experiment['experimentId']];

        $I->seeResponseCodeIsSuccessful(204);

        $I->dontSeeRecord('experiments', $recordExperiment);
        $I->dontSeeRecord('experiment_branches', $recordBranch);
    }

    public function getExperimentsHaveUser(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $userSignature = uniqid();
        $I->experimentsHaveUsers(
            $userSignature,
            $user['id'],
            $experiment['experimentId'],
            $experiment['decodeBranchId']
        );

        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);
        $I->sendGet('experiments/have-user/' . $userSignature);

        $response = json_decode($I->grabResponse(), true);

        $entry = $response['data'][0];

        $I->seeResponseCodeIsSuccessful(200);

        $I->seeResponseContainsJson([
            'data' => [
                [
                    'type' => 'experiments',
                    'id' => $entry['id'],
                    'attributes' => [
                        'name' => $entry['attributes']['name'],
                        'alias' => $entry['attributes']['alias'],
                        'config' => $entry['attributes']['config'],
                        'is_enabled' => true,
                        'is_feature_toggle' => true
                    ],
                    'relationships' => [
                        'owner' => [
                            'data' => [
                                'type' => $entry['relationships']['owner']['data']['type'],
                                'id' => $entry['relationships']['owner']['data']['id']
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function addUserToExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $userSignature = uniqid();
        $I->experimentsHaveUsers(
            $userSignature,
            $user['id'],
            $experiment['experimentId'],
            $experiment['decodeBranchId']
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
                            'id' => $experiment['encodeExperimentId'],
                            'type' => 'experiments'
                        ]
                    ],
                    'branches' => [
                        'data' => [
                            'id' => $experiment['idBranch'],
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
    }
}
