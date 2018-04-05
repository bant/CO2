<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * 事業所検索(indexより)
     */
    public function major_business_type(Request $request)
    {

        // ToDO
        return view('compare.major_business_type');
    }
    
}
