<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response('ok', 200));
Route::get('/health', fn () => response('ok', 200));
