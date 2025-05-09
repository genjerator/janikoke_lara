<?php

use App\Domains\Person\Controllers\PeopleController;
use App\Domains\Person\Controllers\PersonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestmapController;
use App\Http\Controllers\ToplistController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/testmap', [MapController::class, 'index'])->name('map.index');
Route::get('/test', [MapController::class, 'test'])->name('map.test');

Route::get('/toplist/{round}', [ToplistController::class, 'index']);
Route::get('/rrtestmap', [TestmapController::class, 'index']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/testmapi', function () {
    return Inertia::render('Testmap');
})->name('Testmapi');

Route::prefix('people')->group(function () {
    Route::get('/', [PeopleController::class, 'index']);
});

Route::middleware('web')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
