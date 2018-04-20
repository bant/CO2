<?php

namespace App\Http\Controllers;

use App\RegistYear;
use App\Company;
use App\CompanyDivision;
use App\TransporterDischarge;
use App\TransporterDivision;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisionCompareController extends Controller
{
    /**
     * 年度毎の集計
     */
    private function getSumEnergyCO2ByYear($years)
    {
        // 年度毎の集計
        $total_energy_co2 = array();

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

        return $total_energy_co2;
    }


    private static $limit_company_division = 5;
 
    /**
     * 指定区分別にCO2を取得する。
     */
    private function getDischargeByCompanyDivision($years, $company_division_id)
    {
        //  ここからグラフ表示用のデータ
        //=================================
        $result = array();
        $year_id  = 0;

        foreach ($years as $year)
        {
            $year_id = $year->id;
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
                                    ->where('co2_transporter_discharge.regist_year_id', '=', $year_id)
                                    ->groupBy('co2_transporter_discharge.regist_year_id','co2_company.company_division_id')
                                    ->get();

            foreach ($tmp_datas as $tmp_data)
            {
                $company_division = CompanyDivision::find($tmp_data->company_division_id);
                $result[$tmp_data->company_division_id- 1]['ID'] = $tmp_data->company_division_id;
                $result[$tmp_data->company_division_id- 1]['NAME'] = $company_division->name;
                $result[$tmp_data->company_division_id- 1]['DATA'][$year_id] = $tmp_data->sum_energy_co2;
            }
        }

        foreach ($result as $key => $row) 
        {
            $sort_key[$key] = $row['DATA'][$year_id];
        }
        array_multisort($sort_key, SORT_DESC, $result);
      
        return $result;
    }

   /**
     * グラフデータ作成
     */
    private function makeCompanyDivisionGraphData($company_division_id)
    {
        $graph_datasets = array();
        $graph_labels = array();

        $year_list = RegistYear::select()->orderBy('id', 'asc')->get();
        $rank = self::getDischargeByCompanyDivision($year_list, $company_division_id);

        foreach ($year_list as $year)
        {
            $graph_labels[] = $year->name;

            $tmp_sum = 0;
            for ($i = 0; $i < count($rank); $i++)
            {
                if ($i < self::$limit_company_division)
                {
                    $graph_datasets[$i]['POS'] = $i;                    
                    $graph_datasets[$i]['ID'] = $rank[$i]['ID'];
                    $graph_datasets[$i]['NAME'] = $rank[$i]['NAME'];
                    $graph_datasets[$i]['DATA'][$year->id] = $rank[$i]['DATA'][$year->id];
                }
                else
                {
                    $tmp_sum += $rank[$i]['DATA'][$year->id];
                }
            }
            $graph_datasets[self::$limit_company_division]['POS'] = self::$limit_company_division;       
            $graph_datasets[self::$limit_company_division]['ID'] = 0;
            $graph_datasets[self::$limit_company_division]['NAME'] = "その他";
            $graph_datasets[self::$limit_company_division]['DATA'][$year->id] = $tmp_sum;
        }

        // 都道府県指定の時に、その他も含まれているので削除
        if ($company_division_id != 0)
        {
            unset($graph_datasets[self::$limit_company_division]);
        }

        return array($graph_labels, $graph_datasets);
    }


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
//        unset($company_divisions[5]);

        $regist_years = RegistYear::select()->orderBy('id', 'DESC')->pluck('name','id');
        $regist_years->prepend('未選択', 0);    // 最初に追加

        $years = RegistYear::select()->orderBy('id', 'asc')->get();

        // 比較結果表の作成
        //=====================================
        $total_energy_co2 = self::getSumEnergyCO2ByYear($years);

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
            ->groupBy('co2_company.company_division_id', 'co2_transporter_discharge.regist_year_id')
            ->get();


        $temp_data = array();
        $pre_sum = array();
        $table_datasets = array();
        foreach($tmp_datas as $tmp_data)
        {
            $temp_data['YEAR_ID'] = $tmp_data->year_id;
            $temp_data['COMPANY_DIVISION_NAME'] = $tmp_data->company_division->name;
            $temp_data['COMPANY_DIVISION_ID'] = $tmp_data->company_division_id;
            $temp_data['SUM_ENERGY_CO2'] = $tmp_data->sum_energy_co2;
            $temp_data['PERCENT'] = round($tmp_data->sum_energy_co2/$total_energy_co2[$tmp_data->year_id]*100, 2);            
    
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
                $sum_data = self::getSumOfCo2($company_division_id, $tmp_data->transporter_division_id, $regist_year_id - 1);
                if ($sum_data != null and $sum_data->sum_energy_co2 != 0) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_energy_co2 - $sum_data->sum_energy_co2)/$sum_data->sum_energy_co2*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
            }
            array_push($table_datasets, $temp_data);
        }

        //  ここからグラフ表示用のデータ
        //=================================

        // グラフデータの作成
        list($graph_labels, $graph_datasets)  = self::makeCompanyDivisionGraphData($company_division_id);

        // ToDO
        return view('compare.company_division' ,compact('company_divisions', 'regist_years', 'table_datasets', 'graph_labels', 'graph_datasets'));
    }

    //=============================================================================



    private static $limit_transporter_division = 5;
 
    /**
     * 指定区分別にCO2を取得する。
     */
    private function getDischargeByTransporterDivision($years, $transporter_division_id)
    {
        //  ここからグラフ表示用のデータ
        //=================================
        $result = array();
        $year_id  = 0;

        foreach ($years as $year)
        {
            $year_id = $year->id;
            $tmp_datas = Company::select(DB::raw(
                                        "co2_transporter.transporter_division_id AS transporter_division_id,
                                         co2_transporter_discharge.regist_year_id AS year_id,
                                         SUM(co2_transporter_discharge.energy_co2) AS sum_energy_co2"
                                    ))
                                    ->join('co2_transporter','co2_company.id','=','co2_transporter.company_id')
                                    ->join('co2_transporter_discharge','co2_transporter.id','=','co2_transporter_discharge.transporter_id')
                                    ->when($transporter_division_id != 0, function ($query) use ($transporter_division_id) {
                                        return $query->where('co2_transporter.transporter_division_id', '=', $transporter_division_id);
                                    }) 
                                    ->where('co2_transporter_discharge.regist_year_id', '=', $year_id)
                                    ->groupBy('co2_transporter_discharge.regist_year_id','co2_transporter.transporter_division_id')
                                    ->get();

            foreach ($tmp_datas as $tmp_data)
            {
                $transporter_division = TransporterDivision::find($tmp_data->transporter_division_id);
                $result[$tmp_data->transporter_division_id - 1]['ID'] = $tmp_data->transporter_division_id;
                $result[$tmp_data->transporter_division_id - 1]['NAME'] = $transporter_division->name;
                $result[$tmp_data->transporter_division_id - 1]['DATA'][$year_id] = $tmp_data->sum_energy_co2;
            }
        }

        foreach ($result as $key => $row) 
        {
            $sort_key[$key] = $row['DATA'][$year_id];
        }
        array_multisort($sort_key, SORT_DESC, $result);
      
        return $result;
    }

   /**
     * グラフデータ作成
     */
    private function makeTransporterDivisionGraphData($transporter_division_id)
    {
        $graph_datasets = array();
        $graph_labels = array();

        $year_list = RegistYear::select()->orderBy('id', 'asc')->get();
        $rank = self::getDischargeByTransporterDivision($year_list, $transporter_division_id);

        foreach ($year_list as $year)
        {
            $graph_labels[] = $year->name;

            $tmp_sum = 0;
            for ($i = 0; $i < count($rank); $i++)
            {
                if ($i < self::$limit_transporter_division)
                {
                    $graph_datasets[$i]['POS'] = $i;                    
                    $graph_datasets[$i]['ID'] = $rank[$i]['ID'];
                    $graph_datasets[$i]['NAME'] = $rank[$i]['NAME'];
                    $graph_datasets[$i]['DATA'][$year->id] = $rank[$i]['DATA'][$year->id];
                }
                else
                {
                    $tmp_sum += $rank[$i]['DATA'][$year->id];
                }
            }
            $graph_datasets[self::$limit_transporter_division]['POS'] = self::$limit_transporter_division;       
            $graph_datasets[self::$limit_transporter_division]['ID'] = 0;
            $graph_datasets[self::$limit_transporter_division]['NAME'] = "その他";
            $graph_datasets[self::$limit_transporter_division]['DATA'][$year->id] = $tmp_sum;
        }

        // その他も含まれているので削除
        if ($transporter_division_id != 0)
        {
            unset($graph_datasets[self::$limit_transporter_division]);
        }

        return array($graph_labels, $graph_datasets);
    }


    /**
     * 
     */
    public function transporter_division(Request $request)
    {
        // inputs
        $inputs = $request->all();
        $company_division_id = isset($inputs['company_division_id']) ? $inputs['company_division_id'] : 0; // 設定されてないときは農業  
        $transporter_division_id = isset($inputs['transporter_division_id']) ? $inputs['transporter_division_id'] : 0;        // company division_id id
        $regist_year_id = isset($inputs['regist_year_id']) ? $inputs['regist_year_id'] : 0;

        $transporter_divisions = TransporterDivision::all()->pluck('name','id');; 
        $transporter_divisions->prepend('未選択', 0);    // 最初に追加
//        unset($company_divisions[5]);

        $regist_years = RegistYear::select()->orderBy('id', 'DESC')->pluck('name','id');
        $regist_years->prepend('未選択', 0);    // 最初に追加


        // $company_division_id が設定されてない場合アボート
        if ($company_division_id == 0) {
            abort('404');
        }
        $f_company_division = CompanyDivision::find($company_division_id);
        if ($f_company_division == null) {
            abort('404');
        }

        $years = RegistYear::select()->orderBy('id', 'asc')->get();

        // 比較結果表の作成
        //=====================================
        // 年度毎の集計
        $total_energy_co2 = self:: getSumEnergyCO2ByYear($years);

        $tmp_datas = Company::select(DB::raw(
            "co2_transporter.transporter_division_id AS transporter_division_id,
            co2_transporter_discharge.regist_year_id AS year_id,
            SUM(co2_transporter_discharge.energy_co2) AS sum_energy_co2"
            ))
            ->join('co2_transporter','co2_company.id','=','co2_transporter.company_id')
            ->join('co2_transporter_discharge','co2_transporter.id','=','co2_transporter_discharge.transporter_id')
            ->where('co2_company.company_division_id', '=', $company_division_id)
            ->when($transporter_division_id != 0, function ($query) use ($transporter_division_id) {
                return $query->where('co2_transporter.transporter_division_id', '=', $transporter_division_id);
            }) 
            ->when($regist_year_id != 0, function ($query) use ($regist_year_id) {
                return $query->where('co2_transporter_discharge.regist_year_id', '=', $regist_year_id);
            })  
            ->groupBy('co2_transporter.transporter_division_id', 'co2_transporter_discharge.regist_year_id')
            ->get();

        $temp_data = array();
        $pre_sum = array();
        $table_datasets = array();
        foreach($tmp_datas as $tmp_data)
        {
            $transporter_division = TransporterDivision::find($tmp_data->transporter_division_id);
            $temp_data['YEAR_ID'] = $tmp_data->year_id;
            $temp_data['TRANSPORTER_DIVISION_NAME'] = $transporter_division->name;
            $temp_data['TRANSPORTER_DIVISION_ID'] = $tmp_data->transporter_division_id;
            $temp_data['SUM_ENERGY_CO2'] = $tmp_data->sum_energy_co2;
            $temp_data['PERCENT'] = round($tmp_data->sum_energy_co2/$total_energy_co2[$tmp_data->year_id]*100, 2);            
       
            // 増減率(％)を設定
            if ($regist_year_id ==0) {
                // -----
                if (isset($pre_sum[$tmp_data->transporter_division_id])) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_energy_co2-$pre_sum[$tmp_data->transporter_division_id])/$pre_sum[$tmp_data->transporter_division_id]*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
                $pre_sum[$tmp_data->transporter_division_id] = $tmp_data->sum_energy_co2;
            }
            else {
                $sum_data = self::getSumOfCo2($company_division_id, $tmp_data->transporter_division_id, $regist_year_id - 1);
                if ($sum_data != null and $sum_data->sum_energy_co2 != 0) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_energy_co2 - $sum_data->sum_energy_co2)/$sum_data->sum_energy_co2*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
            }
            array_push($table_datasets, $temp_data);
        }
        
        //  ここからグラフ表示用のデータ
        //=================================

        // グラフデータの作成
        list($graph_labels, $graph_datasets)  = self::makeTransporterDivisionGraphData($transporter_division_id);

        return view('compare.transporter_division' ,compact('f_company_division' ,'transporter_divisions', 'regist_years', 'table_datasets',  'graph_labels', 'graph_datasets'));
    }
}
