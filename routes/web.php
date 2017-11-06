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

Route::any('/bank/{action}', 'PagesController@bank');
Route::get('/banks', 'PagesController@banks');

Route::get('/exchanges', 'PagesController@exchanges');

Route::any('/simulation/{action}', 'PagesController@simulation');
Route::get('/simulations', 'PagesController@simulations');

Route::get('/status', 'PagesController@status');

// Route::get('/about', 'PagesController@about');
