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

Route::get('/', function () {
    return view('index');
});

// 検索
Route::get('company/search', 'CompanyController@search');
Route::get('company/list', 'CompanyController@list');
Route::post('company/list', 'CompanyController@list');
Route::get('company/info', 'CompanyController@info');

Route::get('factory/search', 'FactoryController@search');
Route::get('factory/list', 'CompanyController@list');
Route::get('factory/info', 'CompanyController@info');

// 比較
Route::get('compare/MajorBusinessType', 'CompareController@major_business_type');
Route::get('compare/CompanyDivision', 'CompareController@company_division');
Route::get('compare/Pref', 'CompareController@pref');
Route::get('compare/Gas', 'CompareController@gas');