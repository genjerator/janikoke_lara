<?php

use App\Http\Controllers\RoundController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UserAuthController;
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

Route::post('/login',[UserAuthController::class,'login'])->middleware('throttle:5,1');
Route::post('/logout',[UserAuthController::class,'logout'])
    ->middleware('auth:sanctum');
Route::get('/toplist/{round}',[ScoreController::class,'toplist']);

Route::middleware(['auth:sanctum','auth'])->group(function () {
    Route::get('/user', [UserAuthController::class, 'getUser']);

    Route::post('/round/inside/{round}', [RoundController::class, 'insidePolygon']);

    Route::get('/round/{round}', [RoundController::class, 'challenges']);

    Route::get('/round/{round}/scores', [ScoreController::class, 'roundScores']);
    Route::get('/round/{round}/result', [ScoreController::class, 'roundScores']);
    //swat this
});
Route::get('/round', function (Request $request) {
    return 'sdsdsd';
});
Route::get('/round/{round}/rawresult', [RoundController::class, 'roundRawResults']);


Route::get('/private', function () {
    return response()->json([
        'message' => 'Your token is valid; you are authorized.',
    ]);
})->middleware('auth');

Route::get('/scope', function () {
    return response()->json([
        'message' => 'Your token is valid and has the `read:messages` permission; you are authorized.',
    ]);
})->middleware('auth')->can('read:messages');

Route::get('/', function () {
    if (!auth()->check()) {
        return response()->json([
            'message' => 'You did not provide a valid token.',
        ]);
    }

    return response()->json([
        'message' => 'Your token is valid; you are authorized.',
        'id' => auth()->id(),
        'token' => auth()?->user()?->getAttributes(),
    ]);
});

