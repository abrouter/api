<?php

use Modules\Core\EntityId\EntityEncoder;

class UserInfoCest
{
    public function _before(ApiTester $I)
    {

    }

    public function getUserInfo(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $temporaryUserId = substr(md5('user_id' . uniqid()), 0, 13);
        $userId = (new EntityEncoder())->encode(99999, 'users');
        $eventName = 'visit_page';
        $tag = 'tag';
        $referrer = '';
        $ip = '192.168.0.1';
        $meta = [
            'browser' => 'Google Chrome',
            'platform' => 'Linux',
            'country_name' => 'Ukraine'
        ];
        $countryCode = 'UA';
        $createdAt = (new \DateTime())->format('Y-m-d');
        $updatedAt = (new \DateTime())->format('Y-m-d');

        $I->haveEvent(
            $user['id'],
            $temporaryUserId,
            $userId,
            $eventName,
            $tag,
            $referrer,
            $ip,
            $meta,
            $countryCode,
            $createdAt,
            $updatedAt
        );

        $experiment = $I->haveExperimentWithTwoBranch($user['id']);

        $I->haveConductedExperiments(
            $user['id'],
            $experiment['decodeExperimentId'],
            $experiment['decodeBranchId'],
            [$userId]
        );

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated($user['token']);
        $I->sendGet('/user-info/' . $userId);

        $I->seeResponseCodeIsSuccessful(200);

        $response = json_decode($I->grabResponse(), true);

        $I->seeResponseContainsJson([
            'data' => [
                'id' => $user['encodeId'],
                'type' => 'user_info',
                'attributes' => [
                    'experiments_ids' => [$experiment['experimentId']],
                    'created_at' => $response['data']['attributes']['created_at'],
                    'browser' => 'Google Chrome',
                    'platform' => 'Linux',
                    'country_name' => 'Ukraine'
                ]
            ]
        ]);
    }
}
