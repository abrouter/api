<?php

class LoginCest
{
    public function _before(ApiTester $I)
    {
    }

    public function userLogin(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');

        $I->sendPost('/auth', [
            "data" => [
                "type" => "auth-request",
                "attributes" => [
                    "username" => $user['username'],
                    "password" => $user['password']
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
        unset($response);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json, application/vnd.api+json');
        $I->amBearerAuthenticated($token);
        $I->sendGet('/users/me');

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'data' => [
                'id' => $response['data']['id'],
                'type' => 'users',
                'attributes' => [
                    'username' => $response['data']['attributes']['username'],
                    'created_at' => $response['data']['attributes']['created_at'],
                    'updated_at' => $response['data']['attributes']['updated_at'],
                ],
            ],
        ]);
    }

    public function loginWithIncorrectPassword(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');

        $I->sendPost('/auth', [
            "data" => [
                "type" => "auth-request",
                "attributes" => [
                    "username" => $user['username'],
                    "password" => 'pass_' . uniqid()
                ]
            ]
        ]);

        $response = json_decode($I->grabResponse(), true);
    
        $I->seeResponseCodeIs(403);
        $I->seeResponseContainsJson(['message' => 'Password mismatch']);
    }
}
