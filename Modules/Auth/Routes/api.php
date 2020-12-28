<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Middleware\AuthenticatedMiddleware;

Route::post('/v1/users', 'UsersController@create');
Route::get('/v1/users/me', 'UsersController@me')->middleware(AuthenticatedMiddleware::class);

Route::post('/v1/auth', 'AuthController@auth');
