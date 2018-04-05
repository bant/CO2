<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\CompanyDivision;

class CompanyController extends Controller
{
    /**
     * 事業者検索(indexより)
     */
    public function search(Request $request)
    {
        $company_divisions = CompanyDivision::all()->pluck('name','id');
        $company_divisions->prepend('全区分', 0);    // 最初に追加
    
        return view('company.search', compact('company_divisions'));
    }

    /**
     * 事業者リスト(searchより)
     */
    public function list(Request $request)
    {
        // inputs
        $inputs = $request->all();

        $company_name = isset($inputs['company_name']) ? trim($inputs['company_name']) : null;
        $company_address = isset($inputs['company_address']) ? trim($inputs['company_address']) : null;
        $company_division_id = isset($inputs['company_division_id']) ? $inputs['company_division_id'] : 0;

        // 問い合わせSQLを構築
        $query = Company::query();
        if (!is_null($company_name))
        {
            $query->where('name','like', "%$company_name%");
        }
        if (!is_null($company_address))
        {
            $query->where('address','like', "%$company_address%");
        }

        if ($company_division_id != '0')
        {
            $query->where('company_division_id', '=', $company_division_id);
        }
        $query->orderBy('regist_year_id', 'DESC');
        $query->distinct('name');
        $company_count = $query->count();
        $companies = $query->paginate(10);

        $company_divisions = CompanyDivision::all()->pluck('name','id');
        $company_divisions->prepend('全区分', 0);    // 最初に追加

        $pagement_params =  $inputs;
        unset($pagement_params['_token']);
    
        return view('company.list', compact('company_divisions', 'company_count', 'companies' , 'pagement_params'));
    }

    /**
     * 
     */
    public function info(Request $request)
    {
        // inputs
        $inputs = $request->all();

        $id = isset($inputs['id']) ? $inputs['id'] : 0;     // company id

        // company_idが設定されてない場合アボート
        if ($id == 0) {
            abort('404');
        }
        
        $company = Company::find($id);
        if ($company == null) {
            abort('404');
        }
        
        // 会社分類
        $years = RegistYear::select()->orderBy('id', 'desc')->get();

        $historys = array();
        $tmp = array();
        // 年度毎にデータをまとめる
        foreach ($years as $year) 
        {
            $tmp['YEAR_NAME'] = $year->name;

            $factories = Factory::where('company_id', '=', $id)->get();
            $total_factory_sum_of_exharst = 0;
            foreach($factories as $factory) 
            {
                $total_factory_sum_of_exharst += $factory->getSumOfExharst($year->id);
            }
            $tmp['TOTAL_SUM_OF_FACTORY_EXHARST'] = $total_factory_sum_of_exharst;



        }

        return view('company.info', compact('company'));
    }
}

