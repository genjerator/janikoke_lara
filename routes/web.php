<?php

use App\Domains\Person\Controllers\PeopleController;
use App\Domains\Person\Controllers\PersonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestmapController;
use App\Http\Controllers\ToplistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
    ]);
})->name('home');

Route::get('/testmap', [MapController::class, 'index'])->name('map.index');
Route::get('/test', [MapController::class, 'test'])->name('map.test');

Route::get('/toplist/{round}', [ToplistController::class, 'index']);
Route::get('/ranking', [ToplistController::class, 'ranking'])->name('ranking.index');
Route::get('/ranking/last30days', [ToplistController::class, 'rankingLast30Days'])->name('ranking.last30days');
Route::get('/rrtestmap', [TestmapController::class, 'index']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/testmapi', function () {
    return Inertia::render('Testmap');
})->name('rrtestmap');

Route::prefix('people')->group(function () {
    Route::get('/', [PeopleController::class, 'index']);
});

Route::middleware('web')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes accessible only to users logged in via Google
Route::middleware(['web', 'google.auth'])->group(function () {
    // Add Google-only routes here, e.g.:
    // Route::get('/google-dashboard', [GoogleDashboardController::class, 'index']);
});

// Google logout — clears Go session and removes session_id cookie
Route::post('/auth/google/logout', function (Request $request) {
    $sessionId    = $request->cookie('session_id');
    $goServiceUrl = rtrim(env('GO_AUTH_SERVICE_URL', 'http://localhost:8080'), '/');

    if ($sessionId) {
        try {
            Http::withCookies(['session_id' => $sessionId], parse_url($goServiceUrl, PHP_URL_HOST))
                ->timeout(2)
                ->post($goServiceUrl . '/auth/logout');
        } catch (\Throwable $e) {
            // Go service unreachable — proceed with clearing cookie anyway
        }
    }

    return redirect()->route('home')
        ->withCookie(cookie()->forget('session_id'));
})->name('google.logout');

