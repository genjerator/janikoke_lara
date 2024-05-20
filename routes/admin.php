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
    return Inertia::render('Home');
});
Route::get('/rounds', [RoundsController::class, 'index'])->name('rounds.index');
Route::get('/rounds/create', [RoundsController::class, 'create'])->name('rounds.create');
Route::post('/rounds', [RoundsController::class, 'store'])->name('rounds.store');
Route::get('/rounds/{round}/edit', [RoundsController::class, 'edit'])->name('rounds.edit');
Route::put('/rounds/{round}', [RoundsController::class, 'update'])->name('rounds.update');
Route::delete('/rounds/{round}', [RoundsController::class, 'destroy'])->name('rounds.destroy');

