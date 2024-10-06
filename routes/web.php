<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/customers', function () {
    return view('customers.index');
});

Route::get('/projects', function () {
    return view('projects.index');
});
