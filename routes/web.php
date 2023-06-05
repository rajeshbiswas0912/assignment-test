<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

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
    return view('list-data');
});

Route::get('/data', [FormController::class, 'index'])->name('show-data');
Route::post('/data', [FormController::class, 'store'])->name('form-submit');
Route::post('/data/update', [FormController::class, 'update'])->name('update-data');
Route::post('/data/delete', [FormController::class, 'destroy'])->name('remove-data');
Route::post('/data/get', [FormController::class, 'get_data'])->name('get-data');
Route::post('/data/sort', [FormController::class, 'sort_data'])->name('sort-data');
