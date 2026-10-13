<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "API REST ".env('GIT_SHORT_COMMIT');
});
