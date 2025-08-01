<?php

use App\Http\Controllers\SpreadSheetController;
use App\Http\Controllers\SpreadSheetDataController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

Route::controller(SpreadSheetDataController::class)->group(function () {
    Route::get('/spreadsheets-data', 'index')->name('spreadsheetsdata.index');
    Route::get('/spreadsheets-data/{id}', 'show')->whereNumber('id')
        ->name('spreadsheetsdata.show');
    Route::post('/spreadsheets-data', 'create')->name('spreadsheetsdata.create');
    Route::put('/spreadsheets-data/{id}', 'update')->whereNumber('id')
        ->name('spreadsheetsdata.update');
    Route::delete('/spreadsheets-data/{id}', 'destroy')->whereNumber('id')
        ->name('spreadsheetsdata.delete');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
