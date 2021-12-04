<?php

class CreateDisplayEventsCest
{
    public function _before(ApiTester $I)
    {
    }

    public function create(ApiTester $I)
    {   
        $user = $I->haveUser($I);
        $events = $I->haveEvents();

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
}
