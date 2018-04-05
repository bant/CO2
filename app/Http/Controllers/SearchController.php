<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CompanyDivision;

class SearchController extends Controller
{
    /**
     * 事業者検索(indexより)
     */
    public function search_company(Request $request)
    {
        $company_divisions = CompanyDivision::all()->pluck('name','id');
        $company_divisions->prepend('全区分', 0);    // 最初に追加
    
        return view('search.company', compact('company_divisions'));
    }

    /**
     * 事業所検索(indexより)
     */
    public function search_factory(Request $request)
    {

        // ToDO
        return view('search.factory');
    }
}
