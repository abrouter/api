<?php
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Middleware\AuthenticatedMiddleware;

Route::post('/v1/proxy-bindings', 'ProxyBindingsController@create')
    ->middleware('auth:api')
    ->middleware(AuthenticatedMiddleware::class);

Route::patch('/v1/proxy-bindings/{id}', 'ProxyBindingsController@patch')
    ->middleware('auth:api')
    ->middleware(AuthenticatedMiddleware::class);

Route::delete('/v1/proxy-bindings/{id}', 'ProxyBindingsController@delete')
    ->middleware('auth:api')
    ->middleware(AuthenticatedMiddleware::class);

Route::get('/v1/proxy-bindings', 'ProxyBindingsController@index')
    ->middleware('auth:api')
    ->middleware(AuthenticatedMiddleware::class);

Route::post('/v1/received-emails', 'ReceivedEmailsController@create');
