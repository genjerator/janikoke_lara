<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Admin\Rounds\RoundsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Admin/Home');
})->name('admin');;

Route::middleware('auth:filament')->group(function () {
    Route::get('/rounds', [RoundsController::class, 'index'])->name('admin.rounds.index');
    Route::get('/rounds/create', [RoundsController::class, 'create'])->name('rounds.create');
    Route::post('/rounds', [RoundsController::class, 'store'])->name('rounds.store');
    Route::get('/rounds/{round}/edit', [RoundsController::class, 'edit'])->name('admin.rounds.edit');
    Route::put('/rounds/{round}', [RoundsController::class, 'update'])->name('rounds.update');
    Route::delete('/rounds/{round}', [RoundsController::class, 'destroy'])->name('rounds.destroy');
    Route::get('/challenge/{challengeId}/edit', [RoundsController::class, 'edit'])->name('admin.challenge.edit');
    Route::put('/challenge/{challengeId}', [RoundsController::class, 'update'])->name('admin.challenge.update');
    Route::get('/challenge/{round}/create', [RoundsController::class, 'create'])->name('admin.challenge.create');
    Route::post('/challenge', [RoundsController::class, 'store'])->name('admin.challenge.store');
    Route::delete('/challenge/{challengeId}', [RoundsController::class, 'destroy'])->name('admin.challenge.delete');
    Route::put('/challenge/{challengeId}', [RoundsController::class, 'update'])->name('admin.challenge.update');

});
