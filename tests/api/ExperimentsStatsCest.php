<?php

use Modules\Core\EntityId\EntityEncoder;

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

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['experimentId'],
            3,
            $experiment['idBranch'],
            array_unique($users)
        );

        foreach ($experiment['idBranch'] as $branchId) {
            $encodeBranchesIds[] = (new EntityEncoder())
                ->encode($branchId, 'experiment_branches');
        }

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $encodeBranchesIds[0]);

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ],
            'counters' => [
                'visit_mainpage' => 4,
                'open_contact_form' => 4,
                'visited_book_call' => 4,
                'fill_form_later' => 4,
                'form_filler_complete' => 4,
                'visited_nutrionists_page' => 4,
                'skip_call_booking' => 4,
                'thankyou_page' => 4,
                'leave' => 4,
                'sign up' => 4
            ]
        ]);

        $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $encodeBranchesIds[1]);

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ],
            'counters' => [
                'visit_mainpage' => 3,
                'open_contact_form' => 3,
                'visited_book_call' => 3,
                'fill_form_later' => 3,
                'form_filler_complete' => 3,
                'visited_nutrionists_page' => 3,
                'skip_call_booking' => 3,
                'thankyou_page' => 3,
                'leave' => 3,
                'sign up' => 3
            ]
        ]);

        $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $encodeBranchesIds[2]);

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ],
            'counters' => [
                'visit_mainpage' => 3,
                'open_contact_form' => 3,
                'visited_book_call' => 3,
                'fill_form_later' => 3,
                'form_filler_complete' => 3,
                'visited_nutrionists_page' => 3,
                'skip_call_booking' => 3,
                'thankyou_page' => 3,
                'leave' => 3,
                'sign up' => 3
            ]
        ]);
    }

    public function showExperimentBranchStatsWithTwoBranches(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['decodeExperimentId'],
            2,
            $experiment['decodeBranchId'],
            array_unique($users)
        );

        $encodeBranchesIds = [];

        foreach ($experiment['decodeBranchId'] as $branchId) {
            $encodeBranchesIds[] = (new EntityEncoder())
                ->encode($branchId, 'experiment_branches');
        }

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $encodeBranchesIds[0]);

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ],
            'counters' => [
                'visit_mainpage' => 5,
                'open_contact_form' => 5,
                'visited_book_call' => 5,
                'fill_form_later' => 5,
                'form_filler_complete' => 5,
                'visited_nutrionists_page' => 5,
                'skip_call_booking' => 5,
                'thankyou_page' => 5,
                'leave' => 5,
                'sign up' => 5
            ]
        ]);

        $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $encodeBranchesIds[1]);

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ],
            'counters' => [
                'visit_mainpage' => 5,
                'open_contact_form' => 5,
                'visited_book_call' => 5,
                'fill_form_later' => 5,
                'form_filler_complete' => 5,
                'visited_nutrionists_page' => 5,
                'skip_call_booking' => 5,
                'thankyou_page' => 5,
                'leave' => 5,
                'sign up' => 5
            ]
        ]);
    }

    public function showExperimentStatsByExperimentIdWithThreeBranches(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);;

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['experimentId'],
            3,
            $experiment['idBranch'],
            array_unique($users)
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $encodeExperimentId = (new EntityEncoder())->encode($experiment['experimentId'], 'experiments');
        $date = (new \DateTime())->format('Y-m-d');

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $encodeExperimentId);

        $I->seeResponseCodeIsSuccessful(201);

        $I->seeResponseContainsJson([
            'experiment' => [
                'id' => $experiment['experimentId'],
                'name' => $experiment['alias'],
                'is_enabled' => true,
                'days_running' => 0,
                'total_users' => 10
            ],
            'percentage' => [
                'branch_first' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ],
                'branch_second' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ],
                'branch_third' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ]
            ],
            'eventCountersWithDate' => [
                'branch_first' => [
                    'visit_mainpage' => [$date => 4],
                    'open_contact_form' => [$date => 4],
                    'visited_book_call' => [$date => 4],
                    'fill_form_later' => [$date => 4],
                    'form_filler_complete' => [$date => 4],
                    'visited_nutrionists_page' => [$date => 4],
                    'skip_call_booking' => [$date => 4],
                    'thankyou_page' => [$date => 4],
                    'leave' => [$date => 4],
                    'sign up' => [$date => 4]
                ],
                'branch_second' => [
                    'visit_mainpage' => [$date => 3],
                    'open_contact_form' => [$date => 3],
                    'visited_book_call' => [$date => 3],
                    'fill_form_later' => [$date => 3],
                    'form_filler_complete' => [$date => 3],
                    'visited_nutrionists_page' => [$date => 3],
                    'skip_call_booking' => [$date => 3],
                    'thankyou_page' => [$date => 3],
                    'leave' => [$date => 3],
                    'sign up' => [$date => 3]
                ],
                'branch_third' => [
                    'visit_mainpage' => [$date => 3],
                    'open_contact_form' => [$date => 3],
                    'visited_book_call' => [$date => 3],
                    'fill_form_later' => [$date => 3],
                    'form_filler_complete' => [$date => 3],
                    'visited_nutrionists_page' => [$date => 3],
                    'skip_call_booking' => [$date => 3],
                    'thankyou_page' => [$date => 3],
                    'leave' => [$date => 3],
                    'sign up' => [$date => 3]
                ]
            ]
        ]);
    }

    public function showExperimentStatsByExperimentIdWithTwoBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['decodeExperimentId'],
            2,
            $experiment['decodeBranchId'],
            array_unique($users)
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['experimentId']);

        $date = (new \DateTime())->format('Y-m-d');

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'experiment' => [
                'id' => $experiment['decodeExperimentId'],
                'name' => $experiment['alias'],
                'is_enabled' => true,
                'days_running' => 0,
                'total_users' => 10
            ],
            'percentage' => [
                'branch_first' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ],
                'branch_second' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ]
            ],
            'eventCountersWithDate' => [
                'branch_first' => [
                    'visit_mainpage' => [$date => 5],
                    'open_contact_form' => [$date => 5],
                    'visited_book_call' => [$date => 5],
                    'fill_form_later' => [$date => 5],
                    'form_filler_complete' => [$date => 5],
                    'visited_nutrionists_page' => [$date => 5],
                    'skip_call_booking' => [$date => 5],
                    'thankyou_page' => [$date => 5],
                    'leave' => [$date => 5],
                    'sign up' => [$date => 5]
                ],
                'branch_second' => [
                    'visit_mainpage' => [$date => 5],
                    'open_contact_form' => [$date => 5],
                    'visited_book_call' => [$date => 5],
                    'fill_form_later' => [$date => 5],
                    'form_filler_complete' => [$date => 5],
                    'visited_nutrionists_page' => [$date => 5],
                    'skip_call_booking' => [$date => 5],
                    'thankyou_page' => [$date => 5],
                    'leave' => [$date => 5],
                    'sign up' => [$date => 5]
                ]
            ]
        ]);
    }

    public function showExperimentStatsByAliasWithThreeBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['experimentId'],
            3,
            $experiment['idBranch'],
            array_unique($users)
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['alias']);

        $date = (new \DateTime())->format('Y-m-d');

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'experiment' => [
                'id' => $experiment['experimentId'],
                'name' => $experiment['alias'],
                'is_enabled' => true,
                'days_running' => 0,
                'total_users' => 10
            ],
            'percentage' => [
                'branch_first' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ],
                'branch_second' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ],
                'branch_third' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ]
            ],
            'eventCountersWithDate' => [
                'branch_first' => [
                    'visit_mainpage' => [$date => 4],
                    'open_contact_form' => [$date => 4],
                    'visited_book_call' => [$date => 4],
                    'fill_form_later' => [$date => 4],
                    'form_filler_complete' => [$date => 4],
                    'visited_nutrionists_page' => [$date => 4],
                    'skip_call_booking' => [$date => 4],
                    'thankyou_page' => [$date => 4],
                    'leave' => [$date => 4],
                    'sign up' => [$date => 4]
                ],
                'branch_second' => [
                    'visit_mainpage' => [$date => 3],
                    'open_contact_form' => [$date => 3],
                    'visited_book_call' => [$date => 3],
                    'fill_form_later' => [$date => 3],
                    'form_filler_complete' => [$date => 3],
                    'visited_nutrionists_page' => [$date => 3],
                    'skip_call_booking' => [$date => 3],
                    'thankyou_page' => [$date => 3],
                    'leave' => [$date => 3],
                    'sign up' => [$date => 3]
                ],
                'branch_third' => [
                    'visit_mainpage' => [$date => 3],
                    'open_contact_form' => [$date => 3],
                    'visited_book_call' => [$date => 3],
                    'fill_form_later' => [$date => 3],
                    'form_filler_complete' => [$date => 3],
                    'visited_nutrionists_page' => [$date => 3],
                    'skip_call_booking' => [$date => 3],
                    'thankyou_page' => [$date => 3],
                    'leave' => [$date => 3],
                    'sign up' => [$date => 3]
                ]
            ]
        ]);
    }

    public function showExperimentStatsByAliasWithTwoBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['decodeExperimentId'],
            2,
            $experiment['decodeBranchId'],
            array_unique($users)
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['alias']);

        $date = (new DateTime())->format('Y-m-d');

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'experiment' => [
                'id' => $experiment['decodeExperimentId'],
                'name' => $experiment['alias'],
                'is_enabled' => true,
                'days_running' => 0,
                'total_users' => 10
            ],
            'percentage' => [
                'branch_first' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ],
                'branch_second' => [
                    'visit_mainpage' => 100,
                    'open_contact_form' => 100,
                    'visited_book_call' => 100,
                    'fill_form_later' => 100,
                    'form_filler_complete' => 100,
                    'visited_nutrionists_page' => 100,
                    'skip_call_booking' => 100,
                    'thankyou_page' => 100,
                    'leave' => 100,
                    'sign up' => 100
                ]
            ],
            'eventCountersWithDate' => [
                'branch_first' => [
                    'visit_mainpage' => [$date => 5],
                    'open_contact_form' => [$date => 5],
                    'visited_book_call' => [$date => 5],
                    'fill_form_later' => [$date => 5],
                    'form_filler_complete' => [$date => 5],
                    'visited_nutrionists_page' => [$date => 5],
                    'skip_call_booking' => [$date => 5],
                    'thankyou_page' => [$date => 5],
                    'leave' => [$date => 5],
                    'sign up' => [$date => 5]
                ],
                'branch_second' => [
                    'visit_mainpage' => [$date => 5],
                    'open_contact_form' => [$date => 5],
                    'visited_book_call' => [$date => 5],
                    'fill_form_later' => [$date => 5],
                    'form_filler_complete' => [$date => 5],
                    'visited_nutrionists_page' => [$date => 5],
                    'skip_call_booking' => [$date => 5],
                    'thankyou_page' => [$date => 5],
                    'leave' => [$date => 5],
                    'sign up' => [$date => 5]
                ]
            ]
        ]);
    }

    public function showExperimentStatsByIncrementalAndSummarizableEvents(ApiTester $I)
    {
        $users = [];

        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'view_contact_form'],
            ['type' => 'incremental', 'event_name' => 'visit_mainpage'],
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
            2,
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

        $I->seeResponseCodeIsSuccessful(200);

        $I->seeResponseContainsJson([
            'experiment' => [
                'id' => $experiment['decodeExperimentId'],
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
                    'view_contact_form' => 0,
                    'visit_mainpage' => 100
                ]
            ],
            'counters' => [
                'branch_first' => [
                    'view_contact_form' => 10,
                    'visit_mainpage' => 10,
                    'revenue' =>[
                        $yesterday->format('Y-m-d') => 300,
                        $today->format('Y-m-d') => 500
                    ]
                ],
                'branch_second' => [
                    'visit_mainpage' => 10,
                    'revenue' =>[
                        $yesterday->format('Y-m-d') => 300,
                        $today->format('Y-m-d') => 500
                    ]
                ]
            ]
        ]);
    }
}
