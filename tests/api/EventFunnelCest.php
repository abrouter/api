<?php

class EventFunnelCest
{
    public function _before(ApiTester $I)
    {
    }

    public function showStatsByUserId(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);

        $I->createEventsWithUserId($user['id'], $events);
        
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);
        
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
                'visit_mainpage' => 20,
                'open_contact_form' => 20,
                'visited_book_call' => 20,
                'fill_form_later' => 20,
                'form_filler_complete' => 20,
                'visited_nutrionists_page' => 20,
                'skip_call_booking' => 20,
                'thankyou_page' => 20,
                'leave' => 20,
                'sign up' => 20,
                'summarization' => [
                    'visit_mainpage' => $response['counters']['summarization']['visit_mainpage'],
                    'open_contact_form' => $response['counters']['summarization']['open_contact_form'],
                    'visited_book_call' => $response['counters']['summarization']['visited_book_call'],
                    'fill_form_later' => $response['counters']['summarization']['fill_form_later'],
                    'form_filler_complete' => $response['counters']['summarization']['form_filler_complete'],
                    'visited_nutrionists_page' => $response['counters']['summarization']['visited_nutrionists_page'],
                    'skip_call_booking' => $response['counters']['summarization']['skip_call_booking'],
                    'thankyou_page' => $response['counters']['summarization']['thankyou_page'],
                    'leave' => $response['counters']['summarization']['leave'],
                    'sign up' => $response['counters']['summarization']['sign up'],
                ]
            ]
        ]);
    }

    public function showStatsByTemporaryUserId(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);

        $I->createEventsWithTemporaryUserId($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);
        
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
                'visit_mainpage' => 10,
                'open_contact_form' => 10,
                'visited_book_call' => 10,
                'fill_form_later' => 10,
                'form_filler_complete' => 10,
                'visited_nutrionists_page' => 10,
                'skip_call_booking' => 10,
                'thankyou_page' => 10,
                'leave' => 10,
                'sign up' => 10,
                'summarization' => [
                    'visit_mainpage' => $response['counters']['summarization']['visit_mainpage'],
                    'open_contact_form' => $response['counters']['summarization']['open_contact_form'],
                    'visited_book_call' => $response['counters']['summarization']['visited_book_call'],
                    'fill_form_later' => $response['counters']['summarization']['fill_form_later'],
                    'form_filler_complete' => $response['counters']['summarization']['form_filler_complete'],
                    'visited_nutrionists_page' => $response['counters']['summarization']['visited_nutrionists_page'],
                    'skip_call_booking' => $response['counters']['summarization']['skip_call_booking'],
                    'thankyou_page' => $response['counters']['summarization']['thankyou_page'],
                    'leave' => $response['counters']['summarization']['leave'],
                    'sign up' => $response['counters']['summarization']['sign up'],
                ]
            ]
        ]);
    }

    public function showStatsTemporaryUsersAndUsers(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);

        $I->createEventsWithTemporaryUserAndUser($user['id'], $events);
        
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);
        
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
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100,
                'summarization' => [
                    'visit_mainpage' => $response['counters']['summarization']['visit_mainpage'],
                    'open_contact_form' => $response['counters']['summarization']['open_contact_form'],
                    'visited_book_call' => $response['counters']['summarization']['visited_book_call'],
                    'fill_form_later' => $response['counters']['summarization']['fill_form_later'],
                    'form_filler_complete' => $response['counters']['summarization']['form_filler_complete'],
                    'visited_nutrionists_page' => $response['counters']['summarization']['visited_nutrionists_page'],
                    'skip_call_booking' => $response['counters']['summarization']['skip_call_booking'],
                    'thankyou_page' => $response['counters']['summarization']['thankyou_page'],
                    'leave' => $response['counters']['summarization']['leave'],
                    'sign up' => $response['counters']['summarization']['sign up'],
                ]
            ]
        ]);
    }

    public function showStatsRelatedUsersAndUsers(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);

        $I->createEventsWithRelatedUserAndUser($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 66,
                'open_contact_form' => 66,
                'visited_book_call' => 66,
                'fill_form_later' => 66,
                'form_filler_complete' => 66,
                'visited_nutrionists_page' => 66,
                'skip_call_booking' => 66,
                'thankyou_page' => 66,
                'leave' => 66,
                'sign up' => 66
            ],
            'counters' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100,
                'summarization' => [
                    'visit_mainpage' => $response['counters']['summarization']['visit_mainpage'],
                    'open_contact_form' => $response['counters']['summarization']['open_contact_form'],
                    'visited_book_call' => $response['counters']['summarization']['visited_book_call'],
                    'fill_form_later' => $response['counters']['summarization']['fill_form_later'],
                    'form_filler_complete' => $response['counters']['summarization']['form_filler_complete'],
                    'visited_nutrionists_page' => $response['counters']['summarization']['visited_nutrionists_page'],
                    'skip_call_booking' => $response['counters']['summarization']['skip_call_booking'],
                    'thankyou_page' => $response['counters']['summarization']['thankyou_page'],
                    'leave' => $response['counters']['summarization']['leave'],
                    'sign up' => $response['counters']['summarization']['sign up'],
                ]
            ]
        ]);
    }

    public function showStatsRevenueForTenIncrementalEvents(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'first_incremental_events'],
            ['type' => 'incremental', 'event_name' => 'second_incremental_events'],
            ['type' => 'summarizable', 'event_name' => 'summarizable_events']
        ];

        $user = $I->haveUser($I);
        $events = $I->haveRevenueEvents($user['id'], $unsavedEvents);

        $I->createRevenueEventsWithRelatedUserAndUser($user['id'], $events, 10, 'incremental');

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'percentage' => [
                'first_incremental_events' => 50,
                'second_incremental_events' => 50,
            ],
            'counters' => [
                'first_incremental_events' => 5,
                'second_incremental_events' => 5,
            ]
        ]);
    }

    public function showStatsRevenueForTwentyIncrementalEvents(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'first_incremental_events'],
            ['type' => 'incremental', 'event_name' => 'second_incremental_events'],
            ['type' => 'summarizable', 'event_name' => 'summarizable_events']
        ];

        $user = $I->haveUser($I);
        $events = $I->haveRevenueEvents($user['id'], $unsavedEvents);

        $I->createRevenueEventsWithRelatedUserAndUser($user['id'], $events, 20, 'incremental');

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'percentage' => [
                'first_incremental_events' => 50,
                'second_incremental_events' => 50,
            ],
            'counters' => [
                'first_incremental_events' => 10,
                'second_incremental_events' => 10,
            ]
        ]);
    }

    public function showStatsRevenueForHundredSummarizableEvents(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'first_incremental_events'],
            ['type' => 'incremental', 'event_name' => 'second_incremental_events'],
            ['type' => 'summarizable', 'event_name' => 'summarizable_events']
        ];

        $user = $I->haveUser($I);
        $events = $I->haveRevenueEvents($user['id'], $unsavedEvents);

        $I->createRevenueEventsWithRelatedUserAndUser($user['id'], $events, 100, 'summarizable');

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'percentage' => [
                'summarizable_events' => 100,
            ],
            'counters' => [
                'summarizable_events' => 100,
                'summarization' => [
                    'summarizable_events' => 5051
                ]
            ]
        ]);
    }

    public function showStatsRevenueForFourHundredSummarizableEvents(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'first_incremental_events'],
            ['type' => 'incremental', 'event_name' => 'second_incremental_events'],
            ['type' => 'summarizable', 'event_name' => 'summarizable_events']
        ];

        $user = $I->haveUser($I);
        $events = $I->haveRevenueEvents($user['id'], $unsavedEvents);

        $I->createRevenueEventsWithRelatedUserAndUser($user['id'], $events, 400, 'summarizable');

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'percentage' => [
                'summarizable_events' => 100,
            ],
            'counters' => [
                'summarizable_events' => 400,
                'summarization' => [
                    'summarizable_events' => 80201
                ]
            ]
        ]);
    }

    public function showStatsRevenueForHundredSummarizableEventsByOneUser(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'first_incremental_events'],
            ['type' => 'incremental', 'event_name' => 'second_incremental_events'],
            ['type' => 'summarizable', 'event_name' => 'summarizable_events']
        ];

        $user = $I->haveUser($I);
        $events = $I->haveRevenueEvents($user['id'], $unsavedEvents);

        $I->createRevenueEventsWithRelatedUserAndUser(
            $user['id'],
            $events,
            100,
            'summarizable',
            'user_' . uniqid(),
            'temporary_user_' . uniqid()
        );

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'percentage' => [
                'summarizable_events' => 100,
            ],
            'counters' => [
                'summarizable_events' => 1,
                'summarization' => [
                    'summarizable_events' => 2
                ]
            ]
        ]);
    }

    public function showStatsRevenueForTwoHundredSummarizableEventsByOneUser(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'first_incremental_events'],
            ['type' => 'incremental', 'event_name' => 'second_incremental_events'],
            ['type' => 'summarizable', 'event_name' => 'summarizable_events']
        ];

        $user = $I->haveUser($I);
        $events = $I->haveRevenueEvents($user['id'], $unsavedEvents);

        $I->createRevenueEventsWithRelatedUserAndUser(
            $user['id'],
            $events,
            200,
            'summarizable',
            'user_' . uniqid(),
            'temporary_user_' . uniqid()
        );

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'percentage' => [
                'summarizable_events' => 100,
            ],
            'counters' => [
                'summarizable_events' => 1,
                'summarization' => [
                    'summarizable_events' => 2
                ]
            ]
        ]);
    }

    public function getAllEventsWithOwnerByRelatedId(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);

        $temporaryUsersIds = $I->createEventsWithTemporaryUserId($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/statistics/user/' . $temporaryUsersIds[mt_rand(0,9)]);

        $response = json_decode($I->grabResponse(), true);
        $entry = $response['data'];

        $I->seeResponseCodeIsSuccessful(200);

        for ($i = 0; $i < count($response); $i++) {
            $I->seeResponseContainsJson([
                'data' => [
                    [
                        'type' => 'events',
                        'attributes' => [
                            'event' => $entry[$i]['attributes']['event']
                        ]
                    ]
                ]
            ]);
        }
    }

    public function getAllEventsWithOwnerByUserId(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);

        $usersIds = $I->createEventsWithUserId($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/statistics/user/' . $usersIds[mt_rand(0,19)]);

        $response = json_decode($I->grabResponse(), true);
        $entry = $response['data'];

        $I->seeResponseCodeIsSuccessful(200);

        for ($i = 0; $i < count($response); $i++) {
            $I->seeResponseContainsJson([
                'data' => [
                    [
                        'id' => $entry[$i]['id'],
                        'type' => 'events',
                        'attributes' => [
                            'user_id' => $entry[$i]['attributes']['user_id'],
                            'event' => $entry[$i]['attributes']['event'],
                            'tag' => $entry[$i]['attributes']['tag'],
                            'referrer' => $entry[$i]['attributes']['referrer'],
                            'ip' => $entry[$i]['attributes']['ip'],
                            'meta' => $entry[$i]['attributes']['meta'],
                            'created_at' => $entry[$i]['attributes']['created_at']
                        ],
                        'relationships' => [
                            'owner' => [
                                'data' => [
                                    'id' => $entry[$i]['relationships']['owner']['data']['id'],
                                    'type' => 'users'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }
}
