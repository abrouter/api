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

        $type = ['incremental', 'summarizable'];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($events as $event) {
            $I->sendPost('/user-events', [
                'data' => [
                    'type' => 'display_user_events',
                    'attributes' => [
                        'id' => null,
                        'event_name' => $event,
                        'event_type' => $type[mt_rand(0,1)]
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
            $eventName = $entry['attributes']['event_name'];
            $eventType = $entry['attributes']['event_type'];

            $I->seeRecord(
                'display_user_events',
                [
                    'event_name' => $eventName,
                    'type' => $eventType,
                    'user_id' => $user['id']
                ]);
            
            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'data' => [
                    'id' => $entry['id'],
                    'type' => 'display_user_events',
                    'attributes' => [
                        'event_name' => $eventName,
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

    public function getEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/user-events');

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        
        foreach($events as $event) {
            $I->seeResponseContainsJson([
                'data' => [
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
            ]);
        }
    }

    public function updateEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents($user['id']);
        $n = 0;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($events as $event) {
            $newEvent = 'event_' . $n . uniqid();

            $I->sendPatch('/user-events/' . $event['id'], [
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

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($events as $event) {

            $I->sendDelete('/user-events/' . $event['id'], [
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
