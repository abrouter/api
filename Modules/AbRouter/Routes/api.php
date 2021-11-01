<?php
use Illuminate\Support\Facades\Route;

Route::get('/v1/experiment/run', 'ExperimentsController@runSimple');

Route::middleware(['api', 'auth:api'])->group(function () {
    Route::post('/v1/experiments/', 'ExperimentsController@createOrUpdate');
    Route::patch('/v1/experiments/{id}', 'ExperimentsController@createOrUpdate');
    Route::get('/v1/experiments', 'ExperimentsController@index');
    Route::post('/v1/experiment/run', 'ExperimentsController@run');
    
    Route::post('/v1/event', 'StatisticsController@create');
    Route::post('/v1/event/funnel', 'StatisticsController@showStats');

    Route::get('/v1/user-events', 'CustomizationEventController@index');
    Route::post('/v1/user-events', 'CustomizationEventController@create');
    Route::patch('/v1/user-events/{id}', 'CustomizationEventController@update');
    Route::delete('/v1/user-events/{id}', 'CustomizationEventController@delete');
});
