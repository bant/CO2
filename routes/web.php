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
Route::get('company/search', 'CompanySearchController@search');
Route::get('company/list', 'CompanySearchController@list');
Route::post('company/list', 'CompanySearchController@list');
Route::get('company/info', 'CompanySearchController@info');
Route::post('company/info', 'CompanySearchController@info');

// 事業所検索
Route::get('factory/search', 'FactorySearchController@search');
Route::get('factory/list', 'FactorySearchController@list');
Route::post('factory/list', 'FactorySearchController@list');
Route::get('factory/info', 'FactorySearchController@info');
Route::post('factory/info', 'FactorySearchController@info');

// 比較
// 業種別比較
Route::get('compare/MajorBusinessType', 'BusinessTypeCompareController@major_business_type');
Route::post('compare/MajorBusinessType', 'BusinessTypeCompareController@major_business_type');
Route::get('compare/MiddleBusinessType', 'BusinessTypeCompareController@middle_business_type');
Route::post('compare/MiddleBusinessType', 'BusinessTypeCompareController@middle_business_type');
Route::get('compare/MiddleBusinessType/factory', 'BusinessTypeCompareController@factory_by_middle_business_type');

// 輸送排出者別 CO2排出量集計(指定区分)
Route::get('compare/CompanyDivision', 'DivisionCompareController@company_division');
Route::post('compare/CompanyDivision', 'DivisionCompareController@company_division');
Route::get('compare/TransporterDivision', 'DivisionCompareController@transporter_division');
Route::post('compare/TransporterDivision', 'DivisionCompareController@transporter_division');
Route::get('compare/FactoryByCompanyDivision', 'DivisionCompareController@factory_by_company_division');
Route::get('compare/FactoryByTransporterDivision', 'DivisionCompareController@factory_by_transporter_division');


// 都道府県別比較
Route::get('compare/Pref', 'PrefCompareController@pref');
Route::post('compare/Pref', 'PrefCompareController@pref');
Route::get('compare/FactoryByPref', 'PrefCompareController@factory_by_pref');

//
Route::get('compare/Gas', 'GasCompareController@gas');
Route::post('compare/Gas', 'GasCompareController@gas');
Route::get('compare/FactoryByGas', 'GasCompareController@factory_by_gas');