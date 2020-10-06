<?php

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

Auth::routes();

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])
        ->name('home');

// assets
Route::get('assets/show_by_search', [\App\Http\Controllers\AssetController::class, 'showBySearch'])
        ->name('assets.show_by_search');
Route::resource('assets', \App\Http\Controllers\AssetController::class);

// loans
Route::resource('loans', \App\Http\Controllers\LoanController::class);

// assetsloans
Route::resource('assetsloans', \App\Http\Controllers\AssetLoanController::class);
// AssetLoan relation should not be destroyed by asset_loan_id but by each asset and loan id
Route::delete('assetsloans', [\App\Http\Controllers\AssetLoanController::class, 'destroy'])
        ->name('assetsloans.destroy');
