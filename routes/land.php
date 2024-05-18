<?php

use App\Http\Controllers\Land\LandController;
use Illuminate\Support\Facades\Route;


Route::get('landing', [LandController::class, 'index']);

Route::post('landing/cart/add', [LandController::class, 'addCart']);
