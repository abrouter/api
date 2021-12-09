<?php

class EventFunnelCest
{
    public function _before(ApiTester $I)
    {
    }

    public function showStatsByUserId(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

        $I->createEventsWithUserId($user['id'], $events);
        
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ],
            'counters' => [
                'visit_mainpage' => 20,
                'open_contact_form' => 20,
                'visited_book_call' => 20,
                'fill_form_later' => 20,
                'form_filler_complete' => 20,
                'visited_nutrionists_page' => 20,
                'skip_call_booking' => 20,
                'thankyou_page' => 20,
                'leave' => 20,
                'sign up' => 20
            ]
        ]);
    }

    public function showStatsByTemporaryUserId(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

        $I->createEventsWithTemporaryUserId($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ],
            'counters' => [
                'visit_mainpage' => 10,
                'open_contact_form' => 10,
                'visited_book_call' => 10,
                'fill_form_later' => 10,
                'form_filler_complete' => 10,
                'visited_nutrionists_page' => 10,
                'skip_call_booking' => 10,
                'thankyou_page' => 10,
                'leave' => 10,
                'sign up' => 10
            ]
        ]);
    }

    public function showStatsTemporaryUsersAndUsers(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

        $I->createEventsWithTemporaryUserAndUser($user['id'], $events);
        
        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ],
            'counters' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ]
        ]);
    }

    public function showStatsRelatedUsersAndUsers(ApiTester $I)
    {
        $user = $I->haveUser($I);
        $events = $I->haveUserEvents();
        $saveEvents = $I->saveUserEvents($user['id'], $events);

        $I->createEventsWithRelatedUserAndUser($user['id'], $events);

        $I->amBearerAuthenticated($user['token']);

        $I->sendPost('/event/funnel');

        $response = json_decode($I->grabResponse(), true);
        
        $I->seeResponseCodeIsSuccessful(201);
        $I->seeResponseContainsJson([
            'percentage' => [
                'visit_mainpage' => 66,
                'open_contact_form' => 66,
                'visited_book_call' => 66,
                'fill_form_later' => 66,
                'form_filler_complete' => 66,
                'visited_nutrionists_page' => 66,
                'skip_call_booking' => 66,
                'thankyou_page' => 66,
                'leave' => 66,
                'sign up' => 66
            ],
            'counters' => [
                'visit_mainpage' => 100,
                'open_contact_form' => 100,
                'visited_book_call' => 100,
                'fill_form_later' => 100,
                'form_filler_complete' => 100,
                'visited_nutrionists_page' => 100,
                'skip_call_booking' => 100,
                'thankyou_page' => 100,
                'leave' => 100,
                'sign up' => 100
            ]
        ]);
    }
}
