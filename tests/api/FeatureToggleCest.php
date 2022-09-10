<?php

use Modules\Core\EntityId\EntityEncoder;

class FeatureToggleCest
{
    public function _before(ApiTester $I)
    {
    }

    public function create(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $featureToggleName = 'feature-toggle' . uniqid();
        $branchNameFirst = 'ON';
        $branchNameSecond = 'OFF';
        $percentFirst = 0;
        $percentSecond = 100;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/feature-toggles', [
            'data' => [
                'type' => 'feature-toggles',
                'attributes' => [
                    'name' => $featureToggleName,
                    'alias' => $featureToggleName,
                    'is_enabled' => true,
                    'is_feature_toggle' => true,
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
                ]
            ]
        ]);
    
        $response = json_decode($I->grabResponse(), true);

        $featureToggleId = (new EntityEncoder())->decode($response['data']['id'], 'experiments');
        $alias = $response['data']['attributes']['alias'];
        $config = $response['data']['attributes']['config'];
        $isEnabled = $response['data']['attributes']['is_enabled'];
        $isFeatureToggle = $response['data']['attributes']['is_feature_toggle'];
        $recordExperiment = ['name' => $featureToggleName, 'alias' => $alias, 'is_enabled' => $isEnabled, 'is_feature_toggle' => $isFeatureToggle, 'owner_id' => $user['id']];

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'experiments',
                'attributes' => [
                    'name' => $featureToggleName,
                    'alias' => $alias,
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
                            ]
                        ]
                    ]
                ]
            ],
        ]);

        $I->seeRecord('experiments', $recordExperiment);

        $branches = [$branchNameFirst, $branchNameSecond];
        $percents = [$percentFirst, $percentSecond];
        
        for ($n = 0; $n < count($branches); $n++) {
            $recordBranch = ['experiment_id' => $featureToggleId,'name' => $branches[$n], 'uid' => $branches[$n], 'percent' => $percents[$n]];
            $I->seeRecord('experiment_branches', $recordBranch);
        }
    }

    public function run(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveFeatureToggle($user['id']);
        $userSignature = 'user_' . uniqid();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/feature-toggles/run', [
            'data' => [
                'type' => 'feature-toggles-run',
                'attributes' => [
                    'userSignature' => $userSignature
                ],
                'relationships' => [
                    'feature-toggle' => [
                        'data' => [
                            'id' => $experiment['alias'],
                            'type' => 'feature-toggles'
                        ]
                    ]
                ]
            ],
        ]);

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'type' => 'feature-toggle-result',
                'id' => $response['data']['id'],
                'attributes' => [
                    'run-uid' => 'feature-toggle-experiment-ON' ?? 'feature-toggle-experiment-OFF',
                    'branch-uid' => 'ON' ?? 'OFF',
                    'experiment-uid' => 'feature-toggle-experiment',
                    'is_enabled' => true,
                ],
                'relationships' => [
                    'experiment_user' => [
                        'data' => [
                            'type' => 'experiment_users',
                            'id' => $response['data']['relationships']['experiment_user']['data']['id']
                        ]
                    ],
                    'experiment_id' => [
                        'data' => [
                            'type' => 'experiments',
                            'id' => $response['data']['relationships']['experiment_id']['data']['id']
                        ]
                    ],
                    'experiment_branch_id' => [
                        'data' => [
                            'type' => 'experiment_branches',
                            'id' => $experiment['idBranch'][0] ?? $experiment['idBranch'][1]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'type' => 'experiment_branches',
                    'id' => $experiment['idBranch'][0] ?? $experiment['idBranch'][1],
                    'attributes' => [
                        "name" => 'ON' ?? 'OFF',
                        "uid" => 'ON' ?? 'OFF',
                        "percent" => 100 ?? 0
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

    public function createIncorectFeatureToggleWithTwoBranchesZeroPercent(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $featureToggleName = 'feature-toggle' . uniqid();
        $branchNameFirst = 'ON';
        $branchNameSecond = 'OFF';
        $percentFirst = 0;
        $percentSecond = 0;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/feature-toggles', [
            'data' => [
                'type' => 'feature-toggles',
                'attributes' => [
                    'name' => $featureToggleName,
                    'is_enabled' => true,
                    'is_feature_toggle' => true,
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
                ]
            ]
        ]);
    
        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIs(422);
    }

    public function createIncorectFeatureToggleWithTwoBranchesHundredPercent(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $featureToggleName = 'feature-toggle' . uniqid();
        $branchNameFirst = 'ON';
        $branchNameSecond = 'OFF';
        $percentFirst = 100;
        $percentSecond = 100;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/feature-toggles', [
            'data' => [
                'type' => 'feature-toggles',
                'attributes' => [
                    'name' => $featureToggleName,
                    'is_enabled' => true,
                    'is_feature_toggle' => true,
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
                ]
            ]
        ]);
    
        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIs(422);
    }

    public function delete(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $featureToggle = $I->haveFeatureToggle($user['id']);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendDelete('/feature-toggles/' . $featureToggle['encodeExperimentId'], [
            'data' => [
                'id' => $featureToggle['encodeExperimentId'],
                'type' => 'feature-toggles',
                'attributes' => [
                    'name' => $featureToggle['name'],
                ],
                'relationships' => [
                    'branches' => [
                        'data' => [
                            [
                                'id' => $featureToggle['idBranch'][0],
                                'type' => 'experiment_branches',
                            ],
                            [
                                'id' => $featureToggle['idBranch'][1],
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

        $recordExperiment = ['name' => $featureToggle['name'], 'owner_id' => $user['id']];

        $I->seeResponseCodeIsSuccessful(204);

        $I->dontSeeRecord('experiments', $recordExperiment);
        
        for ($n = 0; $n < count($featureToggle['idBranch']); $n++) {
            $recordBranch = ['experiment_id' => $featureToggle['experimentId']];
            $I->dontSeeRecord('experiment_branches', $recordBranch);
        }
    }
}
