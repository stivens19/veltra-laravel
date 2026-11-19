<?php

use Illuminate\Support\Facades\Route;
use Veltra\VeltraLaravel\Http\Controllers\DemoDashboardController;

Route::get('/demo', [DemoDashboardController::class, 'index'])->name('veltra.demo');

