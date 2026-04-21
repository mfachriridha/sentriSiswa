<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/layout', function(){
    // App::setLocale('id');
    return view('layouts.app');
});
