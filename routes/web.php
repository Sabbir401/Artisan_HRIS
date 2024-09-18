<?php

use App\Models\attendence;
use Illuminate\Support\Facades\Route;

Route::get('/link-storage', function () {
    $targetFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage/app/public';
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/public/storage';
    if (file_exists($linkFolder)) {
        return 'The link already exists.';
    } else {
        if (symlink($targetFolder, $linkFolder)) {
            return 'Symbolic link created successfully.';
        } else {
            return 'Failed to create symbolic link. Please check permissions.';
        }
    }
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any','.*');




