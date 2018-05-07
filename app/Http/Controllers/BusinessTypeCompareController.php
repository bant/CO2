<?php

namespace App\Http\Controllers;

use App\RegistYear;
use App\Factory;
use App\FactoryDischarge;
use App\MajorBusinessType;
use App\MiddleBusinessType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessTypeCompareController extends Controller
{

    /**
     * 比較結果表の作成
     */
    private function makeMajorBusinessTypeTableData($major_business_type_id, $regist_year_id)
    {
        // 比較結果表の作成
        //=====================================
        // 年度毎の集計
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

        $tmp_datas = FactoryDischarge::select(DB::raw(
            "co2_factory_discharge.regist_year_id AS year_id,
            co2_factory.major_business_type_id AS major_business_type_id,
            SUM(co2_factory_discharge.energy_co2) AS sum_energy_co2,
            SUM(co2_factory_discharge.noenergy_co2) AS sum_noenergy_co2,
            SUM(co2_factory_discharge.noenergy_dis_co2) AS sum_noenergy_dis_co2,
            SUM(co2_factory_discharge.ch4) AS sum_ch4,
            SUM(co2_factory_discharge.n2o) AS sum_n2o,
            SUM(co2_factory_discharge.hfc) AS sum_hfc,
            SUM(co2_factory_discharge.sf6) AS sum_sf6,
            SUM(co2_factory_discharge.sum_of_exharst) AS sum_of_exharst,
            SUM(co2_factory_discharge.power_plant_energy_co2) AS sum_power_plant_energy_co2"
            ))
            ->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id') 
            ->when($major_business_type_id!=0, function ($query) use ($major_business_type_id) {
                return $query->where('co2_factory.major_business_type_id', '=', $major_business_type_id);
                
            }) 
            ->when($regist_year_id!=0, function ($query) use ($regist_year_id) {
                return $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id);
            })  
            ->groupBy('co2_factory.major_business_type_id' ,'co2_factory_discharge.regist_year_id')
            ->get();

        $temp_data = array();
        $pre_sum = array();
        $discharges = array();
        foreach($tmp_datas as $tmp_data)
        {
            $major_business_type = MajorBusinessType::find($tmp_data->major_business_type_id);
            if ($major_business_type == null) continue;

            $temp_data['MAJOR_BUSINESS_TYPE_NAME'] = $major_business_type->name;
            $temp_data['MAJOR_BUSINESS_TYPE_ID'] = $tmp_data->major_business_type_id;
            $temp_data['YEAR_ID'] = $tmp_data->year_id;
            $temp_data['SUM_ENERGY_CO2'] = $tmp_data->sum_energy_co2;
            $temp_data['SUM_NOENERGY_CO2'] = $tmp_data->sum_noenergy_co2;
            $temp_data['SUM_NOENERGY_DIS_CO2'] = $tmp_data->sum_noenergy_dis_co2;
            $temp_data['SUM_CH4'] = $tmp_data->sum_ch4;
            $temp_data['SUM_N2O'] = $tmp_data->sum_n2o;
            $temp_data['SUM_HFC'] = $tmp_data->sum_hfc;
            $temp_data['SUM_PFC'] = $tmp_data->sum_sf6;
            $temp_data['SUM_SF6'] = $tmp_data->sum_sf6;
            $temp_data['SUM_OF_EXHARST'] = $tmp_data->sum_of_exharst;
            $temp_data['SUM_POWER_PLANT_ENERGY_CO2'] = $tmp_data->sum_power_plant_energy_co2;
            $temp_data['TOTAL_OF_EXHARST'] = $total_exharst[$year->id];
            $temp_data['PERCENT'] = round($tmp_data->sum_of_exharst/$total_exharst[$year->id]*100, 2);            

            // 増減率(％)を設定
            if ($regist_year_id==0) {
                if (isset($pre_sum[$tmp_data->major_business_type_id])) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_of_exharst - $pre_sum[$tmp_data->major_business_type_id])/$pre_sum[$tmp_data->major_business_type_id]*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
                $pre_sum[$tmp_data->major_business_type_id] = $tmp_data->sum_of_exharst;
            }
            else {
//                $pre_sum = getSumExharstByMajorBusiness($pre_sum[$tmp_data->major_business_type_id], $regist_year_id-1);
                $sum_data = FactoryDischarge::select(DB::raw("SUM(sum_of_exharst) AS sum_of_exharst"))
                    ->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id')
                    ->when($major_business_type_id!=0, function ($query) use ($major_business_type_id) {
                        return $query->where('co2_factory.major_business_type_id', '=', $major_business_type_id);
                    }) 
                    ->when($regist_year_id!=0, function ($query) use ($regist_year_id) {
                        return $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id-1);
                    })  
                    ->first();

                if ($sum_data!=null and $sum_data->sum_of_exharst!=0) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_of_exharst - $sum_data->sum_of_exharst)/$sum_data->sum_of_exharst*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
            }
            array_push($discharges, $temp_data);
        }

        return $discharges;
    }


    private static $limit_major_business_type = 6;
    /**
     * 
     */
    private function getFactoryDischargeByMajorBusinessType($years, $major_business_type_id)
    {
        $result = array();
        $year_id  = 0;
        foreach($years as $year)
        {
            $year_id = $year->id;
            $tmp_datas = Factory::select(DB::raw(
                "co2_factory.major_business_type_id AS major_business_type_id,
                co2_major_business_type.name AS major_business_name,
                co2_factory_discharge.regist_year_id AS year_id,
                SUM(co2_factory_discharge.sum_of_exharst) AS sum_of_exharst"
                ))
                ->join('co2_factory_discharge','co2_factory.id','=','co2_factory_discharge.factory_id')
                ->join('co2_major_business_type','co2_factory.major_business_type_id','=','co2_major_business_type.id')
                ->when($year_id != 0, function ($query) use ($year_id) {
                    return $query->where('co2_factory_discharge.regist_year_id', '=', $year_id);
                })
                ->when($major_business_type_id != 0, function ($query) use ($major_business_type_id) {
                    return $query->where('co2_factory.major_business_type_id', '=', $major_business_type_id);
                })
                ->groupBy('co2_factory_discharge.regist_year_id', 'co2_factory.major_business_type_id', 'co2_major_business_type.name')
                ->get();
            
            foreach($tmp_datas as $tmp_data)
            {
                $result[$tmp_data->major_business_type_id - 1]['ID'] = $tmp_data->major_business_type_id;
                $result[$tmp_data->major_business_type_id - 1]['NAME'] = $tmp_data->major_business_name;
                $result[$tmp_data->major_business_type_id - 1]['DATA'][$year->id] = $tmp_data->sum_of_exharst;
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
    private function makeMajorBusinessTypeGraphData($major_business_type_id, $regist_year_id)
    {
        $graph_dataset = array();
        $graph_labels = array();
        $tmp_graph_datas = array();

        $years = RegistYear::select()->orderBy('id', 'asc')->get();

        $major_business_type_rank = self::getFactoryDischargeByMajorBusinessType($years, $major_business_type_id);

        foreach ($years as $year)
        {
            $graph_labels[] = $year->name;

            $tmp_sum = 0;
            for ($i = 0; $i < count($major_business_type_rank); $i++)
            {
                if ($i < self::$limit_major_business_type)
                {
                    $graph_datasets[$i]['POS'] = $i;                    
                    $graph_datasets[$i]['ID'] = $major_business_type_rank[$i]['ID'];
                    $graph_datasets[$i]['NAME'] = $major_business_type_rank[$i]['NAME'];
                    $graph_datasets[$i]['DATA'][$year->id] = $major_business_type_rank[$i]['DATA'][$year->id];
                }
                else
                {
                    $tmp_sum += $major_business_type_rank[$i]['DATA'][$year->id];
                }
            }
            $graph_datasets[self::$limit_major_business_type]['POS'] = self::$limit_major_business_type;       
            $graph_datasets[self::$limit_major_business_type]['ID'] = 0;
            $graph_datasets[self::$limit_major_business_type]['NAME'] = "その他";
            $graph_datasets[self::$limit_major_business_type]['DATA'][$year->id] = $tmp_sum;
        }

        // その他も含まれているので削除
        if ($major_business_type_id != 0)
        {
            unset($graph_datasets[self::$limit_major_business_type]);
        }

//        dd($graph_datasets);
        return array($graph_labels, $graph_datasets);
    }

    /**
     * 業種別比較(大分類)
     */
    public function major_business_type(Request $request)
    {
        // 引数の処理
        $inputs = $request->all();
        $major_business_type_id = isset($inputs['major_business_type_id']) ? $inputs['major_business_type_id'] : 0;
        $regist_year_id = isset($inputs['regist_year_id']) ? $inputs['regist_year_id'] : 0;

        // 選択データの作成
        $major_business_types = MajorBusinessType::all()->pluck('name','id');
        $major_business_types->prepend('未選択', 0);    // 最初に追加
        $regist_years = RegistYear::select()->orderBy('id', 'DESC')->pluck('name','id');
        $regist_years->prepend('未選択', 0);    // 最初に追加

        // テーブルデータの作成
        $discharges = self::makeMajorBusinessTypeTableData($major_business_type_id, $regist_year_id);
        // グラフデータの作成
        list($graph_labels, $graph_datasets) = self::makeMajorBusinessTypeGraphData($major_business_type_id, $regist_year_id);

        $graph_title = "職業別(大分類) 温室効果ガス排出合計"; 

        return view('compare.major_business_type' ,compact('major_business_types', 'regist_years', 'graph_title','graph_labels', 'graph_datasets', 'discharges'));
    }

    /**
     * 業種別比較(中分類)
     */
    public function middle_business_type(Request $request)
    {
        // inputs
        $inputs = $request->all();

        $major_business_type_id = isset($inputs['major_business_type_id']) ? $inputs['major_business_type_id'] : 0; // 設定されてないときは農業  
        $middle_business_type_id = isset($inputs['middle_business_type_id']) ? $inputs['middle_business_type_id'] : 0;
        $regist_year_id = isset($inputs['regist_year_id']) ? $inputs['regist_year_id'] : 0;

        $major_business_type = MajorBusinessType::find($major_business_type_id);
        // MajorBusinessTypeが検索失敗する場合はアボート
        if ($major_business_type == null) {
            abort('404');
        }

        $middle_business_types = MiddleBusinessType::select(DB::raw("co2_middle_business_type.id AS id, co2_middle_business_type.name AS name"))
                    ->join('co2_major_business_type','co2_major_business_type.id','=','co2_middle_business_type.major_business_type_id') 
                    ->where('co2_major_business_type.id', '=', $major_business_type_id )
//                    ->groupBy('co2_middle_business_type.id')
                    ->pluck('name','id');
        $middle_business_types->prepend('未選択', 0);    // 最初に追加

        $regist_years = RegistYear::select()->orderBy('id', 'DESC')->pluck('name','id');
        $regist_years->prepend('未選択', 0);    // 最初に追加

        // 比較結果表の作成
        //=====================================
        // 年度毎の集計
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

        $tmp_datas = FactoryDischarge::select(DB::raw(
            "co2_factory_discharge.regist_year_id AS year_id,
            co2_factory.middle_business_type_id AS middle_business_type_id,
            SUM(co2_factory_discharge.energy_co2) AS sum_energy_co2,
            SUM(co2_factory_discharge.noenergy_co2) AS sum_noenergy_co2,
            SUM(co2_factory_discharge.noenergy_dis_co2) AS sum_noenergy_dis_co2,
            SUM(co2_factory_discharge.ch4) AS sum_ch4,
            SUM(co2_factory_discharge.n2o) AS sum_n2o,
            SUM(co2_factory_discharge.hfc) AS sum_hfc,
            SUM(co2_factory_discharge.sf6) AS sum_sf6,
            SUM(co2_factory_discharge.sum_of_exharst) AS sum_of_exharst,
            SUM(co2_factory_discharge.power_plant_energy_co2) AS sum_power_plant_energy_co2"
            ))
            ->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id')
            ->where('co2_factory.major_business_type_id', '=', $major_business_type_id)
            ->when($middle_business_type_id != 0, function ($query) use ($middle_business_type_id) {
                return $query->where('co2_factory.middle_business_type_id', '=', $middle_business_type_id);
                
            }) 
            ->when($regist_year_id != 0, function ($query) use ($regist_year_id) {
                return $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id);
            })  
            ->groupBy('co2_factory_discharge.regist_year_id' , 'co2_factory.middle_business_type_id')
            ->get();

        $temp_data = array();
        $pre_sum = array();
        $discharges = array();
        foreach($tmp_datas as $tmp_data)
        {
            $middle_business_type = MiddleBusinessType::find($tmp_data->middle_business_type_id);
            if ($middle_business_type == null) continue;

            $temp_data['YEAR_ID'] = $tmp_data->year_id;
            $temp_data['MIDDLE_BUSINESS_TYPE_NAME'] = $middle_business_type->name;
            $temp_data['MIDDLE_BUSINESS_TYPE_ID'] = $tmp_data->middle_business_type_id;
            $temp_data['SUM_ENERGY_CO2'] = $tmp_data->sum_energy_co2;
            $temp_data['SUM_NOENERGY_CO2'] = $tmp_data->sum_noenergy_co2;
            $temp_data['SUM_NOENERGY_DIS_CO2'] = $tmp_data->sum_noenergy_dis_co2;
            $temp_data['SUM_CH4'] = $tmp_data->sum_ch4;
            $temp_data['SUM_N2O'] = $tmp_data->sum_n2o;
            $temp_data['SUM_HFC'] = $tmp_data->sum_hfc;
            $temp_data['SUM_PFC'] = $tmp_data->sum_sf6;
            $temp_data['SUM_SF6'] = $tmp_data->sum_sf6;
            $temp_data['SUM_OF_EXHARST'] = $tmp_data->sum_of_exharst;
            $temp_data['SUM_POWER_PLANT_ENERGY_CO2'] = $tmp_data->sum_power_plant_energy_co2;
            $temp_data['TOTAL_OF_EXHARST'] = $total_exharst[$year->id];
            $temp_data['PERCENT'] = round($tmp_data->sum_of_exharst/$total_exharst[$year->id]*100, 2);            

            // 増減率(％)を設定
            if ($regist_year_id==0) {
                if (isset($pre_sum[$tmp_data->middle_business_type_id])) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_of_exharst - $pre_sum[$tmp_data->middle_business_type_id])/$pre_sum[$tmp_data->middle_business_type_id]*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
                $pre_sum[$tmp_data->middle_business_type_id] = $tmp_data->sum_of_exharst;
            }
            else {
//                $pre_sum = getSumExharstByMajorBusiness($pre_sum[$tmp_data->major_business_type_id], $regist_year_id-1);
                $sum_data = FactoryDischarge::select(DB::raw("SUM(sum_of_exharst) AS sum_of_exharst"))
                    ->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id')
                    ->when($middle_business_type_id != 0, function ($query) use ($middle_business_type_id) {
                        return $query->where('co2_factory.middle_business_type_id', '=', $middle_business_type_id);
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
            array_push($discharges, $temp_data);
        }

        //  ここからグラフ表示用のデータ
        //=================================
        $graph_datasets = array();
        $graph_labels = array();

        $pos = 0;
        foreach ($years as $year)
        {
            $tmp_graph_datas = FactoryDischarge::select(DB::raw(
                    "co2_factory_discharge.regist_year_id AS regist_year_id, 
                    co2_factory.middle_business_type_id AS middle_business_type_id,
                    SUM(co2_factory_discharge.sum_of_exharst) AS total_sum_of_exharst"
                ))
                ->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id')
                ->where('co2_factory.major_business_type_id', '=', $major_business_type_id)
                ->when($middle_business_type_id != 0, function ($query) use ($middle_business_type_id) {
                    return $query->where('co2_factory.middle_business_type_id', '=', $middle_business_type_id);
                    
                }) 
                ->where('co2_factory_discharge.regist_year_id', '=', $year->id)
                ->groupBy('co2_factory.middle_business_type_id', 'co2_factory_discharge.regist_year_id' ,'co2_factory_discharge.factory_id')
                ->get();

            $graph_labels[] = $year->name;
            foreach ($tmp_graph_datas as $tmp_graph_data)
            {
                if (!isset($graph_item_pos[$tmp_graph_data->middle_business_type_id]))
                {
                    $graph_item_pos[$tmp_graph_data->middle_business_type_id] = $pos;
                    $pos++;
                }
                $middle_business_type = MiddleBusinessType::find($tmp_graph_data->middle_business_type_id);
                $graph_datasets[$graph_item_pos[$tmp_graph_data->middle_business_type_id]]['POS'] = $graph_item_pos[$tmp_graph_data->middle_business_type_id];
                $graph_datasets[$graph_item_pos[$tmp_graph_data->middle_business_type_id]]['NAME'] = $middle_business_type->name;
                $graph_datasets[$graph_item_pos[$tmp_graph_data->middle_business_type_id]]['DATA'][$tmp_graph_data->regist_year_id] = $tmp_graph_data->total_sum_of_exharst;
            }
        }

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

        $graph_title = "職業別(中分類) 温室効果ガス排出合計"; 

        return view('compare.middle_business_type' ,compact('major_business_type', 'middle_business_types', 'regist_years', 'regist_year_id', 'discharges', 'graph_title' ,'graph_labels', 'graph_datasets'));
    }

    //========================================================

    /**
     * 
     */

    /**
     * 比較結果表の作成
     */
    private function makeFactoryByMiddleBusinessTypeTableData($major_business_type_id, $middle_business_type_id, $regist_year_id)
    {
        // 問い合わせSQLを構築
        $query = FactoryDischarge::query();
        $query->select('*','co2_factory_discharge.regist_year_id as discharge_regist_year_id');
        $query->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id');
        if ($major_business_type_id != 0)
        {
            $query->where('co2_factory.major_business_type_id', '=', $major_business_type_id);
        }
        if ($middle_business_type_id != 0)
        {
            $query->where('co2_factory.middle_business_type_id', '=', $middle_business_type_id);
        }
        if ($regist_year_id != 0)
        {
            $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id);
        }
//        $query->groupBy('co2_factory_discharge.regist_year_id', 'co2_factory_discharge.id', 'co2_factory_discharge.factory_id');
        $query->orderBy('co2_factory_discharge.sum_of_exharst', 'DESC');
        $table_count = $query->count();
        $table_datasets = $query->paginate(10);

//        dd($table_count);
        return array($table_count, $table_datasets);
    }

    /**
     * 
     */
    public function factory_by_middle_business_type(Request $request)
    {
        // 引数の処理
        $inputs = $request->all();
        $major_business_type_id = isset($inputs['major']) ? $inputs['major'] : 0;
        $middle_business_type_id = isset($inputs['middle']) ? $inputs['middle'] : 0;
        $regist_year_id = isset($inputs['year']) ? $inputs['year'] : 0;

        // factoryy_idが設定されてない場合アボート
        if ($major_business_type_id == 0) {
            abort('404');
        }
        
        $major_business_type = MajorBusinessType::find($major_business_type_id);
        if ($major_business_type == null) {
            abort('404');
        }

        // factoryy_idが設定されてない場合アボート
        if ($major_business_type_id == 0) {
            abort('404');
        }
                
        $middle_business_type = MiddleBusinessType::find($middle_business_type_id);
        if ($major_business_type == null) {
            abort('404');
        }

        // テーブルデータの作成
        list($table_count, $table_datasets) = self::makeFactoryByMiddleBusinessTypeTableData($major_business_type_id, $middle_business_type_id, $regist_year_id);

        $pagement_params =  $inputs;
        unset($pagement_params['_token']);

        // ToDo: カウントがおかしい!! 要修正 
        return view('compare.factory_by_middle_business_type' ,compact('major_business_type', 'middle_business_type', 'regist_year_id', 'table_count', 'table_datasets', 'pagement_params'));
    }
}
