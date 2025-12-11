<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return ['message' => 'Welcome to VidaPlus API'];
});

require __DIR__.'/auth.php';
