<?php
use Illuminate\Support\Facades\Route;

Route::get('/v1/experiment/run', 'ExperimentsController@runSimple');

Route::middleware(['api', 'auth:api'])->group(function () {
    Route::post('/v1/experiments/', 'ExperimentsController@createOrUpdate');
    Route::patch('/v1/experiments/{id}', 'ExperimentsController@createOrUpdate');
    Route::get('/v1/experiments', 'ExperimentsController@index');
    Route::post('/v1/experiment/run', 'ExperimentsController@run');
});
