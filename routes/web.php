<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestViewController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', []);
})->name('home');

Route::get('/postscript', function () {
    return Inertia::render('Postscript', []);
})->name('postscript');

Route::get('/quest/{name}', [QuestViewController::class, 'index']);
