<?php

use App\Models\attendence;
use Illuminate\Support\Facades\Route;



Route::get('/{any}', function () {
    return view('welcome');
})->where('any','.*');


