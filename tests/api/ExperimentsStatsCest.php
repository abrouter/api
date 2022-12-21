<?php

class ExperimentsStatsCest
{
    public function _before(ApiTester $I)
    {
    }

    public function showExperimentBranchStatsWithThreeBranches(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);

        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $runExperiment = $I->runExperiments($I, $user['token'], $experiment['alias'], $users);

        foreach ($runExperiment as $branchId) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $branchId);

            $response = json_decode($I->grabResponse(), true);

            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'percentage' => [
                    'visit_mainpage' => $response['percentage']['visit_mainpage'],
                    'open_contact_form' => $response['percentage']['open_contact_form'],
                    'visited_book_call' => $response['percentage']['visited_book_call'],
                    'fill_form_later' => $response['percentage']['fill_form_later'],
                    'form_filler_complete' => $response['percentage']['form_filler_complete'],
                    'visited_nutrionists_page' => $response['percentage']['visited_nutrionists_page'],
                    'skip_call_booking' => $response['percentage']['skip_call_booking'],
                    'thankyou_page' => $response['percentage']['thankyou_page'],
                    'leave' => $response['percentage']['leave'],
                    'sign up' => $response['percentage']['sign up']
                ],
                'counters' => [
                    'incrementalUnique' => [
                        'visit_mainpage' => $response['counters']['incrementalUnique']['visit_mainpage'],
                        'open_contact_form' => $response['counters']['incrementalUnique']['open_contact_form'],
                        'visited_book_call' => $response['counters']['incrementalUnique']['visited_book_call'],
                        'fill_form_later' => $response['counters']['incrementalUnique']['fill_form_later'],
                        'form_filler_complete' => $response['counters']['incrementalUnique']['form_filler_complete'],
                        'visited_nutrionists_page' => $response['counters']['incrementalUnique']['visited_nutrionists_page'],
                        'skip_call_booking' => $response['counters']['incrementalUnique']['skip_call_booking'],
                        'thankyou_page' => $response['counters']['incrementalUnique']['thankyou_page'],
                        'leave' => $response['counters']['incrementalUnique']['leave'],
                        'sign up' => $response['counters']['incrementalUnique']['sign up']
                    ]
                ]
            ]);
        }

    }

    public function showExperimentBranchStatsWithTwoBranches(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $runExperiment = $I->runExperiments($I, $user['token'], $experiment['alias'], $users);

        foreach ($runExperiment as $branchId) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $branchId);

            $response = json_decode($I->grabResponse(), true);

            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'percentage' => [
                    'visit_mainpage' => $response['percentage']['visit_mainpage'],
                    'open_contact_form' => $response['percentage']['open_contact_form'],
                    'visited_book_call' => $response['percentage']['visited_book_call'],
                    'fill_form_later' => $response['percentage']['fill_form_later'],
                    'form_filler_complete' => $response['percentage']['form_filler_complete'],
                    'visited_nutrionists_page' => $response['percentage']['visited_nutrionists_page'],
                    'skip_call_booking' => $response['percentage']['skip_call_booking'],
                    'thankyou_page' => $response['percentage']['thankyou_page'],
                    'leave' => $response['percentage']['leave'],
                    'sign up' => $response['percentage']['sign up']
                ],
                'counters' => [
                    'incrementalUnique' => [
                        'visit_mainpage' => $response['counters']['incrementalUnique']['visit_mainpage'],
                        'open_contact_form' => $response['counters']['incrementalUnique']['open_contact_form'],
                        'visited_book_call' => $response['counters']['incrementalUnique']['visited_book_call'],
                        'fill_form_later' => $response['counters']['incrementalUnique']['fill_form_later'],
                        'form_filler_complete' => $response['counters']['incrementalUnique']['form_filler_complete'],
                        'visited_nutrionists_page' => $response['counters']['incrementalUnique']['visited_nutrionists_page'],
                        'skip_call_booking' => $response['counters']['incrementalUnique']['skip_call_booking'],
                        'thankyou_page' => $response['counters']['incrementalUnique']['thankyou_page'],
                        'leave' => $response['counters']['incrementalUnique']['leave'],
                        'sign up' => $response['counters']['incrementalUnique']['sign up']
                    ]
                ]
            ]);
        }
    }

    public function showExperimentStatsByExperimentIdWithThreeBranches(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->runExperiments($I, $user['token'], $experiment['alias'], $users);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['experimentId']);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(201);

        foreach($response['counters']['incrementalUnique'] as $branch => $event) {
            $I->seeResponseContainsJson([
                'experiment' => [
                    'id' => $response['experiment']['id'],
                    'name' => $response['experiment']['name'],
                    'is_enabled' => $response['experiment']['is_enabled'],
                    'days_running' => $response['experiment']['days_running'],
                    'total_users' => $response['experiment']['total_users']
                ],
                'percentage' => [
                    $branch => [
                        'visit_mainpage' => $response['percentage'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['percentage'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['percentage'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['percentage'][$branch]['fill_form_later'],
                        'form_filler_complete' => $response['percentage'][$branch]['form_filler_complete'],
                        'visited_nutrionists_page' => $response['percentage'][$branch]['visited_nutrionists_page'],
                        'skip_call_booking' => $response['percentage'][$branch]['skip_call_booking'],
                        'thankyou_page' => $response['percentage'][$branch]['thankyou_page'],
                        'leave' => $response['percentage'][$branch]['leave'],
                        'sign up' => $response['percentage'][$branch]['sign up']
                    ]
                ],
                'counters' => [
                    'incrementalUnique' => [
                        $branch => [
                            'visit_mainpage' => $response['counters']['incrementalUnique'][$branch]['visit_mainpage'],
                            'open_contact_form' => $response['counters']['incrementalUnique'][$branch]['open_contact_form'],
                            'visited_book_call' => $response['counters']['incrementalUnique'][$branch]['visited_book_call'],
                            'fill_form_later' => $response['counters']['incrementalUnique'][$branch]['fill_form_later'],
                            'form_filler_complete' => $response['counters']['incrementalUnique'][$branch]['form_filler_complete'],
                            'visited_nutrionists_page' => $response['counters']['incrementalUnique'][$branch]['visited_nutrionists_page'],
                            'skip_call_booking' => $response['counters']['incrementalUnique'][$branch]['skip_call_booking'],
                            'thankyou_page' => $response['counters']['incrementalUnique'][$branch]['thankyou_page'],
                            'leave' => $response['counters']['incrementalUnique'][$branch]['leave'],
                            'sign up' => $response['counters']['incrementalUnique'][$branch]['sign up']
                        ]
                    ]
                ]
            ]);
        }
    }

    public function showExperimentStatsByExperimentIdWithTwoBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->runExperiments($I, $user['token'], $experiment['alias'], $users);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['experimentId']);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(201);

        foreach($response['counters']['incrementalUnique'] as $branch => $event) {
            $I->seeResponseContainsJson([
                'experiment' => [
                    'id' => $response['experiment']['id'],
                    'name' => $response['experiment']['name'],
                    'is_enabled' => $response['experiment']['is_enabled'],
                    'days_running' => $response['experiment']['days_running'],
                    'total_users' => $response['experiment']['total_users']
                ],
                'percentage' => [
                    $branch => [
                        'visit_mainpage' => $response['percentage'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['percentage'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['percentage'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['percentage'][$branch]['fill_form_later'],
                        'form_filler_complete' => $response['percentage'][$branch]['form_filler_complete'],
                        'visited_nutrionists_page' => $response['percentage'][$branch]['visited_nutrionists_page'],
                        'skip_call_booking' => $response['percentage'][$branch]['skip_call_booking'],
                        'thankyou_page' => $response['percentage'][$branch]['thankyou_page'],
                        'leave' => $response['percentage'][$branch]['leave'],
                        'sign up' => $response['percentage'][$branch]['sign up']
                    ]
                ],
                'counters' => [
                    'incrementalUnique' => [
                        $branch => [
                            'visit_mainpage' => $response['counters']['incrementalUnique'][$branch]['visit_mainpage'],
                            'open_contact_form' => $response['counters']['incrementalUnique'][$branch]['open_contact_form'],
                            'visited_book_call' => $response['counters']['incrementalUnique'][$branch]['visited_book_call'],
                            'fill_form_later' => $response['counters']['incrementalUnique'][$branch]['fill_form_later'],
                            'form_filler_complete' => $response['counters']['incrementalUnique'][$branch]['form_filler_complete'],
                            'visited_nutrionists_page' => $response['counters']['incrementalUnique'][$branch]['visited_nutrionists_page'],
                            'skip_call_booking' => $response['counters']['incrementalUnique'][$branch]['skip_call_booking'],
                            'thankyou_page' => $response['counters']['incrementalUnique'][$branch]['thankyou_page'],
                            'leave' => $response['counters']['incrementalUnique'][$branch]['leave'],
                            'sign up' => $response['counters']['incrementalUnique'][$branch]['sign up']
                        ]
                    ]
                ]
            ]);
        }
    }

    public function showExperimentStatsByAliasWithThreeBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->runExperiments($I, $user['token'], $experiment['alias'], $users);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['alias']);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(201);

        foreach($response['counters']['incrementalUnique'] as $branch => $event) {
            $I->seeResponseContainsJson([
                'experiment' => [
                    'id' => $response['experiment']['id'],
                    'name' => $response['experiment']['name'],
                    'is_enabled' => $response['experiment']['is_enabled'],
                    'days_running' => $response['experiment']['days_running'],
                    'total_users' => $response['experiment']['total_users']
                ],
                'percentage' => [
                    $branch => [
                        'visit_mainpage' => $response['percentage'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['percentage'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['percentage'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['percentage'][$branch]['fill_form_later'],
                        'form_filler_complete' => $response['percentage'][$branch]['form_filler_complete'],
                        'visited_nutrionists_page' => $response['percentage'][$branch]['visited_nutrionists_page'],
                        'skip_call_booking' => $response['percentage'][$branch]['skip_call_booking'],
                        'thankyou_page' => $response['percentage'][$branch]['thankyou_page'],
                        'leave' => $response['percentage'][$branch]['leave'],
                        'sign up' => $response['percentage'][$branch]['sign up']
                    ]
                ],
                'counters' => [
                    'incrementalUnique' => [
                        $branch => [
                            'visit_mainpage' => $response['counters']['incrementalUnique'][$branch]['visit_mainpage'],
                            'open_contact_form' => $response['counters']['incrementalUnique'][$branch]['open_contact_form'],
                            'visited_book_call' => $response['counters']['incrementalUnique'][$branch]['visited_book_call'],
                            'fill_form_later' => $response['counters']['incrementalUnique'][$branch]['fill_form_later'],
                            'form_filler_complete' => $response['counters']['incrementalUnique'][$branch]['form_filler_complete'],
                            'visited_nutrionists_page' => $response['counters']['incrementalUnique'][$branch]['visited_nutrionists_page'],
                            'skip_call_booking' => $response['counters']['incrementalUnique'][$branch]['skip_call_booking'],
                            'thankyou_page' => $response['counters']['incrementalUnique'][$branch]['thankyou_page'],
                            'leave' => $response['counters']['incrementalUnique'][$branch]['leave'],
                            'sign up' => $response['counters']['incrementalUnique'][$branch]['sign up']
                        ]
                    ]
                ]
            ]);
        }
    }

    public function showExperimentStatsByAliasWithTwoBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->runExperiments($I, $user['token'], $experiment['alias'], $users);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['alias']);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(201);

        foreach($response['counters']['incrementalUnique'] as $branch => $event) {
            $I->seeResponseContainsJson([
                'experiment' => [
                    'id' => $response['experiment']['id'],
                    'name' => $response['experiment']['name'],
                    'is_enabled' => $response['experiment']['is_enabled'],
                    'days_running' => $response['experiment']['days_running'],
                    'total_users' => $response['experiment']['total_users']
                ],
                'percentage' => [
                    $branch => [
                        'visit_mainpage' => $response['percentage'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['percentage'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['percentage'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['percentage'][$branch]['fill_form_later']
                    ]
                ],
                'counters' => [
                    'incrementalUnique' => [
                        $branch => [
                            'visit_mainpage' => $response['counters']['incrementalUnique'][$branch]['visit_mainpage'],
                            'open_contact_form' => $response['counters']['incrementalUnique'][$branch]['open_contact_form'],
                            'visited_book_call' => $response['counters']['incrementalUnique'][$branch]['visited_book_call'],
                            'fill_form_later' => $response['counters']['incrementalUnique'][$branch]['fill_form_later']
                        ]
                    ]
                ]
            ]);
        }
    }

    public function showExperimentStatsByIncrementalUniqueAndSummarizableEvents(ApiTester $I)
    {
        $users = [];

        $unsavedEvents = [
            ['type' => 'incremental-unique', 'event_name' => 'view_contact_form'],
            ['type' => 'incremental-unique', 'event_name' => 'visit_mainpage'],
            ['type' => 'summarizable', 'event_name' => 'revenue']
        ];
        $today = (new \DateTime());
        $yesterday = (new \DateTime())
            ->add(new DateInterval('P1D'));

        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);

        $I->createEventWithSpecificType($user['id'], $unsavedEvents);

        $users[] = $I->createEventsWithTypeIncremental($user['id'], $unsavedEvents[0]['event_name'], 10);
        $users[] = $I->createEventsWithTypeIncremental($user['id'], $unsavedEvents[1]['event_name'], 20);
        $users[] = $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            1,
            100,
            $today->format('Y-m-d')
        );
        $users[] = $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            2,
            100,
            $yesterday->format('Y-m-d')
        );
        $users[] = $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            2,
            200,
            $yesterday->format('Y-m-d')
        );
        $users[] = $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            3,
            400,
            $today->format('Y-m-d')
        );

        $users = collect($users)
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['decodeExperimentId'],
            $experiment['decodeBranchId'],
            $users
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['experimentId'] .
            '&filter[date_from]=' .
            $today->format('m-d-Y') .
            '&filter[date_to]=' . $yesterday->format('m-d-Y')
        );

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(200);

        $I->seeResponseContainsJson([
            'experiment' => [
                'id' => $response['experiment']['id'],
                'name' => $experiment['alias'],
                'is_enabled' => true,
                'days_running' => 0,
                'total_users' => 20
            ],
            'percentage' => [
                'branch_first' => [
                    'view_contact_form' => 100,
                    'visit_mainpage' => 100
                ],
                'branch_second' => [
                    'visit_mainpage' => 100
                ]
            ],
            'counters' => [
                'incrementalUnique' => [
                    'branch_first' => [
                        'view_contact_form' => [$today->format('Y-m-d') => 10],
                        'visit_mainpage' => [$today->format('Y-m-d') => 10],
                    ],
                    'branch_second' => [
                        'visit_mainpage' => [$today->format('Y-m-d') => 10],
                    ]
                ],
                'summarizable' => [
                    'branch_first' => [
                        'revenue' =>[
                            $yesterday->format('Y-m-d') => 300,
                            $today->format('Y-m-d') => 500
                        ]
                    ],
                    'branch_second' => [
                        'revenue' =>[
                            $yesterday->format('Y-m-d') => 300,
                            $today->format('Y-m-d') => 500
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function showExperimentStatsByIncrementalEvents(ApiTester $I)
    {
        $users = [];

        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'view_contact_form'],
            ['type' => 'incremental', 'event_name' => 'visit_mainpage'],
        ];
        $today = (new \DateTime());
        $yesterday = (new \DateTime())
            ->add(new DateInterval('P1D'));

        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);

        $I->createEventWithSpecificType($user['id'], $unsavedEvents);

        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            5,
            $today->format('Y-m-d')
        );
        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[1]['event_name'],
            4,
            $yesterday->format('Y-m-d')
        );
        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            2,
            $yesterday->format('Y-m-d')
        );
        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[1]['event_name'],
            3,
            $today->format('Y-m-d')
        );

        $users = collect($users)
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['decodeExperimentId'],
            $experiment['decodeBranchId'],
            $users
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['experimentId'] .
            '&filter[date_from]=' .
            $today->format('m-d-Y') .
            '&filter[date_to]=' . $yesterday->format('m-d-Y')
        );

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(200);

        $I->seeResponseContainsJson([
            'experiment' => [
                'id' => $response['experiment']['id'],
                'name' => $experiment['alias'],
                'is_enabled' => true,
                'days_running' => 0,
                'total_users' => 5
            ],
            'counters' => [
                'incremental' => [
                    'branch_first' => [
                        'view_contact_form' => [
                            $today->format('Y-m-d') => 5,
                            $yesterday->format('Y-m-d') => 2
                        ],
                        'visit_mainpage' => [
                            $today->format('Y-m-d') => 3,
                            $yesterday->format('Y-m-d') => 4
                        ],
                    ]
                ]
            ]
        ]);
    }

    public function showExperimentStatsByIncrementalAndIncrementalUniqueEvents(ApiTester $I)
    {
        $users = [];

        $unsavedEvents = [
            ['type' => 'incremental-unique', 'event_name' => 'view_contact_form'],
            ['type' => 'incremental-unique', 'event_name' => 'visit_mainpage'],
            ['type' => 'incremental', 'event_name' => 'send_message_button'],
            ['type' => 'incremental', 'event_name' => 'view_phone_number_button'],
        ];
        $today = (new \DateTime());
        $yesterday = (new \DateTime())
            ->add(new DateInterval('P1D'));

        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);

        $I->createEventWithSpecificType($user['id'], $unsavedEvents);

        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            5,
        );
        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[1]['event_name'],
            4,
        );
        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            3,
        );
        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            3,
        );
        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[3]['event_name'],
            5,
        );
        $users[] = $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[3]['event_name'],
            2,
        );

        $users = collect($users)
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['decodeExperimentId'],
            $experiment['decodeBranchId'],
            $users
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['experimentId'] .
            '&filter[date_from]=' .
            $today->format('m-d-Y') .
            '&filter[date_to]=' . $yesterday->format('m-d-Y')
        );

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(200);

        $I->seeResponseContainsJson([
            'experiment' => [
                'id' => $response['experiment']['id'],
                'name' => $experiment['alias'],
                'is_enabled' => true,
                'days_running' => 0,
                'total_users' => 5
            ],
            'counters' => [
                'incrementalUnique' => [
                    'branch_first' => [
                        'view_contact_form' => [
                            $today->format('Y-m-d') => 5,
                        ],
                        'visit_mainpage' => [
                            $today->format('Y-m-d') => 4,
                        ]
                    ]
                ],
                'incremental' => [
                    'branch_first' => [
                        'send_message_button' => [
                            $today->format('Y-m-d') => 6,
                        ],
                        'view_phone_number_button' => [
                            $today->format('Y-m-d') => 7,
                        ]
                    ]
                ]
            ]
        ]);
    }
}
