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

Route::get('/', 'HomeController@index')
        ->name('home');

Route::resource('loans', 'LoanController');

Route::get('loans/showoredit/{loanid}', function ($loanid) {
    if (Auth::user()->role == 'werkzeugag') {
        return redirect()->route('loans.edit', $loanid);
    } else {
        return redirect()->route('loans.show', $loanid);
    }
})->name('loans.showoredit');

Route::resource('assetsloans', 'AssetLoanController');
// AssetLoan relation should not be destroyed by asset_loan_id but by each asset and loan id
Route::delete('assetsloans', 'AssetLoanController@destroy')->name('assetsloans.destroy');
