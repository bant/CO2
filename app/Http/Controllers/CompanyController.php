<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\CompanyDivision;
use App\RegistYear;

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
     * 事業者届出情報
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
        
        $years = RegistYear::select()->orderBy('id', 'asc')->get();

        //  ここから検索結果用データの作成
        //==================================
        $histories = array();
        $pre_total_sum = 0;
        $tmp = array();
        // 年度毎にデータをまとめる
        foreach ($years as $year) 
        {
            $tmp['YEAR_NAME'] = $year->name . "年";

            $total_sum_of_exharst = 0;
            foreach ($company->factories as $factory)
            {
                $total_sum_of_exharst += $factory->getSumOfExharst($year->id);
            }
            $tmp['TOTAL_SUM_OF_EXHARST'] = $total_sum_of_exharst;

            $total_sum_of_transpoers_energy_CO2 = 0;
            foreach ($company->transporters as $transporter) 
            {
                $total_sum_of_transpoers_energy_CO2 += $transporter->getEnergyCO2($year->id);
            }
            $tmp['TOTAL_SUM_OF_ENERGY_CO2'] = $total_sum_of_transpoers_energy_CO2;

            $total_sum = $total_sum_of_exharst + $total_sum_of_transpoers_energy_CO2;
            $tmp['TOTAL_SUM'] = $total_sum;

            if ($pre_total_sum != 0 && $total_sum != 0)
            {
                $tmp_zougen = ($total_sum - $pre_total_sum) / $total_sum * 100;
                $zougen = round($tmp_zougen, 2);
            }
            else
            {
                $zougen = 0;    
            }       
            $tmp['ZOUGEN'] = $zougen;
            $pre_total_sum = $total_sum;

            // データをプッシュし格納
            array_push($histories, $tmp);
            arsort($histories);
        }

        //  ここからグラフ表示用のデータ
        //=================================
        $graph_datasets = array();
        $graph_labels = array();

        $graph_item_data = array();
        $graph_item_pos = array();
        $pos = 0;
        foreach ($years as $year)
        {
            $graph_labels[] = $year->name;
            foreach ($company->factories as $factory)
            {
                if (!isset($graph_item_pos[$factory->id]))
                {
                    $graph_item_pos[$factory->id] = $pos;
                    $pos++;
                }
                $graph_datasets[$graph_item_pos[$factory->id]]['POS'] = $graph_item_pos[$factory->id];
                $graph_datasets[$graph_item_pos[$factory->id]]['NAME'] = $factory->name;
                $graph_datasets[$graph_item_pos[$factory->id]]['DATA'][] = $factory->getSumOfExharst($year->id);
            }

            foreach($company->transporters as $transporter)
            {
                if (!isset($graph_item_pos[$transporter->id]))
                {
                    $graph_item_pos[$transporter->id] = $pos;
                    $pos++;
                }
                $graph_datasets[$graph_item_pos[$transporter->id]]['POS'] = $graph_item_pos[$transporter->id];
                $graph_datasets[$graph_item_pos[$transporter->id]]['NAME'] = "輸送排出量";
                $graph_datasets[$graph_item_pos[$transporter->id]]['DATA'][] = $transporter->getEnergyCO2($year->id);
            }
        }
//        dd($graph_labels);
//        dd($graph_datasets);

        // ここから検索結果用データの作成
        // 工場を持っている会社のみ
        //==================================
        $discharges = array();
        if ($company->getFactoryCount() != 0)
        {
            // 年度毎にデータをまとめる
            foreach ($years as $year) 
            {
                foreach ($company->factories as $factory)
                {
                    unset($tmp);

                    $discharge = $factory->getDischargeByYear($year->id);
                    if ($discharge == null)
                        continue;
                    
                    $tmp['REGIST_YEAR'] = $discharge->regist_year->id;
                    $tmp['FACTORY_ID'] = $factory->id;
                    $tmp['FACTORY_NAME'] = $factory->name;
                    $tmp['BUSINESS_TYPE'] = $factory->business_type->name;
                    $tmp['ENERGY_CO2'] = $discharge->energy_co2;
                    $tmp['NO_ENERGY_CO2'] = $discharge->noenergy_co2;
                    $tmp['NO_ENERGY_DIS_CO2'] = $discharge->noenergy_dis_co2;
                    $tmp['CH4'] = $discharge->ch4;
                    $tmp['N2O'] = $discharge->n2o;
                    $tmp['HFC'] = $discharge->hfc;
                    $tmp['PFC'] = $discharge->pfc;
                    $tmp['SF6'] = $discharge->sf6;
                    $tmp['SUM_OF_EXHARST'] = $discharge->sum_of_exharst;

                    if ($discharge->pre_percent == -99999999) {
                        $tmp['PRE_PERCENT'] = 0;
                    }
                    else {
                        $tmp['PRE_PERCENT'] = round($discharge->pre_percent,2);
                    }
                    array_push($discharges, $tmp);
                    arsort($discharges);
                }
            }
        }



        return view('company.info', compact('company', 'histories', 'graph_labels', 'graph_datasets','discharges'));
    }
}

