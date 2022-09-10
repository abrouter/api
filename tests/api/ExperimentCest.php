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
        $percentFirst = 50;
        $percentSecond = 10;
        $percentThird = 40;

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
        $experimentBranch = $I->haveBranch($experiment['experimentId']);
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
                    'run-uid' => $experiment['alias'] . '-' . $experimentBranch['branchName'],
                    'branch-uid' => $experimentBranch['branchName'],
                    'experiment-uid' => $experiment['alias'] ,
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
                            'id' => $experiment['encodeExperimentId']
                        ]
                    ],
                    'experiment_branch_id' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $experimentBranch['encodeBranchId']
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'type' => 'experiment_branches',
                    'id' => $experimentBranch['encodeBranchId'],
                    'attributes' => [
                        'name' => $experimentBranch['branchName'],
                        'uid' => $experimentBranch['branchName'],
                        'percent' => 100
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $experiment['encodeExperimentId']
                            ]       
                        ]
                    ]
                ]
            ]
        ]);

        $experimentUserId = (new EntityEncoder())
            ->decode(
                $response['data']['relationships']['experiment_user']['data']['id'], 'experiment_users'
            );
        $experimentBranchId = (new EntityEncoder())
            ->decode(
                $response['data']['relationships']['experiment_branch_id']['data']['id'], 'experiment_branches'
            );

        $experimentId = $experiment['experimentId'];

        $recordBranchUsers = [
            'experiment_user_id' => $experimentUserId,
            'experiment_id' => $experimentId,
            'experiment_branch_id' => $experimentBranchId,
            'owner_id' => $user['id'],
        ];
        $recordExperimentUsers = ['owner_id' => $user['id'], 'user_signature' => $userSignature];

        $I->seeRecord('experiment_user_branches', $recordBranchUsers);
        $I->seeRecord('experiment_users', $recordExperimentUsers);
    }


    /**
     * ****
     * !!!! FIX RELATED USERS TESTS !!!!
     * ***
     */
//    public function runExperimentWithRelatedUser(ApiTester $I)
//    {
//        $user = $I->haveUser($I);
//        $experiment = $I->haveExperiment($user['id']);
//        $experimentBranchFirst = $I->haveBranch($experiment['experimentId']);
//        $experimentBranchSecond = $I->haveBranch($experiment['experimentId'], 0);
//
//        $usersIds = $I->createEventsWithTypeIncremental(
//            $user['id'],
//            'test',
//            5,
//        );
//
//        $I->haveConductedExperiments(
//            $user['id'],
//            $experiment['experimentId'],
//            (array) $experimentBranchSecond['branchId'],
//            $usersIds
//        );
//
//        $event = $I->grabRecord('events', ['user_id' => $usersIds[0]]);
//        $userSignature = $event['temporary_user_id'];
//
//        $I->haveHttpHeader('Content-Type', 'application/json');
//        $I->haveHttpHeader('Accept', 'application/json');
//        $I->amBearerAuthenticated($user['token']);
//
//        $I->sendPost('/experiment/run', [
//            'data' => [
//                'type' => 'experiment-run',
//                'attributes' => [
//                    'userSignature' => $userSignature
//                ],
//                'relationships' => [
//                    'experiment' => [
//                        'data' => [
//                            'id' => $experiment['encodeExperimentId'],
//                            'type' => 'experiments'
//                        ]
//                    ]
//                ]
//            ]
//        ]);
//
//        $response = json_decode($I->grabResponse(), true);
//
//        $I->seeResponseCodeIsSuccessful(201);
//        $I->seeResponseContainsJson([
//            'data' => [
//                'type' => 'experiment_branch_users',
//                'id' => $response['data']['id'],
//                'attributes' => [
//                    'run-uid' => $experiment['alias'] . '-' . $experimentBranchSecond['branchName'],
//                    'branch-uid' => $experimentBranchSecond['branchName'],
//                    'experiment-uid' => $experiment['alias'] ,
//                ],
//                'relationships' => [
//                    'experiment_user' => [
//                        'data' => [
//                            'type' => 'experiment_user',
//                            'id' => $response['data']['relationships']['experiment_user']['data']['id']
//                        ]
//                    ],
//                    'experiment_id' => [
//                        'data' => [
//                            'type' => 'users',
//                            'id' => $experiment['encodeExperimentId']
//                        ]
//                    ],
//                    'experiment_branch_id' => [
//                        'data' => [
//                            'type' => 'users',
//                            'id' => $experimentBranchSecond['encodeBranchId']
//                        ]
//                    ]
//                ]
//            ],
//            'included' => [
//                [
//                    'type' => 'experiment_branches',
//                    'id' => $experimentBranchSecond['encodeBranchId'],
//                    'attributes' => [
//                        'name' => $experimentBranchSecond['branchName'],
//                        'uid' => $experimentBranchSecond['branchName'],
//                        'percent' => 0
//                    ],
//                    'relationships' => [
//                        'experiment' => [
//                            'data' => [
//                                'type' => 'users',
//                                'id' => $experiment['encodeExperimentId']
//                            ]
//                        ]
//                    ]
//                ]
//            ]
//        ]);
//
//        $experimentUserId = (new EntityEncoder())
//            ->decode(
//                $response['data']['relationships']['experiment_user']['data']['id'], 'experiment_users'
//            );
//        $experimentBranchId = (new EntityEncoder())
//            ->decode(
//                $response['data']['relationships']['experiment_branch_id']['data']['id'], 'experiment_branches'
//            );
//
//        $experimentId = $experiment['experimentId'];
//
//        $recordBranchUsers = [
//            'experiment_user_id' => $experimentUserId,
//            'experiment_id' => $experimentId,
//            'experiment_branch_id' => $experimentBranchId
//        ];
//        $recordExperimentUsers = ['owner_id' => $user['id'], 'user_signature' => $event['user_id']];
//
//        $I->seeRecord('experiment_user_branches', $recordBranchUsers);
//        $I->seeRecord('experiment_users', $recordExperimentUsers);
//    }

    public function updateExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $experimentBranch = $I->haveBranch($experiment['experimentId']);
        $experimentId = $experiment['encodeExperimentId'];
        $experimentName = 'experiment_' . uniqid();
        $branchName = 'branch_' . uniqid();
        $percent = 100;

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
                            'id' => $experimentBranch['encodeBranchId'],
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
                    'id' => $experimentBranch['encodeBranchId'],
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

        $recordExperiment = [
            'name' => $experimentName,
            'uid' => $experimentName,
            'alias' => $experimentName,
            'is_enabled' => true,
            'is_feature_toggle' => false,
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
                    'alias' => $experimentName,
                    'config' => [],
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
                                'id' => $experimentBranch['encodeBranchId'],
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
        $experimentBranch = $I->haveBranch($experiment['experimentId']);
        $experimentId = $experiment['encodeExperimentId'];

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
                            'id' => $experimentBranch['encodeBranchId'],
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

        $recordExperiment = ['name' => $experiment['name'], 'owner_id' => $user['id']];
        $recordBranch = ['experiment_id' => $experiment['experimentId']];

        $I->seeResponseCodeIsSuccessful(204);

        $I->dontSeeRecord('experiments', $recordExperiment);
        $I->dontSeeRecord('experiment_branches', $recordBranch);
    }

    public function getUserExperiments(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $experiment = $I->haveExperiment($user['id']);
        $experimentBranch = $I->haveBranch($experiment['experimentId']);
        $userSignature = uniqid();

        $I->experimentsHaveUsers(
            $userSignature,
            $user['id'],
            $experiment['experimentId'],
            $experimentBranch['branchId']
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
                        'run-uid' => $experiment['alias'] . '-' . $experimentBranch['branchName'],
                        'branch-uid' => $experimentBranch['branchName'],
                        'experiment-uid' => $experiment['alias'],
                    ],
                ]
            ]
        ]);
    }

    public function addUserToExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $experimentBranch = $I->haveBranch($experiment['experimentId']);
        $userSignature = uniqid();
        $I->experimentsHaveUsers(
            $userSignature,
            $user['id'],
            $experiment['experimentId'],
            $experimentBranch['branchId']
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
                            'id' => $experimentBranch['encodeBranchId'],
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
                    'id' => $experimentBranch['encodeBranchId'],
                    'attributes' => [
                        'name' => $experimentBranch['branchName'],
                        'uid' => $experimentBranch['branchName'],
                        'percent' => 100
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $experiment['encodeExperimentId']
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function reAddingUserToExperiment(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperiment($user['id']);
        $experimentBranchFirst = $I->haveBranch($experiment['experimentId']);
        $experimentBranchSecond = $I->haveBranch($experiment['experimentId']);
        $userSignature = uniqid();
        $I->experimentsHaveUsers(
            $userSignature,
            $user['id'],
            $experiment['experimentId'],
            $experimentBranchFirst['branchId']
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('experiments/add-user', [
            'data' => [
                'type' => 'experiment_users',
                'attributes' => [
                    'user_signature' => $userSignature,
                    'forced' => true,
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
                            'id' => $experimentBranchSecond['encodeBranchId'],
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
                    'run-uid' => $experiment['alias'] . '-' . $experimentBranchSecond['branchName'],
                    'branch-uid' => $experimentBranchSecond['branchName'],
                    'experiment-uid' => $experiment['alias'],
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
                    'id' => $experimentBranchSecond['encodeBranchId'],
                    'attributes' => [
                        'name' => $experimentBranchSecond['branchName'],
                        'uid' => $experimentBranchSecond['branchName'],
                        'percent' => 100
                    ],
                    'relationships' => [
                        'experiment' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $experiment['encodeExperimentId']
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function allUsersExperiments(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiments = [
            'first' => $I->haveExperiment($user['id']),
            'second' => $I->haveExperiment($user['id']),
            'third' => $I->haveExperiment($user['id'])
        ];

        $experimentsBranch = [
            'first' => $I->haveBranch($experiments['first']['experimentId']),
            'second' => $I->haveBranch($experiments['second']['experimentId']),
            'third' => $I->haveBranch($experiments['third']['experimentId'])
        ];

        $userSignature = [];

        foreach ($experimentsBranch as $key => $experimentBranch) {
            $userSignature[$key] = uniqid();
            $I->experimentsHaveUsers(
                $userSignature[$key],
                $user['id'],
                $experiments[$key]['experimentId'],
                $experimentBranch['branchId']
            );
        }

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('all-users-experiments');

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            $userSignature['first'] => [
                $experiments['first']['name'] => $experimentsBranch['first']['branchName']
            ],
            $userSignature['second'] => [
                $experiments['second']['name'] => $experimentsBranch['second']['branchName']
            ],
            $userSignature['third'] => [
                $experiments['third']['name'] => $experimentsBranch['third']['branchName']
            ]
        ]);
    }
}
