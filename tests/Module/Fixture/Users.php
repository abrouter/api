<?php
declare(strict_types=1);

namespace Tests\Module\Fixture;

use Modules\Core\EntityId\EntityEncoder;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;

class Users extends Module implements DependsOnModule
{
    public function haveUser($I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');

        $username = 'user' . uniqid() . '@test' . uniqid() . '.com';
        $password = 'pass_' . uniqid();
        
        $I->sendPost('/users', [
            'data' => [
                'type' => 'users',
                'attributes' => [
                    'username' => $username, 
                    'password' => $password
                ]
            ]
        ]);

        $response = json_decode($I->grabResponse(), true);
        $id = $response['data']['id'];

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $id,
                'type' => 'oauth-access-tokens',
                'attributes' => [
                    'token' => $response['data']['attributes']['token'],
                    'expires_at' => $response['data']['attributes']['expires_at']
                ],
                'relationships' => [
                    'user' => [
                        'data' => [
                            'type' => $response['data']['relationships']['user']['data']['type'],
                            'id' => $response['data']['relationships']['user']['data']['id']
                        ],
                    ],
                ],
            ],
        ]);

        $token = $response['data']['attributes']['token'];
        $userEncodeId = $response['data']['relationships']['user']['data']['id'];
        $userId = (new EntityEncoder())->decode($userEncodeId, 'users');
        $user = ['id' => $userId, 'encodeId' => $userEncodeId , 'username' => $username, 'password' => $password, 'token' => $token];

        return $user;
    }

    public function haveLogin($I, $username, $password)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');

        $I->sendPost('/auth', [
            "data" => [
                "type" => "auth-request",
                "attributes" => [
                    "username" => $username,
                    "password" => $password
                ]
            ]
        ]);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'oauth-access-tokens',
                'attributes' => [
                    'token' => $response['data']['attributes']['token'],
                    'expires_at' => $response['data']['attributes']['expires_at']
                ],
                'relationships' => [
                    'user' => [
                        'data' => [
                            'type' => $response['data']['relationships']['user']['data']['type'],
                            'id' => $response['data']['relationships']['user']['data']['id']
                        ],
                    ],
                ]
            ]
        ]);

        $token = $response['data']['attributes']['token'];

        return $token;
    }
    
    /**
     * {@inheritdoc}
     */
    public function _depends()
    {
        
    }
}
