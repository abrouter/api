<?php

class UserCest
{
    public function _before(ApiTester $I)
    {
        
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
}
