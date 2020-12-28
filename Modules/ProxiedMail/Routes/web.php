<?php

Route::prefix('proxiedmail')->group(function () {
    Route::get('/', 'ProxiedMailController@index');
});
