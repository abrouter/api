<?php

class DisplayEventsCest
{
    public function _before(ApiTester $I)
    {
    }

    public function createEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $events = [
            'visit_mainpage',
            'open_contact_form',
            'visited_book_call',
            'fill_form_later',
            'form_filler_complete',
            'visited_nutrionists_page',
            'skip_call_booking',
            'thankyou_page',
            'leave',
            'sign up'
            ];

        $type = ['incremental', 'summarizable', 'incremental-unique'];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($events as $event) {
            $eventType = $type[mt_rand(0,2)];

            $I->sendPost('/display-user-events', [
                'data' => [
                    'type' => 'display_user_events',
                    'attributes' => [
                        'id' => null,
                        'event_name' => $event,
                        'event_type' => $eventType
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => $user['encodeId'],
                                'type' => 'users'
                            ]
                        ]
                    ]
                ]
            ]);
    
            $response = json_decode($I->grabResponse(), true);
            $entry = $response['data'];

            $I->seeRecord(
                'display_user_events',
                [
                    'event_name' => $event,
                    'type' => $eventType,
                    'user_id' => $user['id']
                ]);
            
            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'data' => [
                    'id' => $entry['id'],
                    'type' => 'display_user_events',
                    'attributes' => [
                        'event_name' => $event,
                        'event_type' => $eventType
                    ],
                    'relationships' => [
                        'user_id' => [
                            'data' => [
                                'id' => $entry['relationships']['user_id']['data']['id'],
                                'type' => 'users'
                            ]
                        ]
                    ]
                ],
            ]);
        }
    }

    public function getIncrementalUniqueEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);
        $savedEvents = $I->saveUserEvents($user['id'], $events, 'incremental-unique');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/display-user-events');
        
        $I->seeResponseCodeIsSuccessful(201);
        
        foreach($savedEvents as $event) {
            $I->seeResponseContainsJson([
                'data' => [
                    [
                        'id' => $event['id'],
                        'type' => 'display_user_events',
                        'attributes' => [
                            'event_name' => $event['event_name'],
                            'event_type' => 'incremental-unique'
                        ],
                        'relationships' => [
                            'user_id' => [
                                'data' => [
                                    'id' => $user['encodeId'],
                                    'type' => 'users'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }

    public function getIncrementalEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);
        $savedEvents = $I->saveUserEvents($user['id'], $events, 'incremental');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/display-user-events');

        $I->seeResponseCodeIsSuccessful(201);

        foreach($savedEvents as $event) {
            $I->seeResponseContainsJson([
                'data' => [
                    [
                        'id' => $event['id'],
                        'type' => 'display_user_events',
                        'attributes' => [
                            'event_name' => $event['event_name'],
                            'event_type' => 'incremental'
                        ],
                        'relationships' => [
                            'user_id' => [
                                'data' => [
                                    'id' => $user['encodeId'],
                                    'type' => 'users'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }

    public function getSummarizableEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);
        $savedEvents = $I->saveUserEvents($user['id'], $events, 'summarizable');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/display-user-events');

        $I->seeResponseCodeIsSuccessful(201);

        foreach($savedEvents as $event) {
            $I->seeResponseContainsJson([
                'data' => [
                    [
                        'id' => $event['id'],
                        'type' => 'display_user_events',
                        'attributes' => [
                            'event_name' => $event['event_name'],
                            'event_type' => 'summarizable'
                        ],
                        'relationships' => [
                            'user_id' => [
                                'data' => [
                                    'id' => $user['encodeId'],
                                    'type' => 'users'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }

    public function updateEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);
        $savedEvents = $I->saveUserEvents($user['id'], $events);
        $n = 0;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($savedEvents as $event) {
            $newEvent = 'event_' . $n . uniqid();

            $I->sendPatch('/display-user-events/' . $event['id'], [
                'data' => [
                    'type' => 'display_user_events',
                    'attributes' => [
                        'id' => $event['id'],
                        'event_name' => $newEvent
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => $user['encodeId'],
                                'type' => 'users'
                            ]
                        ]
                    ]
                ]
            ]);
            
            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'data' => [
                    'id' => $event['id'],
                    'type' => 'display_user_events',
                    'attributes' => [
                        'event_name' => $newEvent
                    ],
                    'relationships' => [
                        'user_id' => [
                            'data' => [
                                'id' => $user['encodeId'],
                                'type' => 'users'
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }

    public function deleteEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);
        $savedEvents = $I->saveUserEvents($user['id'], $events);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($savedEvents as $event) {
            $I->sendDelete('/display-user-events/' . $event['id'], [
                'data' => [
                    'type' => 'display_user_events',
                    'attributes' => [
                        'id' => $event['id'],
                        'event_name' => $event['event_name']
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => $user['encodeId'],
                                'type' => 'users'
                            ]
                        ]
                    ]
                ]
            ]);
            
            $I->seeResponseCodeIsSuccessful(200);
        }
    }
}
