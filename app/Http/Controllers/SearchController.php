<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * 事業者検索(indexより)
     */
    public function company(Request $request)
    {
        $company_prefs = Pref::all()->pluck('name','id');
        $company_prefs->prepend('全都道府県', 0);    // 最初に追加
    
        return view('search.company', compact('company_prefs'));
    }
}
