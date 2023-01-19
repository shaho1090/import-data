<?php

use App\Http\Controllers\Logs\LogCountController;
use Illuminate\Support\Facades\Route;

Route::get('/logs/count',[LogCountController::class,'index']);
