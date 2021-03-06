<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('users',                           'ApiController@users');
Route::get('users/{uid}',                     'ApiController@user');
Route::get('users/{uid}/posts/{searchText?}', 'ApiController@userPosts');
Route::get('posts/{searchText}',              'ApiController@postSearch');
Route::get('posts/{pid}/comments',            'ApiController@postComments');
