<?php
use Illuminate\Support\Facades\Route;

Route::get('/v1/experiment/run', 'ExperimentsController@runSimple');
Route::post('/v1/ask-form', 'AskFormController@askForm');

Route::middleware(['api', 'auth:api'])->group(function () {
    Route::get('/v1/api-info/{ip}', 'IpInfoController');
    Route::get('/v1/user-info/{id}', 'UserInfoController');

    Route::post('/v1/experiments/', 'ExperimentsController@createOrUpdate');
    Route::patch('/v1/experiments/{id}', 'ExperimentsController@createOrUpdate');
    Route::delete('/v1/experiments/{id}', 'ExperimentsController@delete');
    Route::get('/v1/experiments', 'ExperimentsController@index');
    Route::get('/v1/experiments/have-user/{id}', 'ExperimentsController@getUserExperiments');
    Route::post('/v1/experiments/add-user', 'ExperimentsController@addUserToExperiment');
    Route::get('/v1/all-users-experiments', 'ExperimentsController@allUsersExperiments');
    Route::delete('/v1/experiments/user/delete', 'ExperimentsController@deleteUserFromExperiment');
    Route::post('/v1/experiment/run', 'ExperimentsController@run');
    Route::post('/v1/feature-toggles/', 'FeatureTogglesController@createOrUpdate');
    Route::patch('/v1/feature-toggles/{id}', 'FeatureTogglesController@createOrUpdate');
    Route::delete('/v1/feature-toggles/{id}', 'FeatureTogglesController@delete');
    Route::post('/v1/feature-toggles/run', 'FeatureTogglesController@run');
    Route::get('/v1/experiments/branch-stats', 'StatisticsController@showStatsByExperimentBranch');
    Route::get('/v1/experiments/stats', 'StatisticsController@showStatsByExperiment');
    Route::get('/v1/statistics/user/{id}', 'StatisticsController@getAllStatisticsEventsByUserId');

    Route::post('/v1/event', 'StatisticsController@create');
    Route::post('/v1/event/funnel', 'StatisticsController@showStats');

    Route::post('/v1/related-users', 'RelatedUserController@create');
    Route::get('/v1/all-related-users/{id}', 'RelatedUserController@getAllRelatedUsers');

    Route::get('/v1/user-tags', 'StatisticsController@showTags');

    /**
     * @TODO: rename to display user events
     */
    Route::get('/v1/user-events', 'CustomizationEventController@index');
    Route::post('/v1/user-events', 'CustomizationEventController@create');
    Route::patch('/v1/user-events/{id}', 'CustomizationEventController@update');
    Route::delete('/v1/user-events/{id}', 'CustomizationEventController@delete');
});
