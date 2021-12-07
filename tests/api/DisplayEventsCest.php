<?php

class DisplayEventsCest
{
    public function _before(ApiTester $I)
    {
    }

    public function createEvents(ApiTester $I)
    {   
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($events as $event) {
            $I->sendPost('/user-events', [
                'data' => [
                    'type' => 'display_user_events',
                    'attributes' => [
                        'id' => null,
                        'event_name' => $event
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
            $eventName = $response['data']['attributes']['event_name'];

            $I->seeRecord('display_user_events', ['event_name' => $eventName, 'user_id' => $user['id']]);
            
            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'data' => [
                    'id' => $response['data']['id'],
                    'type' => 'display_user_events',
                    'attributes' => [
                        'event_name' => $eventName
                    ],
                    'relationships' => [
                        'user_id' => [
                            'data' => [
                                'id' => $response['data']['relationships']['user_id']['data']['id'],
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
        $events = $I->haveUserEvents();
        $saveEvents = $I->saveUserEvents($user['id'], $events);
        $n = 0;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/user-events');

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        
        foreach($saveEvents['events'] as $event) {
            $I->seeResponseContainsJson([
                'data' => [
                    'id' => $response['data'][$n]['id'],
                    'type' => 'display_user_events',
                    'attributes' => [
                        'event_name' => $event
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

            $n++;
        }
    }

    public function updateEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();
        $saveEvents = $I->saveUserEvents($user['id'], $events);
        $n = 0;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($saveEvents['eventsId'] as $id) {
            $event = 'event_' . $n . uniqid();

            $I->sendPatch('/user-events/' . $id, [
                'data' => [
                    'type' => 'display_user_events',
                    'attributes' => [
                        'id' => $id,
                        'event_name' => $event
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
            
            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'data' => [
                    'id' => $id,
                    'type' => 'display_user_events',
                    'attributes' => [
                        'event_name' => $event
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

            $n++;
        }
    }

    public function deleteEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();
        $saveEvents = $I->saveUserEvents($user['id'], $events);
        $n = 0;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        foreach($saveEvents['eventsId'] as $id) {

            $I->sendDelete('/user-events/' . $id, [
                'data' => [
                    'type' => 'display_user_events',
                    'attributes' => [
                        'id' => $id,
                        'event_name' => $saveEvents['events'][$n]
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
            
            $I->seeResponseCodeIsSuccessful(201);

            $n++;
        }
    }
}
