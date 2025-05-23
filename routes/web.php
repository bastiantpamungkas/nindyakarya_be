<?php

use App\Http\Controllers\FaceScanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});