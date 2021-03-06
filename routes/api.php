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

Route::get('UpdateMaia', 'Api\Maia\MasterController@masterAll');
Route::get('test', 'Api\Maia\UsersController@validateNewRole');
Route::get('testy', function(){
    return response()->json(['response' => 'Yes'], 200);
});
