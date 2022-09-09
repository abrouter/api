<?php

class EventCest
{
    public function _before(ApiTester $I)
    {
    }

    public function createShortEventWithUserId(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $userId = uniqid();

        $I->sendPost('/event', [
            'data' => [
                'type' => 'events',
                'attributes' => [
                    'user_id' => $userId,
                    'event' => 'test',
                ],
            ]
        ]);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'events',
                'attributes' => [
                    'user_id' => $userId,
                    'event' => 'test',
                ],
                'relationships' => [
                    'owner' => [
                        'data' => [
                            'id' => $user['encodeId'],
                            'type' => 'users'
                        ]
                    ]
                ]
            ]
        ]);

        $recordEvents = [
            'user_id' => $userId,
            'event' => 'test',
        ];

        $I->seeRecord('events', $recordEvents);
    }


    public function createEvents(ApiTester $I)
   {
       $user = $I->haveUser($I);
       $events = $I->haveUserEvents();
       $savedEvents = $I->saveUserEvents($user['id'], $events);

       foreach($savedEvents as $event) {
           $I->haveHttpHeader('Content-Type', 'application/json');
           $I->haveHttpHeader('Accept', 'application/json');
           $I->amBearerAuthenticated($user['token']);

           $temporaryUserId = uniqid();
           $userId = uniqid();
           $tag = 'tags_' . uniqid();
           $referrer = '';
           $ip = $I->getRandomIpAddress();
           $meta = [];
           $date = (new \DateTime())->format('Y-m-d');

           $I->sendPost('/event', [
               'data' => [
                   'type' => 'events',
                   'attributes' => [
                       'temporary_user_id' => $temporaryUserId,
                       'user_id' => $userId,
                       'event' => $event['event_name'],
                       'tag' => $tag,
                       'referrer' => $referrer,
                       'ip' => $ip,
                       'country_code' => 'ua',
                       'meta' => $meta,
                       'created_at' => $date
                   ],
                   'relationships' => [
                       'owner' => [
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
                   'id' => $response['data']['id'],
                   'type' => 'events',
                   'attributes' => [
                       'user_id' => $userId,
                       'event' => $event['event_name'],
                       'tag' => $tag,
                       'referrer' => $referrer,
                       'ip' => $ip,
                       'meta' => $response['data']['attributes']['meta'],
                       'created_at' => $response['data']['attributes']['created_at']
                   ],
                   'relationships' => [
                       'owner' => [
                           'data' => [
                               'id' => $user['encodeId'],
                               'type' => 'users'
                           ]
                       ]
                   ]
               ]
           ]);

           $recordEvents = [
               'temporary_user_id' => $temporaryUserId,
               'user_id' => $userId,
               'event' => $event['event_name'],
               'tag' => $tag,
               'referrer' => $referrer,
               'ip' => $ip,
               'owner_id' => $user['id']
           ];

           $I->seeRecord('events', $recordEvents);
       }
   }

    public function createEventWithDateAndTime(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $temporaryUserId = uniqid();
        $userId = uniqid();
        $event = 'test_created_at';
        $tag = 'tags_' . uniqid();
        $referrer = '';
        $ip = $I->getRandomIpAddress();
        $meta = [];
        $date = (new \DateTime())->format('Y-m-d H:i:s');

        $I->sendPost('/event', [
            'data' => [
                'type' => 'events',
                'attributes' => [
                    'temporary_user_id' => $temporaryUserId,
                    'user_id' => $userId,
                    'event' => 'test_created_at',
                    'tag' => $tag,
                    'referrer' => $referrer,
                    'ip' => $ip,
                    'country_code' => 'ua',
                    'meta' => $meta,
                    'created_at' => $date
                ],
                'relationships' => [
                    'owner' => [
                        'data' => [
                            'id' => $user['encodeId'],
                            'type' => 'users'
                        ]
                    ]
                ]
            ]
        ]);

        $response = json_decode($I->grabResponse(), true);
        $responseDate = (new \DateTime($response['data']['attributes']['created_at']))
            ->format('Y-m-d H:i:s');

        $d = $responseDate === $date ? $response['data']['attributes']['created_at'] : false;

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'events',
                'attributes' => [
                    'user_id' => $userId,
                    'event' => $event,
                    'tag' => $tag,
                    'referrer' => $referrer,
                    'ip' => $ip,
                    'meta' => $response['data']['attributes']['meta'],
                    'created_at' => $d
                ],
                'relationships' => [
                    'owner' => [
                        'data' => [
                            'id' => $user['encodeId'],
                            'type' => 'users'
                        ]
                    ]
                ]
            ]
        ]);

        $recordEvents = [
            'temporary_user_id' => $temporaryUserId,
            'user_id' => $userId,
            'event' => $event,
            'tag' => $tag,
            'referrer' => $referrer,
            'ip' => $ip,
            'owner_id' => $user['id']
        ];

        $I->seeRecord('events', $recordEvents);
    }

    public function createEventWithoutDate(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $temporaryUserId = uniqid();
        $userId = uniqid();
        $event = 'test_created_at';
        $tag = 'tags_' . uniqid();
        $referrer = '';
        $ip = $I->getRandomIpAddress();
        $meta = [];
        $date = (new \DateTime())->format('Y-m-d H:i:s');

        $I->sendPost('/event', [
            'data' => [
                'type' => 'events',
                'attributes' => [
                    'temporary_user_id' => $temporaryUserId,
                    'user_id' => $userId,
                    'event' => 'test_created_at',
                    'tag' => $tag,
                    'referrer' => $referrer,
                    'ip' => $ip,
                    'country_code' => 'ua',
                    'meta' => $meta,
                ],
                'relationships' => [
                    'owner' => [
                        'data' => [
                            'id' => $user['encodeId'],
                            'type' => 'users'
                        ]
                    ]
                ]
            ]
        ]);

        $response = json_decode($I->grabResponse(), true);
        $responseDate = (new \DateTime($response['data']['attributes']['created_at']))
            ->format('Y-m-d H:i:s');

        $d = $responseDate === $date ? $response['data']['attributes']['created_at'] : false;

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'events',
                'attributes' => [
                    'user_id' => $userId,
                    'event' => $event,
                    'tag' => $tag,
                    'referrer' => $referrer,
                    'ip' => $ip,
                    'meta' => $response['data']['attributes']['meta'],
                    'created_at' => $d
                ],
                'relationships' => [
                    'owner' => [
                        'data' => [
                            'id' => $user['encodeId'],
                            'type' => 'users'
                        ]
                    ]
                ]
            ]
        ]);

        $recordEvents = [
            'temporary_user_id' => $temporaryUserId,
            'user_id' => $userId,
            'event' => $event,
            'tag' => $tag,
            'referrer' => $referrer,
            'ip' => $ip,
            'owner_id' => $user['id']
        ];

        $I->seeRecord('events', $recordEvents);
    }
}
