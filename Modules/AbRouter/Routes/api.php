<?php
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Middleware\SimpleTokenMiddleware;

Route::get('/v1/experiment/run', 'ExperimentsController@runSimple');

Route::middleware([SimpleTokenMiddleware::class, 'api', 'auth:api'])->group(function () {
    Route::post('/v1/experiments/', 'ExperimentsController@createOrUpdate');
    Route::patch('/v1/experiments/{id}', 'ExperimentsController@createOrUpdate');
    Route::get('/v1/experiments', 'ExperimentsController@index');
    Route::post('/v1/experiment/run', 'ExperimentsController@run');
    
    Route::post('/v1/event', 'StatisticsController@create');
    Route::post('/v1/event/funnel', 'StatisticsController@showStats');
});
