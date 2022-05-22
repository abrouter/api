<?php

class EventCest
{
    public function _before(ApiTester $I)
    {
    }

    public function createTenIncrementalEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveIncrementalEvents($user['id']);

        foreach(range(0, 10) as $i) {
            $event = $events[mt_rand(0,1)];
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $temporaryUserId = uniqid();
            $userId = uniqid();
            $tag = 'tags_' . uniqid();
            $referrer = '';
            $ip = '188.163.122.80';
            $meta = [];
            $date = (new \DateTime())->format('Y-m-d');

            $I->sendPost('/event', [
                'data' => [
                    'type' => 'events',
                    'attributes' => [
                        'temporary_user_id' => $temporaryUserId,
                        'user_id' => $userId,
                        'event' => $event,
                        'value' => '',
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
                        'user_id' => $response['data']['attributes']['user_id'],
                        'event' => $response['data']['attributes']['event'],
                        'value' => '',
                        'tag' => $response['data']['attributes']['tag'],
                        'referrer' => $response['data']['attributes']['referrer'],
                        'ip' => $response['data']['attributes']['ip'],
                        'meta' => $response['data']['attributes']['meta'],
                        'created_at' => $response['data']['attributes']['created_at']
                    ],
                    'relationships' => [
                        'owner' => [
                            'data' => [
                                'id' => $response['data']['relationships']['owner']['data']['id'],
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
                'value' => '',
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'owner_id' => $user['id'],
                'created_at' => $date
            ];

            $I->seeRecord('events', $recordEvents);
        }
    }

    public function createTwentyIncrementalEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveIncrementalEvents($user['id']);

        foreach(range(0, 20) as $i) {
            $event = $events[mt_rand(0,1)];
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $temporaryUserId = uniqid();
            $userId = uniqid();
            $tag = 'tags_' . uniqid();
            $referrer = '';
            $ip = '188.163.122.80';
            $meta = [];
            $date = (new \DateTime())->format('Y-m-d');

            $I->sendPost('/event', [
                'data' => [
                    'type' => 'events',
                    'attributes' => [
                        'temporary_user_id' => $temporaryUserId,
                        'user_id' => $userId,
                        'event' => $event,
                        'value' => '',
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
                        'user_id' => $response['data']['attributes']['user_id'],
                        'event' => $response['data']['attributes']['event'],
                        'value' => '',
                        'tag' => $response['data']['attributes']['tag'],
                        'referrer' => $response['data']['attributes']['referrer'],
                        'ip' => $response['data']['attributes']['ip'],
                        'meta' => $response['data']['attributes']['meta'],
                        'created_at' => $response['data']['attributes']['created_at']
                    ],
                    'relationships' => [
                        'owner' => [
                            'data' => [
                                'id' => $response['data']['relationships']['owner']['data']['id'],
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
                'value' => '',
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'owner_id' => $user['id'],
                'created_at' => $date
            ];

            $I->seeRecord('events', $recordEvents);
        }
    }

    public function createHundredSummarizableEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $event = $I->haveSummarizableEvents($user['id']);

        foreach(range(0, 100) as $i) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $temporaryUserId = uniqid();
            $userId = uniqid();
            $tag = 'tags_' . uniqid();
            $referrer = '';
            $ip = '188.163.122.80';
            $meta = [];
            $date = (new \DateTime())->format('Y-m-d');

            $I->sendPost('/event', [
                'data' => [
                    'type' => 'events',
                    'attributes' => [
                        'temporary_user_id' => $temporaryUserId,
                        'user_id' => $userId,
                        'event' => $event,
                        'value' => '',
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
                        'user_id' => $response['data']['attributes']['user_id'],
                        'event' => $response['data']['attributes']['event'],
                        'value' => '',
                        'tag' => $response['data']['attributes']['tag'],
                        'referrer' => $response['data']['attributes']['referrer'],
                        'ip' => $response['data']['attributes']['ip'],
                        'meta' => $response['data']['attributes']['meta'],
                        'created_at' => $response['data']['attributes']['created_at']
                    ],
                    'relationships' => [
                        'owner' => [
                            'data' => [
                                'id' => $response['data']['relationships']['owner']['data']['id'],
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
                'value' => '',
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'owner_id' => $user['id'],
                'created_at' => $date
            ];

            $I->seeRecord('events', $recordEvents);
        }
    }

    public function createHundredSummarizableEventsByOneUser(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $event = $I->haveSummarizableEvents($user['id']);
        $temporaryUserId = uniqid();
        $userId = uniqid();

        foreach(range(0, 100) as $i) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $tag = 'tags_' . uniqid();
            $referrer = '';
            $ip = '188.163.122.80';
            $meta = [];
            $date = (new \DateTime())->format('Y-m-d');

            $I->sendPost('/event', [
                'data' => [
                    'type' => 'events',
                    'attributes' => [
                        'temporary_user_id' => $temporaryUserId,
                        'user_id' => $userId,
                        'event' => $event,
                        'value' => '',
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
                        'user_id' => $response['data']['attributes']['user_id'],
                        'event' => $response['data']['attributes']['event'],
                        'value' => '',
                        'tag' => $response['data']['attributes']['tag'],
                        'referrer' => $response['data']['attributes']['referrer'],
                        'ip' => $response['data']['attributes']['ip'],
                        'meta' => $response['data']['attributes']['meta'],
                        'created_at' => $response['data']['attributes']['created_at']
                    ],
                    'relationships' => [
                        'owner' => [
                            'data' => [
                                'id' => $response['data']['relationships']['owner']['data']['id'],
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
                'value' => '',
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'owner_id' => $user['id'],
                'created_at' => $date
            ];

            $I->seeRecord('events', $recordEvents);
        }
    }

    public function createTwoHundredSummarizableEventsByOneUser(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $event = $I->haveSummarizableEvents($user['id']);
        $temporaryUserId = uniqid();
        $userId = uniqid();

        foreach(range(0, 200) as $i) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $tag = 'tags_' . uniqid();
            $referrer = '';
            $ip = '188.163.122.80';
            $meta = [];
            $date = (new \DateTime())->format('Y-m-d');

            $I->sendPost('/event', [
                'data' => [
                    'type' => 'events',
                    'attributes' => [
                        'temporary_user_id' => $temporaryUserId,
                        'user_id' => $userId,
                        'event' => $event,
                        'value' => '',
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
                        'user_id' => $response['data']['attributes']['user_id'],
                        'event' => $response['data']['attributes']['event'],
                        'value' => '',
                        'tag' => $response['data']['attributes']['tag'],
                        'referrer' => $response['data']['attributes']['referrer'],
                        'ip' => $response['data']['attributes']['ip'],
                        'meta' => $response['data']['attributes']['meta'],
                        'created_at' => $response['data']['attributes']['created_at']
                    ],
                    'relationships' => [
                        'owner' => [
                            'data' => [
                                'id' => $response['data']['relationships']['owner']['data']['id'],
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
                'value' => '',
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'owner_id' => $user['id'],
                'created_at' => $date
            ];

            $I->seeRecord('events', $recordEvents);
        }
    }

    public function createFourHundredSummarizableEventsByOneUser(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $event = $I->haveSummarizableEvents($user['id']);

        foreach(range(0, 400) as $i) {
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $temporaryUserId = uniqid();
            $userId = uniqid();
            $tag = 'tags_' . uniqid();
            $referrer = '';
            $ip = '188.163.122.80';
            $meta = [];
            $date = (new \DateTime())->format('Y-m-d');

            $I->sendPost('/event', [
                'data' => [
                    'type' => 'events',
                    'attributes' => [
                        'temporary_user_id' => $temporaryUserId,
                        'user_id' => $userId,
                        'event' => $event,
                        'value' => '',
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
                        'user_id' => $response['data']['attributes']['user_id'],
                        'event' => $response['data']['attributes']['event'],
                        'value' => '',
                        'tag' => $response['data']['attributes']['tag'],
                        'referrer' => $response['data']['attributes']['referrer'],
                        'ip' => $response['data']['attributes']['ip'],
                        'meta' => $response['data']['attributes']['meta'],
                        'created_at' => $response['data']['attributes']['created_at']
                    ],
                    'relationships' => [
                        'owner' => [
                            'data' => [
                                'id' => $response['data']['relationships']['owner']['data']['id'],
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
                'value' => '',
                'tag' => $tag,
                'referrer' => $referrer,
                'ip' => $ip,
                'owner_id' => $user['id'],
                'created_at' => $date
            ];

            $I->seeRecord('events', $recordEvents);
        }
    }
}
