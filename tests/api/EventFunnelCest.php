<?php

class EventFunnelCest
{
    public function _before(ApiTester $I)
    {
    }

    public function showStatsByUserId(ApiTester $I)
    {
        $date = (new DateTime())->format('Y-m-d');
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $I->createEventsWithUserId($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

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
                'incrementalUnique' => [
                    'visit_mainpage' => [$date => 20],
                    'open_contact_form' => [$date => 20],
                    'visited_book_call' => [$date => 20],
                    'fill_form_later' => [$date => 20],
                    'form_filler_complete' => [$date => 20],
                    'visited_nutrionists_page' => [$date => 20],
                    'skip_call_booking' => [$date => 20],
                    'thankyou_page' => [$date => 20],
                    'leave' => [$date => 20],
                    'sign up' => [$date => 20],
                ]
            ]
        ]);
    }

    public function showStatsByTemporaryUserId(ApiTester $I)
    {
        $date = (new DateTime())->format('Y-m-d');
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $I->createEventsWithTemporaryUserId($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

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
                'incrementalUnique' => [
                    'visit_mainpage' => [$date => 10],
                    'open_contact_form' => [$date => 10],
                    'visited_book_call' => [$date => 10],
                    'fill_form_later' => [$date => 10],
                    'form_filler_complete' => [$date => 10],
                    'visited_nutrionists_page' => [$date => 10],
                    'skip_call_booking' => [$date => 10],
                    'thankyou_page' => [$date => 10],
                    'leave' => [$date => 10],
                    'sign up' => [$date => 10],
                ]
            ]
        ]);
    }

    public function showStatsTemporaryUsersAndUsers(ApiTester $I)
    {
        $date = (new DateTime())->format('Y-m-d');
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $I->createEventsWithTemporaryUserAndUser($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

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
                'incrementalUnique' => [
                    'visit_mainpage' => [$date => 100],
                    'open_contact_form' => [$date => 100],
                    'visited_book_call' => [$date => 100],
                    'fill_form_later' => [$date => 100],
                    'form_filler_complete' => [$date => 100],
                    'visited_nutrionists_page' => [$date => 100],
                    'skip_call_booking' => [$date => 100],
                    'thankyou_page' => [$date => 100],
                    'leave' => [$date => 100],
                    'sign up' => [$date => 100],
                ]
            ]
        ]);
    }

    public function showStatsRelatedUsersAndUsers(ApiTester $I)
    {
        $date = (new DateTime())->format('Y-m-d');
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();

        $I->saveUserEvents($user['id'], $events);

        $I->createEventsWithRelatedUserAndUser($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

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
                'incrementalUnique' => [
                    'visit_mainpage' => [$date => 100],
                    'open_contact_form' => [$date => 100],
                    'visited_book_call' => [$date => 100],
                    'fill_form_later' => [$date => 100],
                    'form_filler_complete' => [$date => 100],
                    'visited_nutrionists_page' => [$date => 100],
                    'skip_call_booking' => [$date => 100],
                    'thankyou_page' => [$date => 100],
                    'leave' => [$date => 100],
                    'sign up' => [$date => 100],
                ]
            ]
        ]);
    }

    public function showStatsByIncrementalUniqueAndSummarizableEvents(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'incremental-unique', 'event_name' => 'view_contact_form'],
            ['type' => 'incremental-unique', 'event_name' => 'visit_mainpage'],
            ['type' => 'summarizable', 'event_name' => 'revenue']
        ];
        $today = (new \DateTime());
        $yesterday = (new \DateTime())
            ->add(new DateInterval('P1D'));

        $user = $I->haveUser($I);

        $I->createEventWithSpecificType($user['id'], $unsavedEvents);

        $I->createEventsWithTypeIncremental($user['id'], $unsavedEvents[0]['event_name'], 10);
        $I->createEventsWithTypeIncremental($user['id'], $unsavedEvents[1]['event_name'], 20);
        $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            1,
            100,
            $today->format('Y-m-d')
        );
        $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            2,
            100,
            $yesterday->format('Y-m-d')
        );
        $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            2,
            200,
            $yesterday->format('Y-m-d')
        );
        $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[2]['event_name'],
            3,
            400,
            $today->format('Y-m-d')
        );

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel?filter[date_from]=' .
            $today->format('m-d-Y') .
            '&filter[date_to]=' . $yesterday->format('m-d-Y')
        );

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'percentage' => [
                'view_contact_form' => 50,
                'visit_mainpage' => 100
            ],
            'counters' => [
                'incrementalUnique' => [
                    'view_contact_form' => [$today->format('Y-m-d') => 10],
                    'visit_mainpage' => [$today->format('Y-m-d') => 20],
                ],
                'summarizable' => [
                    'revenue' => [
                        $today->format('Y-m-d') => 500,
                        $yesterday->format('Y-m-d') => 300
                    ]
                ]
            ]
        ]);
    }

    public function showStatsBySummarizableEvents(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'summarizable', 'event_name' => 'revenue']
        ];
        $today = (new \DateTime());
        $yesterday = (new \DateTime())
            ->add(new DateInterval('P1D'));

        $user = $I->haveUser($I);

        $I->createEventWithSpecificType($user['id'], $unsavedEvents);

        $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            1,
            null,
            $today->format('Y-m-d')
        );
        $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            2,
            '',
            $yesterday->format('Y-m-d')
        );
        $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            2,
            '00321',
            $yesterday->format('Y-m-d')
        );
        $I->createEventsWithTypeSummarizable(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            3,
            '400',
            $today->format('Y-m-d')
        );

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel?filter[date_from]=' .
            $today->format('m-d-Y') .
            '&filter[date_to]=' . $yesterday->format('m-d-Y')
        );

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'counters' => [
                'summarizable' => [
                    'revenue' => [
                        $today->format('Y-m-d') => 400,
                        $yesterday->format('Y-m-d') => 321
                    ]
                ]
            ]
        ]);
    }

    public function showStatsByIncrementalEvents(ApiTester $I)
    {
        $unsavedEvents = [
            ['type' => 'incremental', 'event_name' => 'click_button']
        ];
        $today = (new \DateTime());
        $yesterday = (new \DateTime())
            ->add(new DateInterval('P1D'));

        $user = $I->haveUser($I);

        $I->createEventWithSpecificType($user['id'], $unsavedEvents);

        $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            1,
            $today->format('Y-m-d')
        );
        $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            2,
            $yesterday->format('Y-m-d')
        );
        $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            2,
            $yesterday->format('Y-m-d')
        );
        $I->createEventsWithTypeIncremental(
            $user['id'],
            $unsavedEvents[0]['event_name'],
            5,
            $today->format('Y-m-d')
        );

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel?filter[date_from]=' .
            $today->format('m-d-Y') .
            '&filter[date_to]=' . $yesterday->format('m-d-Y')
        );

        $I->seeResponseCodeIsSuccessful(200);
        $I->seeResponseContainsJson([
            'counters' => [
                'incremental' => [
                    'click_button' => [
                        $today->format('Y-m-d') => 6,
                        $yesterday->format('Y-m-d') => 4
                    ]
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
