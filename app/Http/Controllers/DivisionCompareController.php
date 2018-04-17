<?php

namespace App\Http\Controllers;

use App\RegistYear;
use App\Company;
use App\CompanyDivision;
use App\TransporterDischarge;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisionCompareController extends Controller
{
    /**
     * 
     */
    public function company_division(Request $request)
    {
        // inputs
        $inputs = $request->all();

        $company_division_id = isset($inputs['company_division_id']) ? $inputs['company_division_id'] : 0; // 設定されてないときは農業  
        $regist_year_id = isset($inputs['regist_year_id']) ? $inputs['regist_year_id'] : 0;
/*
        $company_division = CompanyDivision::find($company_division_id);
        // CompanyDivisionが検索失敗する場合はアボート
        if ($company_division == null) {
            abort('404');
        }
*/
        $company_divisions = CompanyDivision::all()->pluck('name','id');; 
        $company_divisions->prepend('未選択', 0);    // 最初に追加
        unset($company_divisions[5]);

        $regist_years = RegistYear::select()->orderBy('id', 'DESC')->pluck('name','id');
        $regist_years->prepend('未選択', 0);    // 最初に追加


        // 比較結果表の作成
        //=====================================
        // 年度毎の集計
        $total_energy_co2 = array();
        $years = RegistYear::select()->orderBy('id', 'asc')->get();
        foreach($years as $year)
        {
            $sum_data = TransporterDischarge::select(DB::raw("SUM(energy_co2) AS sum_of_energy_co2"))
                ->where('regist_year_id', '=', $year->id)
                ->groupBy('regist_year_id')
                ->first();
    
            if ($sum_data!=null)
            {
                $total_energy_co2[$year->id] = $sum_data->sum_of_energy_co2;
            }
        }

        $tmp_datas = Company::select(DB::raw(
            "co2_company.company_division_id AS company_division_id,
            co2_transporter_discharge.regist_year_id AS year_id,
            SUM(co2_transporter_discharge.energy_co2) AS sum_energy_co2"
            ))
            ->join('co2_transporter','co2_company.id','=','co2_transporter.company_id')
            ->join('co2_transporter_discharge','co2_transporter.id','=','co2_transporter_discharge.transporter_id')
            ->when($company_division_id != 0, function ($query) use ($company_division_id) {
                return $query->where('co2_company.company_division_id', '=', $company_division_id);
            })
            ->when($regist_year_id != 0, function ($query) use ($regist_year_id) {
                return $query->where('co2_transporter_discharge.regist_year_id', '=', $regist_year_id);
            })  
            ->groupBy('co2_transporter_discharge.regist_year_id','co2_company.company_division_id')
            ->get();


         $temp_data = array();
         $pre_sum = array();
         $discharges = array();
         foreach($tmp_datas as $tmp_data)
         {
            $company_division = CompanyDivision::find($tmp_data->company_division_id);
            if ($company_division == null) continue;
    
            $temp_data['YEAR_ID'] = $tmp_data->year_id;
            $temp_data['COMPANY_DIVISION_NAME'] = $company_division->name;
            $temp_data['COMPANY_DIVISION_ID'] = $tmp_data->company_division_id;
            $temp_data['SUM_ENERGY_CO2'] = $tmp_data->sum_energy_co2;
            $temp_data['PERCENT'] = round($tmp_data->sum_energy_co2/$total_energy_co2[$year->id]*100, 2);            
    
            // 増減率(％)を設定
            if ($regist_year_id==0) {
                if (isset($pre_sum[$tmp_data->company_division_id])) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_energy_co2 - $pre_sum[$tmp_data->company_division_id])/$pre_sum[$tmp_data->company_division_id]*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
                $pre_sum[$tmp_data->company_division_id] = $tmp_data->sum_energy_co2;
            }
            else {
                $sum_data = Company::select(DB::raw(
                    "co2_company.company_division_id AS company_division_id,
                    co2_transporter_discharge.regist_year_id AS year_id,
                    SUM(co2_transporter_discharge.energy_co2) AS sum_energy_co2"
                    ))
                    ->join('co2_transporter','co2_company.id','=','co2_transporter.company_id')
                    ->join('co2_transporter_discharge','co2_transporter.id','=','co2_transporter_discharge.transporter_id')
                    ->when($company_division_id != 0, function ($query) use ($company_division_id) {
                        return $query->where('co2_company.company_division_id', '=', $company_division_id);
                    })
                    ->when($regist_year_id != 0, function ($query) use ($regist_year_id) {
                        return $query->where('co2_transporter_discharge.regist_year_id', '=', $regist_year_id-1);
                    })  
                    ->groupBy('co2_transporter_discharge.regist_year_id','co2_company.company_division_id')
                    ->first();

                if ($sum_data != null and $sum_data->sum_energy_co2 != 0) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_energy_co2 - $sum_data->sum_energy_co2)/$sum_data->sum_energy_co2*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
            }
            array_push($discharges, $temp_data);
        }

        //  ここからグラフ表示用のデータ
        //=================================
        $graph_datasets = array();
        $graph_labels = array();

        $pos = 0;
        foreach ($years as $year)
        {
            $tmp_graph_datas = Company::select(DB::raw(
                                        "co2_company.company_division_id AS company_division_id,
                                         co2_transporter_discharge.regist_year_id AS year_id,
                                         SUM(co2_transporter_discharge.energy_co2) AS sum_energy_co2"
                                    ))
                                    ->join('co2_transporter','co2_company.id','=','co2_transporter.company_id')
                                    ->join('co2_transporter_discharge','co2_transporter.id','=','co2_transporter_discharge.transporter_id')
                                    ->when($company_division_id != 0, function ($query) use ($company_division_id) {
                                        return $query->where('co2_company.company_division_id', '=', $company_division_id);
                                    })
                                    ->where('co2_transporter_discharge.regist_year_id', '=', $year->id)
                                    ->groupBy('co2_transporter_discharge.regist_year_id','co2_company.company_division_id')
                                    ->get();

            $graph_labels[] = $year->name;
            foreach ($tmp_graph_datas as $tmp_graph_data)
            {
                if (!isset($graph_item_pos[$tmp_graph_data->company_division_id]))
                {
                    $graph_item_pos[$tmp_graph_data->company_division_id] = $pos;
                    $pos++;
                }
                $company_division = CompanyDivision::find($tmp_graph_data->company_division_id);
                $graph_datasets[$graph_item_pos[$tmp_graph_data->company_division_id]]['POS'] = $graph_item_pos[$tmp_graph_data->company_division_id];
                $graph_datasets[$graph_item_pos[$tmp_graph_data->company_division_id]]['NAME'] =  $company_division->name;
                $graph_datasets[$graph_item_pos[$tmp_graph_data->company_division_id]]['DATA'][$tmp_graph_data->year_id] = $tmp_graph_data->sum_energy_co2;
            }
        }

 //       dd($graph_datasets);

        // 虫食いのデータに0を埋める
        foreach($graph_datasets as $graph_dataset)
        {
            foreach($years as $year)
            {
                if (!isset($graph_dataset['DATA'][$year->id]))
                {
                    $graph_datasets[$graph_dataset['POS']]['DATA'][$year->id] = 0;
                    ksort($graph_datasets[$graph_dataset['POS']]['DATA']);
                }
            }
        }


        // ToDO
        return view('compare.company_division' ,compact('company_divisions', 'regist_years', 'discharges', 'graph_labels', 'graph_datasets'));
    }

    /**
     * 
     */
    public function transporter_division(Request $request)
    {
        // ToDO 
        return view('compare.transporter_division');
    }
}
