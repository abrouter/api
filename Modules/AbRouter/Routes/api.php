<?php

use Illuminate\Support\Facades\Route;

Route::post('/v1/experiment/run', 'ExperimentsController@run');
Route::get('/v1/experiments', 'ExperimentsController@index');
