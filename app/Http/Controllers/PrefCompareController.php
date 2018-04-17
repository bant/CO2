<?php

namespace App\Http\Controllers;

use App\RegistYear;
use App\Factory;
use App\FactoryDischarge;
use App\Company;
use App\CompanyDivision;
use App\TransporterDischarge;
use App\Pref;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrefCompareController extends Controller
{
    private static $limit_pref = 5;

    /**
     * 
     */
    private function getDischargeByPref($pref_id)
    {
        $result = array();
        $year_id  = 0;

        $years = RegistYear::select()->orderBy('id', 'asc')->get();
        foreach($years as $year)
        {
            $year_id = $year->id;
            $tmp_datas = Factory::select(DB::raw(
                "co2_factory.pref_id AS pref_id,
                co2_pref.name AS pref_name,
                co2_factory_discharge.regist_year_id AS year_id,
                SUM(co2_factory_discharge.sum_of_exharst) AS sum_of_exharst"
                ))
                ->join('co2_factory_discharge','co2_factory.id','=','co2_factory_discharge.factory_id')
                ->join('co2_pref','co2_factory.pref_id','=','co2_pref.id')
                ->when($year_id != 0, function ($query) use ($year_id) {
                    return $query->where('co2_factory_discharge.regist_year_id', '=', $year_id);
                })
                ->when($pref_id != 0, function ($query) use ($pref_id) {
                    return $query->where('co2_factory.pref_id', '=', $pref_id);
                })
                ->groupBy('co2_factory_discharge.regist_year_id', 'co2_factory.pref_id', 'co2_pref.name')
                ->get();
            
            foreach($tmp_datas as $tmp_data)
            {
                $result[$tmp_data->pref_id - 1]['ID'] = $tmp_data->pref_id;
                $result[$tmp_data->pref_id - 1]['NAME'] = $tmp_data->pref_name;
                $result[$tmp_data->pref_id - 1]['DATA'][$year->id] = $tmp_data->sum_of_exharst;
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
    private function makePrefGraphData($pref_id)
    {
        $graph_datasets = array();
        $graph_labels = array();

        $year_list = RegistYear::select()->orderBy('id', 'asc')->get();
        $pref_rank = self::getDischargeByPref($pref_id);

        foreach ($year_list as $year)
        {
            $graph_labels[] = $year->name;

            $tmp_sum = 0;
            for ($i = 0; $i < count($pref_rank); $i++)
            {
                if ($i < self::$limit_pref)
                {
                    $graph_datasets[$i]['POS'] = $i;                    
                    $graph_datasets[$i]['ID'] = $pref_rank[$i]['ID'];
                    $graph_datasets[$i]['NAME'] = $pref_rank[$i]['NAME'];
                    $graph_datasets[$i]['DATA'][$year->id] = $pref_rank[$i]['DATA'][$year->id];
                }
                else
                {
                    $tmp_sum += $pref_rank[$i]['DATA'][$year->id];
                }
            }
            $graph_datasets[self::$limit_pref]['POS'] = self::$limit_pref;       
            $graph_datasets[self::$limit_pref]['ID'] = 0;
            $graph_datasets[self::$limit_pref]['NAME'] = "その他";
            $graph_datasets[self::$limit_pref]['DATA'][$year->id] = $tmp_sum;
        }

        // 都道府県指定の時に、その他も含まれているので削除
        if ($pref_id != 0)
        {
            unset($graph_datasets[self::$limit_pref]);
        }

        return array($graph_labels, $graph_datasets);
    }

    /**
     * 比較結果表の作成
     */
    private function makePrefTableData($pref_id, $regist_year_id)
    {
        // 年度毎の集計
        //=====================================
        $total_exharst = array();
        $years = RegistYear::select()->orderBy('id', 'asc')->get();
        foreach($years as $year)
        {
            $sum_data = FactoryDischarge::select(DB::raw("SUM(sum_of_exharst) AS sum_of_exharst"))
                ->where('regist_year_id', '=', $year->id)
                ->groupBy('regist_year_id')
                ->first();
    
            if ($sum_data!=null)
            {
                $total_exharst[$year->id] = $sum_data->sum_of_exharst;
            }
        }

        $tmp_datas = Factory::select(DB::raw(
            "co2_factory.pref_id AS pref_id,
            co2_pref.name AS pref_name,
            co2_factory_discharge.regist_year_id AS year_id,
            SUM(co2_factory_discharge.energy_co2) AS sum_energy_co2,
            SUM(co2_factory_discharge.noenergy_co2) AS sum_noenergy_co2,
            SUM(co2_factory_discharge.noenergy_dis_co2) AS sum_noenergy_dis_co2,
            SUM(co2_factory_discharge.ch4) AS sum_ch4,
            SUM(co2_factory_discharge.n2o) AS sum_n2o,
            SUM(co2_factory_discharge.hfc) AS sum_hfc,
            SUM(co2_factory_discharge.pfc) AS sum_pfc,
            SUM(co2_factory_discharge.sf6) AS sum_sf6,
            SUM(co2_factory_discharge.sum_of_exharst) AS sum_of_exharst,
            SUM(co2_factory_discharge.power_plant_energy_co2) AS sum_power_plant_energy_co2"
            ))
            ->join('co2_factory_discharge','co2_factory.id','=','co2_factory_discharge.factory_id')
            ->join('co2_pref','co2_factory.pref_id','=','co2_pref.id')
            ->when($pref_id != 0, function ($query) use ($pref_id) {
                return $query->where('co2_factory.pref_id', '=', $pref_id);
                
            }) 
            ->when($regist_year_id != 0, function ($query) use ($regist_year_id) {
                return $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id);
            })  
            ->groupBy('co2_factory_discharge.regist_year_id' , 'co2_factory.pref_id', 'co2_pref.name')
            ->get();

        $temp_data = array();
        $pre_sum = array();
        $table_datasets = array();
        foreach($tmp_datas as $tmp_data)
        {
            $temp_data['YEAR_ID'] = $tmp_data->year_id;
            $temp_data['PREF_NAME'] = $tmp_data->pref_name;
            $temp_data['PREF_ID'] = $tmp_data->pref_id;
            $temp_data['SUM_ENERGY_CO2'] = $tmp_data->sum_energy_co2;
            $temp_data['SUM_NOENERGY_CO2'] = $tmp_data->sum_noenergy_co2;
            $temp_data['SUM_NOENERGY_DIS_CO2'] = $tmp_data->sum_noenergy_dis_co2;
            $temp_data['SUM_CH4'] = $tmp_data->sum_ch4;
            $temp_data['SUM_N2O'] = $tmp_data->sum_n2o;
            $temp_data['SUM_HFC'] = $tmp_data->sum_hfc;
            $temp_data['SUM_PFC'] = $tmp_data->sum_pfc;
            $temp_data['SUM_SF6'] = $tmp_data->sum_sf6;
            $temp_data['SUM_OF_EXHARST'] = $tmp_data->sum_of_exharst;
            $temp_data['SUM_POWER_PLANT_ENERGY_CO2'] = $tmp_data->sum_power_plant_energy_co2;
            $temp_data['TOTAL_OF_EXHARST'] = $total_exharst[$year->id];
            $temp_data['PERCENT'] = round($tmp_data->sum_of_exharst/$total_exharst[$year->id]*100, 2);            

            // 増減率(％)を設定
            if ($regist_year_id==0) {
                if (isset($pre_sum[$tmp_data->pref_id])) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_of_exharst - $pre_sum[$tmp_data->pref_id])/$pre_sum[$tmp_data->pref_id]*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
                $pre_sum[$tmp_data->pref_id] = $tmp_data->sum_of_exharst;
            }
            else {
                $sum_data = FactoryDischarge::select(DB::raw("SUM(sum_of_exharst) AS sum_of_exharst"))
                    ->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id')
                    ->when($pref_id != 0, function ($query) use ($pref_id) {
                        return $query->where('co2_factory.pref_id', '=', $pref_id);
                    }) 
                    ->when($regist_year_id != 0, function ($query) use ($regist_year_id) {
                        return $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id - 1);
                    })  
                    ->first();

                if ($sum_data != null and $sum_data->sum_of_exharst != 0) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_of_exharst - $sum_data->sum_of_exharst)/$sum_data->sum_of_exharst*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
            }
            array_push($table_datasets, $temp_data);
        }

        return $table_datasets;
    }

    /**
     * 都道府県比較
     */
    public function pref(Request $request)
    {
        // 引数の処理
        $inputs = $request->all();
        $pref_id = isset($inputs['pref_id']) ? $inputs['pref_id'] : 0; 
        $regist_year_id = isset($inputs['regist_year_id']) ? $inputs['regist_year_id'] : 0;

        // 選択データの作成
        $prefs = Pref::all()->pluck('name','id');; 
        $prefs->prepend('未選択', 0);    // 最初に追加
        $regist_years = RegistYear::select()->orderBy('id', 'DESC')->pluck('name','id');
        $regist_years->prepend('未選択', 0);    // 最初に追加

        // 表データの作成
        $table_datasets = self::makePrefTableData($pref_id, $regist_year_id);

   //     dd($table_datasets);

        // グラフデータの作成
        list($graph_labels, $graph_datasets) = self::makePrefGraphData($pref_id);

        // ビューへの渡し
        return view('compare.pref', compact('prefs', 'regist_years', 'table_datasets', 'graph_labels', 'graph_datasets'));
    }
}
