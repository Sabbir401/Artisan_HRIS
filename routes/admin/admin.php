<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'setting'], function(){
    Route::get('/','SettingController@index')->name('setting.index');
});