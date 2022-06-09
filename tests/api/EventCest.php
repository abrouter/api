<?php

use Modules\Core\EntityId\EntityEncoder;

class EventCest
{
    public function _before(ApiTester $I)
    {
    }

    public function createEvents(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        for($i = 0; $i < 20; $i++) {
            $temporaryUserId = substr(md5('user_' . uniqid()), 0, 13);
            $userId = (new EntityEncoder())->encode(random_int(1, 1000), 'users');
            $event = 'event_' . uniqid();
            $tag = 'tags_' . uniqid();
            $referrer = '';
            $ip = random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255);
            $meta = [];
            $date = (new \DateTime())->format('Y-m-d');

            $I->sendPost('/event', [
                'data' => [
                    'type' => 'events',
                    'attributes' => [
                        'temporary_user_id' => $temporaryUserId,
                        'user_id' => $userId,
                        'event' => $event,
                        'tag' => $tag,
                        'referrer' => $referrer,
                        'ip' => $ip,
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
