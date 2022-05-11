<?php

use Modules\Core\EntityId\EntityEncoder;

class RelatedUserCest
{
    public function _before(ApiTester $I)
    {
    }

    public function createRelatedUsers(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveEvents($user['id']);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        for($i = 0; $i < 5; $i++) {
            $existUserId = substr(md5('user_' . uniqid()), 0, 13);
            $userId = (new EntityEncoder())->encode(random_int(1, 1000), 'users');

            $I->sendPost('/related-users', [
                'data' => [
                    'type' => 'related_users',
                    'attributes' => [
                        'exist_user_id' => $existUserId,
                        'user_id' => $userId,
                        'event_id' => $events[$i]['eventId']
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
                    'type' => 'related_users',
                    'attributes' => [
                        'user_id' => $response['data']['attributes']['user_id'],
                        'related_user_id' => $response['data']['attributes']['related_user_id']
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

            $I->seeRecord('related_users', [
                'owner_id' => $user['id'],
                'related_user_id' => $response['data']['attributes']['related_user_id'],
                'user_id' => $response['data']['attributes']['user_id'],
            ]);
        }
    }

    public function getAllRelatedUsersIdsByUserId(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $userId = $I->haveRelatedUserIdWithUserId($user['id']);

        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/all-related-users/'. $userId);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(200);

        for ($n = 0; $n < 5; $n++) {
            $I->seeResponseContainsJson([
                'data' => [
                    [
                        'type' => 'related_users',
                        'attributes' => [
                            'related_user_id' => $response['data'][$n]['attributes']['related_user_id'],
                        ]
                    ],
                ],
            ]);
        }
    }
}
