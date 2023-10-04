<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller; 

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

Route::middleware(['spotify'])->group(function () {
  Route::get('/', [Controller::class, 'nowPlaying']);
  Route::get('/skip', 'Controller@skip');
  Route::get('/back', 'Controller@back');
});

Route::get('/lookup', [Controller::class, 'lookup']);
