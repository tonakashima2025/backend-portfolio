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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/categories', 'CategoriesController@index');
Route::post('/categories', 'CategoriesController@store');
Route::put('/categories/{category_id}', 'CategoriesController@update');
Route::delete('/categories/{category_id}', 'CategoriesController@delete');

Route::get('/tasks', 'TasksController@index');
Route::post('/tasks', 'TasksController@store');
Route::put('/tasks/{task_id}', 'TasksController@update');
Route::delete('/tasks/{task_id}', 'TasksController@delete');
