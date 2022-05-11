<?php

class ExperimentsStatsCest
{
    public function _before(ApiTester $I)
    {
    }

    public function showExperimentBranchStatsWhithThreeBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);
        $events = $I->haveUserEventsForExperimentStats();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

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
                    'fill_form_later' => $response['percentage']['fill_form_later']
                ],
                'counters' => [
                    'visit_mainpage' => $response['counters']['visit_mainpage'],
                    'open_contact_form' => $response['counters']['open_contact_form'],
                    'visited_book_call' => $response['counters']['visited_book_call'],
                    'fill_form_later' => $response['counters']['fill_form_later']
                ]
            ]);
        }
        
    }

    public function showExperimentBranchStatsWhithTwoBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEventsForExperimentStats();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

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
                    'fill_form_later' => $response['percentage']['fill_form_later']
                ],
                'counters' => [
                    'visit_mainpage' => $response['counters']['visit_mainpage'],
                    'open_contact_form' => $response['counters']['open_contact_form'],
                    'visited_book_call' => $response['counters']['visited_book_call'],
                    'fill_form_later' => $response['counters']['fill_form_later']
                ]
            ]);
        }
    }

    public function showExperimentStatsByExperimentIdWhithThreeBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);
        $events = $I->haveUserEventsForExperimentStats();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $runExperiment = $I->runExperiments($I, $user['token'], $experiment['alias'], $users);
        
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);
        
        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['experimentId']);

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        
        foreach($response['eventCountersWithDate'] as $branch => $event) {
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
                'eventCountersWithDate' => [
                    $branch => [
                        'visit_mainpage' => $response['eventCountersWithDate'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['eventCountersWithDate'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['eventCountersWithDate'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['eventCountersWithDate'][$branch]['fill_form_later']
                    ]
                ],
                'eventCountersWithDate' => [
                    $branch => [
                        'visit_mainpage' => $response['eventCountersWithDate'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['eventCountersWithDate'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['eventCountersWithDate'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['eventCountersWithDate'][$branch]['fill_form_later']
                    ]
                ]
            ]);
        }
    }

    public function showExperimentStatsByExperimentIdWhithTwoBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEventsForExperimentStats();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $runExperiment = $I->runExperiments($I, $user['token'], $experiment['alias'], $users);
        
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);
        
        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['experimentId']);

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        
        foreach($response['eventCountersWithDate'] as $branch => $event) {
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
                'eventCountersWithDate' => [
                    $branch => [
                        'visit_mainpage' => $response['eventCountersWithDate'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['eventCountersWithDate'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['eventCountersWithDate'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['eventCountersWithDate'][$branch]['fill_form_later']
                    ]
                ]
            ]);
        }
    }

    public function showExperimentStatsByAliasWhithThreeBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);
        $events = $I->haveUserEventsForExperimentStats();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $runExperiment = $I->runExperiments($I, $user['token'], $experiment['alias'], $users);
        
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);
        
        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['alias']);

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        
        foreach($response['eventCountersWithDate'] as $branch => $event) {
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
                'eventCountersWithDate' => [
                    $branch => [
                        'visit_mainpage' => $response['eventCountersWithDate'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['eventCountersWithDate'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['eventCountersWithDate'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['eventCountersWithDate'][$branch]['fill_form_later']
                    ]
                ]
            ]);
        }
    }

    public function showExperimentStatsByAliasWhithTwoBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $events = $I->haveUserEventsForExperimentStats();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

        $users = $I->createEventsWithRelatedUserAndUserForExperimentStats($user['id'], $events);

        $runExperiment = $I->runExperiments($I, $user['token'], $experiment['alias'], $users);
        
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);
        
        $I->sendGet('/experiments/stats?filter[experimentId]=' . $experiment['alias']);

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        
        foreach($response['eventCountersWithDate'] as $branch => $event) {
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
                'eventCountersWithDate' => [
                    $branch => [
                        'visit_mainpage' => $response['eventCountersWithDate'][$branch]['visit_mainpage'],
                        'open_contact_form' => $response['eventCountersWithDate'][$branch]['open_contact_form'],
                        'visited_book_call' => $response['eventCountersWithDate'][$branch]['visited_book_call'],
                        'fill_form_later' => $response['eventCountersWithDate'][$branch]['fill_form_later']
                    ]
                ]
            ]);
        }
    }
}
