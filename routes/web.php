<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PagesController@index');
Route::get('/about', 'PagesController@about');

Route::any('/bank/{action}', 'PagesController@bank');
Route::post('/bank/{action}', 'PagesController@bank');
Route::get('/banks', 'PagesController@banks');
Route::get('/exchanges', 'PagesController@exchanges');
Route::get('/simulations', 'PagesController@simulations');
Route::get('/simulation/setup', 'PagesController@setupSimulation');
Route::get('/simulation/save', 'PagesController@saveSimulation');
Route::get('/simulation/run', 'PagesController@runSimulation');
Route::get('/status', 'PagesController@status');