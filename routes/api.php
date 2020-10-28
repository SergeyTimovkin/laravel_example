<?php

use Illuminate\Http\Request;

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
Route::post('main/addUser', 'MainController@addUser');

Route::post('main/importUsersFromExcel', 'MainController@importUsersFromExcel');

Route::get('main/exampleExcelFile', 'MainController@exampleExcelFile');