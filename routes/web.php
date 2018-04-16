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

// 事業者検索
Route::get('company/search', 'CompanyController@search');
Route::get('company/list', 'CompanyController@list');
Route::post('company/list', 'CompanyController@list');
Route::get('company/info', 'CompanyController@info');
Route::post('company/info', 'CompanyController@info');

// 事業所検索
Route::get('factory/search', 'FactoryController@search');
Route::get('factory/list', 'FactoryController@list');
Route::post('factory/list', 'FactoryController@list');
Route::get('factory/info', 'FactoryController@info');
Route::post('factory/info', 'FactoryController@info');

// 比較
// 業種別比較
Route::get('compare/MajorBusinessType', 'CompareController@major_business_type');
Route::post('compare/MajorBusinessType', 'CompareController@major_business_type');
Route::get('compare/MiddleBusinessType', 'CompareController@middle_business_type');
Route::post('compare/MiddleBusinessType', 'CompareController@middle_business_type');

// 輸送排出者別 CO2排出量集計(指定区分)
Route::get('compare/CompanyDivision', 'CompareController@company_division');
Route::post('compare/CompanyDivision', 'CompareController@company_division');
Route::get('compare/TransporterDivision', 'CompareController@transporter_division');

// 都道府県別比較
Route::get('compare/Pref', 'PrefCompareController@pref');
Route::post('compare/Pref', 'PrefCompareController@pref');


Route::get('compare/Gas', 'CompareController@gas');