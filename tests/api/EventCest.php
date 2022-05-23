<?php

class EventCest
{
    public function _before(ApiTester $I)
    {
    }

//    public function createEvents(ApiTester $I)
//    {
//        $user = $I->haveUser($I);
//
//        foreach(range(0, 9) as $i) {
//            $events = $I->haveEvents($user['id']);
//
//            $I->haveHttpHeader('Content-Type', 'application/json');
//            $I->haveHttpHeader('Accept', 'application/json');
//            $I->amBearerAuthenticated($user['token']);
//
//            $temporaryUserId = uniqid();
//            $userId = uniqid();
//            $tag = 'tags_' . uniqid();
//            $referrer = '';
//            $ip = mt_rand( 1 , 255 ). '.' . mt_rand( 1 , 255 ). '.' . mt_rand( 1 , 255 ). '.' . mt_rand( 1 , 255 ) ;;
//            $meta = [];
//            $date = (new \DateTime())->format('Y-m-d');
//
//            $I->sendPost('/event', [
//                'data' => [
//                    'type' => 'events',
//                    'attributes' => [
//                        'temporary_user_id' => $temporaryUserId,
//                        'user_id' => $userId,
//                        'event' => $events[$i]['event'],
//                        'value' => '',
//                        'tag' => $tag,
//                        'referrer' => $referrer,
//                        'ip' => $ip,
//                        'country_code' => 'ua',
//                        'meta' => $meta,
//                        'created_at' => $date
//                    ],
//                    'relationships' => [
//                        'owner' => [
//                            'data' => [
//                                'id' => $user['encodeId'],
//                                'type' => 'users'
//                            ]
//                        ]
//                    ]
//                ]
//            ]);
//
//            $response = json_decode($I->grabResponse(), true);
//
//            $I->seeResponseCodeIsSuccessful(201);
//            $I->seeResponseContainsJson([
//                'data' => [
//                    'id' => $response['data']['id'],
//                    'type' => 'events',
//                    'attributes' => [
//                        'user_id' => $response['data']['attributes']['user_id'],
//                        'event' => $response['data']['attributes']['event'],
//                        'value' => '',
//                        'tag' => $response['data']['attributes']['tag'],
//                        'referrer' => $response['data']['attributes']['referrer'],
//                        'ip' => $response['data']['attributes']['ip'],
//                        'meta' => $response['data']['attributes']['meta'],
//                        'created_at' => $response['data']['attributes']['created_at']
//                    ],
//                    'relationships' => [
//                        'owner' => [
//                            'data' => [
//                                'id' => $response['data']['relationships']['owner']['data']['id'],
//                                'type' => 'users'
//                            ]
//                        ]
//                    ]
//                ]
//            ]);
//
//            $recordEvents = [
//                'temporary_user_id' => $temporaryUserId,
//                'user_id' => $userId,
//                'event' => $events[$i]['event'],
//                'value' => '',
//                'tag' => $tag,
//                'referrer' => $referrer,
//                'ip' => $ip,
//                'owner_id' => $user['id'],
//                'created_at' => $date
//            ];
//
//            $I->seeRecord('events', $recordEvents);
//        }
//    }
}
