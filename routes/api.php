<?php

use App\Http\Controllers\RoundController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/round', function (Request $request) {
    return 'sdsdsd';
});

Route::post('/round/inside/{round}', [RoundController::class, 'insidePolygon']);

Route::get('/round/{round}', [RoundController::class, 'challenges']);
