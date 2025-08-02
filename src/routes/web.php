<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RowController;
use App\Http\Controllers\SpreadSheetController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', 'index')->middleware(['auth', 'verified'])
        ->name('dashboard');
});

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::controller(SpreadSheetController::class)->group(function () {
    Route::get('/spreadsheets', 'index')->name('spreadsheet.index');
    Route::get('/spreadsheets/{id}', 'show')->whereNumber('id')
        ->name('spreadsheet.show');
    Route::post('/spreadsheets', 'create')->name('spreadsheet.create');
    Route::put('/spreadsheets/{id}', 'update')->whereNumber('id')
        ->name('spreadsheet.update');
    Route::delete('/spreadsheets/{id}', 'destroy')->whereNumber('id')
        ->name('spreadsheet.delete');
});

Route::controller(RowController::class)->group(function () {
    Route::get('/rows', 'index')->name('rows.index');
    Route::get('/rows/{id}', 'show')->whereNumber('id')
        ->name('rows.show');
    Route::post('/rows', 'create')->name('rows.create');
    Route::put('/rows/{id}', 'update')->whereNumber('id')
        ->name('rows.update');
    Route::delete('/rows/{id}', 'destroy')->whereNumber('id')
        ->name('rows.delete');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
