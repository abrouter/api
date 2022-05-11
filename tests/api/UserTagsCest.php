<?php

class UserTagsCest
{
    public function _before(ApiTester $I)
    {
    }

    public function getTags(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $tags = $I->haveEvents($user['id']);

        $I->amBearerAuthenticated($user['token']);

        $I->sendGet('/user-tags');

        $response = json_decode($I->grabResponse(), true);

        $count = count($response['data']);

        $I->seeResponseCodeIsSuccessful(201);

        for($i = 0; $i < $count; $i++) {

            $I->seeResponseContainsJson([
                'data' => [
                    'id' => $response['data'][$i]['id'],
                    'type' => 'tags',
                    'attributes' => [
                        'tag' => $response['data'][$i]['attributes']['tag']
                    ]
                ]
            ]);

        }
    }
}
