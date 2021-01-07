<?php

use Illuminate\Support\Facades\Route;

Route::post('/v1/experiment/run', 'ExperimentsController@run');
Route::post('/v1/experiments/', 'ExperimentsController@createOrUpdate');
Route::patch('/v1/experiments/{id}', 'ExperimentsController@createOrUpdate');
Route::get('/v1/experiments', 'ExperimentsController@index');
