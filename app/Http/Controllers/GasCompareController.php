<?php

namespace App\Http\Controllers;

use App\RegistYear;
use App\Factory;
use App\FactoryDischarge;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GasCompareController extends Controller
{
    private static $limit_gas = 9;

    private function getRankByGassAll($gas_id)
    {
        $year_id = 0;
        $result = array();   
        $year_list = RegistYear::select()->orderBy('id', 'asc')->get();

//        dd($gas_id);
        foreach ($year_list as $year) {
            $regist_year_id = $year->id;
            $tmp_data = FactoryDischarge::select(DB::raw(
                    "co2_factory_discharge.regist_year_id AS regist_year_id,
                    SUM(co2_factory_discharge.energy_co2) AS sum_of_energy_co2,
                    SUM(co2_factory_discharge.noenergy_co2) AS sum_of_noenergy_co2,
                    SUM(co2_factory_discharge.noenergy_dis_co2) AS sum_of_noenergy_dis_co2,
                    SUM(co2_factory_discharge.ch4) AS sum_of_ch4,
                    SUM(co2_factory_discharge.n2o) AS sum_of_n2o,
                    SUM(co2_factory_discharge.hfc) AS sum_of_hfc,
                    SUM(co2_factory_discharge.pfc) AS sum_of_pfc,
                    SUM(co2_factory_discharge.sf6) AS sum_of_sf6,
                    SUM(co2_factory_discharge.sum_of_exharst) AS sum_of_sum_of_exharst,
                    SUM(co2_factory_discharge.power_plant_energy_co2) AS sum_of_power_plant_energy_co2"
                ))
                ->when($regist_year_id != 0, function ($query) use ($regist_year_id) {
                    return $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id);
                })  
                ->groupBy('co2_factory_discharge.regist_year_id')
                ->first();


//            dd($tmp_datas);
//            foreach($tmp_datas as $tmp_data)
//            {
                // 仮の順位をつけて登録
                if ($gas_id == 'all' || $gas_id == 'energy_co2') {
                    $result[0]['NAME'] = 'エネ起';
                    $result[0]['DATA'][$year->id] = $tmp_data->sum_of_energy_co2;
                }

                if ($gas_id == 'all' || $gas_id == 'noenergy_co2') {
                    $result[1]['NAME'] = '非エネ';
                    $result[1]['DATA'][$year->id] = $tmp_data->sum_of_noenergy_co2;
                }

                if ($gas_id == 'all' || $gas_id == 'noenergy_dis_co2') {
                    $result[2]['NAME'] = '非エ廃';
                    $result[2]['DATA'][$year->id] = $tmp_data->sum_of_noenergy_dis_co2;
                }

                if ($gas_id == 'all' || $gas_id == 'ch4') {
                    $result[3]['NAME'] = 'CH4';
                    $result[3]['DATA'][$year->id] = $tmp_data->sum_of_ch4;
                }

                if ($gas_id == 'all' || $gas_id == 'n2o') {
                    $result[4]['NAME'] = 'N2O';
                    $result[4]['DATA'][$year->id] = $tmp_data->sum_of_n2o;
                }

                if ($gas_id == 'all' || $gas_id == 'hfc') {
                    $result[5]['NAME'] = 'HFC';
                    $result[5]['DATA'][$year->id] = $tmp_data->sum_of_hfc;
                }

                if ($gas_id == 'all' || $gas_id == 'pfc') {
                    $result[6]['NAME'] = 'PFC';
                    $result[6]['DATA'][$year->id] = $tmp_data->sum_of_pfc;
                }

                if ($gas_id == 'all' || $gas_id == 'sf6') {
                    $result[7]['NAME'] = 'SF6';
                    $result[7]['DATA'][$year->id] = $tmp_data->sum_of_sf6;
                }

                if ($gas_id == 'all' || $gas_id == 'power_plant_energy_co2') {
                    $result[8]['NAME'] = 'エネルギー起源CO2(発電所等配分前)';
                    $result[8]['DATA'][$year->id] = $tmp_data->sum_of_power_plant_energy_co2;
                }
                $year_id = $year->id;
  //          }
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
    private function makeGasGraphData($gas_id, $regist_year_id)
    {
        $graph_datasets = array();
        $graph_labels = array();

        $year_list = RegistYear::select()->orderBy('id', 'asc')->get();
        $gas_rank = self::getRankByGassAll($gas_id);

//        dd($gas_rank);
        foreach ($year_list as $year)
        {
            $graph_labels[] = $year->name;

            $tmp_sum = 0;
            for ($i = 0; $i < count($gas_rank); $i++)
            {
                if ($i < self::$limit_gas)
                {
                    $graph_datasets[$i]['POS'] = $i;                    
                    $graph_datasets[$i]['NAME'] = $gas_rank[$i]['NAME'];
                    $graph_datasets[$i]['DATA'][$year->id] = $gas_rank[$i]['DATA'][$year->id];
                }
                else
                {
                    $tmp_sum += $gas_rank[$i]['DATA'][$year->id];
                }
            }
            $graph_datasets[self::$limit_gas]['POS'] = self::$limit_gas;       
            $graph_datasets[self::$limit_gas]['NAME'] = "その他";
            $graph_datasets[self::$limit_gas]['DATA'][$year->id] = $tmp_sum;
        }

        //その他も含まれているので削除
        if ($gas_id != 'all')
        {
            unset($graph_datasets[self::$limit_gas]);
        }

//        dd($graph_datasets);
        return array($graph_labels, $graph_datasets);
    }

    /**
     * 比較結果表の作成
     */
    private function makeGasTableData($gas_id, $regist_year_id)
    {
        $pre_sum_of_energy_co2 = 0;
        $pre_sum_of_noenergy_co2 = 0;
        $pre_sum_of_noenergy_dis_co2 = 0;
        $pre_sum_of_ch4 = 0;
        $pre_sum_of_n2o= 0;
        $pre_sum_of_hfc = 0;
        $pre_sum_of_pfc = 0;
        $pre_sum_of_sf6 = 0;
        $pre_sum_of_power_plant_energy_co2 = 0;
        $pre_sum_of_sum_of_exharst = 0;

        $result_list = array();
        $result = array();

        $tmp_datas = FactoryDischarge::select(DB::raw(
            "co2_factory_discharge.regist_year_id AS regist_year_id,
            SUM(co2_factory_discharge.energy_co2) AS sum_of_energy_co2,
            SUM(co2_factory_discharge.noenergy_co2) AS sum_of_noenergy_co2,
            SUM(co2_factory_discharge.noenergy_dis_co2) AS sum_of_noenergy_dis_co2,
            SUM(co2_factory_discharge.ch4) AS sum_of_ch4,
            SUM(co2_factory_discharge.n2o) AS sum_of_n2o,
            SUM(co2_factory_discharge.hfc) AS sum_of_hfc,
            SUM(co2_factory_discharge.pfc) AS sum_of_pfc,
            SUM(co2_factory_discharge.sf6) AS sum_of_sf6,
            SUM(co2_factory_discharge.sum_of_exharst) AS sum_of_sum_of_exharst,
            SUM(co2_factory_discharge.power_plant_energy_co2) AS sum_of_power_plant_energy_co2"
            ))
 //           ->join('co2_factory_discharge','co2_factory.id','=','co2_factory_discharge.factory_id')
            ->when($regist_year_id != 0, function ($query) use ($regist_year_id) {
                return $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id);
            })  
            ->groupBy('co2_factory_discharge.regist_year_id')
            ->get();

        foreach ($tmp_datas as $tmp_data)
        {
            $result['REGIST_YEAR_ID'] = $tmp_data->regist_year_id;

            // ---------
            if ($gas_id == 'all' || $gas_id == 'energy_co2') {
                $result['SUM_OF_ENERGY_CO2'] = $tmp_data->sum_of_energy_co2;
                if ($pre_sum_of_energy_co2 != 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_ENERGY_CO2'] = round(($result['SUM_OF_ENERGY_CO2'] - $pre_sum_of_energy_co2) / $pre_sum_of_energy_co2 * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_ENERGY_CO2'] = -99999999;
                }
                $pre_sum_of_energy_co2 = $result['SUM_OF_ENERGY_CO2'];
                $result['PERCENT_SUM_OF_ENERGY_CO2'] = round($result['SUM_OF_ENERGY_CO2'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            // -----------
            if ($gas_id == 'all' || $gas_id == 'noenergy_co2') {
                $result['SUM_OF_NOENERGY_CO2'] = $tmp_data->sum_of_noenergy_co2;
                if ($pre_sum_of_noenergy_co2 != 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_NOENERGY_CO2'] = round(($result['SUM_OF_NOENERGY_CO2'] - $pre_sum_of_noenergy_co2) / $pre_sum_of_noenergy_co2 * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_NOENERGY_CO2'] = -99999999;
                }
                $pre_sum_of_noenergy_co2 = $result['SUM_OF_NOENERGY_CO2'];
                $result['PERCENT_SUM_OF_NOENERGY_CO2'] = round($result['SUM_OF_NOENERGY_CO2'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            // ------------
            if ($gas_id == 'all' || $gas_id == 'noenergy_dis_co2') {
                $result['SUM_OF_NOENERGY_DIS_CO2'] = $tmp_data->sum_of_noenergy_dis_co2;
                if ($pre_sum_of_noenergy_dis_co2 != 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_NOENERGY_DIS_CO2'] = round(($result['SUM_OF_NOENERGY_DIS_CO2'] - $pre_sum_of_noenergy_dis_co2) / $pre_sum_of_noenergy_dis_co2 * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_NOENERGY_DIS_CO2'] = -99999999;
                }
                $pre_sum_of_noenergy_dis_co2 = $result['SUM_OF_NOENERGY_DIS_CO2'];
                $result['PERCENT_SUM_OF_NOENERGY_DIS_CO2'] = round($result['SUM_OF_NOENERGY_DIS_CO2'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            // ------------
            if ($gas_id == 'all' || $gas_id == 'ch4') {
                $result['SUM_OF_CH4'] = $tmp_data->sum_of_ch4;
                if ($pre_sum_of_ch4 != 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_CH4'] = round(($result['SUM_OF_CH4'] - $pre_sum_of_ch4) / $pre_sum_of_ch4 * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_CH4'] = -99999999;
                }
                $pre_sum_of_ch4 = $result['SUM_OF_CH4'];
                $result['PERCENT_SUM_OF_CH4'] = round($result['SUM_OF_CH4'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            // --------------
            if ($gas_id == 'all' || $gas_id == 'n2o') {
                $result['SUM_OF_N2O'] = $tmp_data->sum_of_n2o;
                if ($pre_sum_of_n2o != 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_N2O'] = round(($result['SUM_OF_N2O'] - $pre_sum_of_n2o) / $pre_sum_of_n2o * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_N2O'] = -99999999;
                }
                $pre_sum_of_n2o = $result['SUM_OF_N2O'];
                $result['PERCENT_SUM_OF_N2O'] = round($result['SUM_OF_N2O'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            // ---------
            if ($gas_id == 'all' || $gas_id == 'hfc') {
                $result['SUM_OF_HFC'] = $tmp_data->sum_of_hfc;
                if ($pre_sum_of_hfc != 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_HFC'] = round(($result['SUM_OF_HFC'] - $pre_sum_of_hfc) / $pre_sum_of_hfc * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_HFC'] = -99999999;
                }
                $pre_sum_of_hfc = $result['SUM_OF_HFC'];
                $result['PERCENT_SUM_OF_HFC'] = round($result['SUM_OF_HFC'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            // ---------
            if ($gas_id == 'all' || $gas_id == 'pfc') {
                $result['SUM_OF_PFC'] = $tmp_data->sum_of_pfc;
                if ($pre_sum_of_pfc != 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_PFC'] = round(($result['SUM_OF_PFC'] - $pre_sum_of_pfc) / $pre_sum_of_pfc * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_PFC'] = -99999999;
                }
                $pre_sum_of_pfc = $result['SUM_OF_PFC'];
                $result['PERCENT_SUM_OF_PFC'] = round($result['SUM_OF_PFC'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            // ---------
            if ($gas_id == 'all' || $gas_id == 'sf6') {
                $result['SUM_OF_SF6'] = $tmp_data->sum_of_sf6;
                if ($pre_sum_of_sf6!= 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_SF6'] = round(($result['SUM_OF_SF6'] - $pre_sum_of_sf6) / $pre_sum_of_sf6 * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_SF6'] = -99999999;
                }
                $pre_sum_of_sf6 = $result['SUM_OF_SF6'];
                $result['PERCENT_SUM_OF_SF6'] = round($result['SUM_OF_SF6'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            // ---------
            if ($gas_id == 'all' || $gas_id == 'power_plant_energy_co2') {
                $result['SUM_OF_POWER_PLANT_ENERGY_CO2'] = $tmp_data->sum_of_power_plant_energy_co2;
                if ($pre_sum_of_power_plant_energy_co2!= 0) {
                    $result['RATE_OF_CHANGE_SUM_OF_POWER_PLANT_ENERGY_CO2'] = round(($result['SUM_OF_POWER_PLANT_ENERGY_CO2'] - $pre_sum_of_power_plant_energy_co2) / $pre_sum_of_power_plant_energy_co2 * 100,2);
                }
                else {
                    $result['RATE_OF_CHANGE_SUM_OF_POWER_PLANT_ENERGY_CO2'] = -99999999;
                }
                $pre_sum_of_power_plant_energy_co2 = $result['SUM_OF_POWER_PLANT_ENERGY_CO2'];
                $result['PERCENT_SUM_OF_POWER_PLANT_ENERGY_CO2'] = round($result['SUM_OF_POWER_PLANT_ENERGY_CO2'] /$tmp_data->sum_of_sum_of_exharst * 100,2);
            }

            array_push($result_list, $result);
        }

        return $result_list;
    }


    /**
     * 温室効果ガス別比較
     */
    public function gas(Request $request)
    {
        // 引数の処理
        $inputs = $request->all();
        $gas_id = isset($inputs['gas_id']) ? $inputs['gas_id'] : 'all'; 
        $regist_year_id = isset($inputs['regist_year_id']) ? $inputs['regist_year_id'] : 0;

        // 選択データの作成
        $regist_years = RegistYear::select()->orderBy('id', 'DESC')->pluck('name','id');
        $regist_years->prepend('未選択', 0);    // 最初に追加
        // テーブルフデータの作成
        $table_datasets = self::makeGasTableData($gas_id, $regist_year_id);
        // グラフデータの作成
        list($graph_labels, $graph_datasets) = self::makeGasGraphData($gas_id, $regist_year_id);

//        dd($table_datasets);
        return view('compare.gas', compact('regist_years', 'table_datasets', 'graph_labels', 'graph_datasets'));
    }
}
