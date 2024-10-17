<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Post\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
})->name('user.me');

Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function () {
    Route::post('login','login')->name('login');
    Route::post('logout','logout')->name('logout')->middleware('auth:sanctum');
    Route::post('register','register')->name('register');
});

Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/', 'App\Http\Controllers\PostController@index')->name('index');
    Route::post('/', 'App\Http\Controllers\PostController@store')->name('store')->middleware('auth:sanctum');
    Route::get('/{post}', 'App\Http\Controllers\PostController@show')->name('show');
    Route::put('/{post}', 'App\Http\Controllers\PostController@update')->name('update')->middleware('auth:sanctum');
    Route::delete('/{post}', 'App\Http\Controllers\PostController@destroy')->name('destroy')->middleware('auth:sanctum');

    Route::prefix('{post}')->name('posts.show.')->group(function (){
        Route::prefix('comments')->name('comments.')->controller(CommentController::class)->group(function (){
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store')->middleware('auth:sanctum');
            Route::get('/{comment}', 'show')->name('show');
            Route::put('/{comment}', 'update')->name('update')->middleware('auth:sanctum');
            Route::delete('/{comment}', 'destroy')->name('destroy')->middleware('auth:sanctum');
        });
    });
});