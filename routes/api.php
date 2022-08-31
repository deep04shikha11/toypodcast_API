<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PodcastCategoryController;
use App\Http\Controllers\PodcastController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('registration',[UserController::class,'store']);
Route::post('admin_reg',[UserController::class,'admin_reg']);
Route::post('login',[UserController::class, 'login']);

//only admin user routes
Route::group(['middleware'=>['auth:sanctum','admin']],function(){
    Route::get('get_category',[PodcastCategoryController::class,'index']);
    Route::post('save_category',[PodcastCategoryController::class,'save_category']);
    Route::get('get_all_audio',[PodcastController::class,'index']);
    Route::post('store_audio',[PodcastController::class,'store']);
    Route::get('get_audio/{id}',[PodcastController::class,'show']);
    Route::put('update_audio/{id}',[PodcastController::class,'update']);
    Route::delete('delete_audio/{id}',[PodcastController::class,'destroy']);
    Route::get('get_status/{id}',[PodcastController::class,'get_status']);
    Route::post('logout',[UserController::class, 'logout']);
});

//non admin user routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('get_all_podcast',[PodcastController::class,'index']);
    Route::get('play_audio/{id}',[PodcastController::class,'start_play']);
    Route::get('stop_audio/{id}',[PodcastController::class,'stop_play']);
    Route::post('logout',[UserController::class, 'logout']);
});
