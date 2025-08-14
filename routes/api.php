<?php

use App\Http\Controllers\QuestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

Route::middleware('api')->post('/register', [RegisterController::class, 'register']);
Route::middleware('auth.token')->post('/quests/{id}/complete', [QuestController::class, 'complete']);
Route::middleware('auth.token')->get('/quests', [QuestController::class, 'index']);
Route::middleware('auth.token')->get('/quests/{id}', [QuestController::class, 'show']);
Route::middleware('auth.token')->get('/quests/name/{name}', [QuestController::class, 'showByName']);
