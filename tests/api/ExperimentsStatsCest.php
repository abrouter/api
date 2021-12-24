<?php

class ExperimentsStatsCest
{
    public function _before(ApiTester $I)
    {
    }

    public function showExperimentStatsWhithThreeBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithThreeBranch($user['id']);
        $runExperiment = $I->runExperiments($I, $user['token'], $experiment['alias']);
        
        for ($i = 0; $i < 3; $i++) { 
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $experiment['idBranch'][$i]);

            $response = json_decode($I->grabResponse(), true);
    
            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'percentage' => [
                    
                ],
                'counters' => [
                    
                ]
            ]);
        }
        
    }

    public function showExperimentStatsWhithTwoBranch(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $experiment = $I->haveExperimentWithTwoBranch($user['id']);
        $runExperiment = $I->runExperiments($I, $user['token'], $experiment['alias']);
        
        for ($i = 0; $i < 2; $i++) { 
            $I->haveHttpHeader('Content-Type', 'application/json');
            $I->haveHttpHeader('Accept', 'application/json');
            $I->amBearerAuthenticated($user['token']);

            $I->sendGet('/experiments/branch-stats?filter[experimentBranchId]=' . $experiment['idBranch'][$i]);

            $response = json_decode($I->grabResponse(), true);
            
            $I->seeResponseCodeIsSuccessful(201);
            $I->seeResponseContainsJson([
                'percentage' => [
                   
                ],
                'counters' => [
                    
                ]
            ]);
        }
        
    }
}
