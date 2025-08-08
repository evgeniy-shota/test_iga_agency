<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FetchCommandController;
use App\Http\Controllers\RowController;
use App\Http\Controllers\SpreadSheetController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return to_route('dashboard');
    // return Inertia::render('Welcome');
})->name('home');

Route::controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', 'index')->middleware(['auth'])
        ->name('dashboard');
    Route::get('/dashboard/{id}', 'show')->middleware(['auth'])
        ->name('dashboard.show');
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
    Route::get('/rows/', 'index')->middleware(['auth'])
        ->name('rows.index');
    Route::get('/rows/{id}', 'show')->middleware(['auth'])
        ->whereNumber('id')->name('rows.show');
    Route::post('/rows/add-multiple-rows/{sheetId}', 'addMultipleRows')
        ->middleware(['auth'])
        ->name('rows.addmultiplerows');
    Route::post('/rows', 'create')->middleware(['auth'])
        ->name('rows.create');
    Route::put('/rows/{id}', 'update')->middleware(['auth'])
        ->whereNumber('id')->name('rows.update');
    Route::delete('/rows/delete-all-rows/{sheetId}', 'deleteAllRows')
        ->middleware(['auth'])->name('rows.deleteallrows');
    Route::delete('/rows/{id}', 'destroy')->middleware(['auth'])
        ->whereNumber('id')->name('rows.delete');
});

Route::controller(FetchCommandController::class)->group(function () {
    Route::get('/fetch/{count?}', 'getSpreadsheetCommand')->whereNumber('count')
        ->middleware(['auth'])->name('fetch.getspreadsheet');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
