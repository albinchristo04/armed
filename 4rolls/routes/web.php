<?php

use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\EmbedController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// ── Public Routes ──
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/match/{slug}', [PageController::class, 'match'])->name('match');
Route::get('/watch/{slug}/{streamId}', [PageController::class, 'watch'])->name('watch');
Route::get('/webmasters', [PageController::class, 'webmasters'])->name('webmasters');

// ── Public API ──
Route::get('/api/matches', [PageController::class, 'apiMatches'])->name('api.matches');

// ── Embed (frameable, no layout) ──
Route::get('/embed/{game}/{stream}', [EmbedController::class, 'show'])->name('embed.show');

// ── Admin Routes (protected by auth middleware later) ──
Route::prefix('admin')->name('admin.')->group(function () {

    // Match CRUD
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{game}/edit', [GameController::class, 'edit'])->name('games.edit');
    Route::put('/games/{game}', [GameController::class, 'update'])->name('games.update');
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
    Route::post('/games/{game}/toggle-important', [GameController::class, 'toggleImportant'])->name('games.toggle-important');

    // Import
    Route::get('/import', [GameController::class, 'importForm'])->name('import.form');
    Route::post('/import/fetch', [GameController::class, 'importFetch'])->name('import.fetch');
    Route::post('/import/save', [GameController::class, 'importSave'])->name('import.save');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
});
