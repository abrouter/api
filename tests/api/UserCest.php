<?php

class UserCest
{
    public function _before(ApiTester $I)
    {
        
    }

    public function createUser(ApiTester $I)
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
                ],
            ],
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
            'meta' => [
                'short_token' => $response['meta']['short_token'],
            ]
        ]);

        $I->seeRecord('users', ['username' => $username]);
    }

    public function creatingAUserWithAnIdenticalLogin(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');

        $password = 'pass_' . uniqid();
        
        $I->sendPost('/users', [
            'data' => [
                'type' => 'users',
                'attributes' => [
                    'username' => $user['username'], 
                    'password' => $password
                ]
            ]
        ]);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseCodeIs(422);
    }

    public function meTest(ApiTester $I)
    {
        $user = $I->haveUser($I);

        $I->seeRecord('users', ['username' => $user['username']]);
        $I->seeRecord('user_short_tokens', ['user_id' => $user['id'], 'token' => $user['token']]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json, application/vnd.api+json');
        $I->amBearerAuthenticated($user['token']);
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

    public function meTestWithRealBearerToken(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $token = $I->haveLogin($I, $user['username'], $user['password']);

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
}
