<?php

namespace App\Http\Controllers;

use App\MajorBusinessType;
use App\RegistYear;
use App\FactoryDischarge;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompareController extends Controller
{
    /**
     * 特定の年度・大分類の合計を計算する
     */
    private function getSumExharstByMajorBusiness($major_business_type_id, $year_id)
    {
        $sum_data = FactoryDischarge::select(DB::raw("SUM(sum_of_exharst) AS sum_of_exharst"))
        ->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id')
        ->when($major_business_type_id!=0, function ($query) use ($major_business_type_id) {
            return $query->where('co2_factory.major_business_type_id', '=', $major_business_type_id);
            
        }) 
        ->when($regist_year_id!=0, function ($query) use ($regist_year_id) {
            return $query->where('co2_factory_discharge.regist_year_id', '=', $regist_year_id);
        })  
        ->first();
        
        if ($sum_data!=null)
        {
            return $sum_data->sum_of_exharst;
        }
        else
        {
            return 0;
        }
    }

    /**
     * 事業所検索(indexより)
     */
    public function major_business_type(Request $request)
    {
        // inputs
        $inputs = $request->all();

        $major_business_type_id = isset($inputs['major_business_type_id']) ? $inputs['major_business_type_id'] : 1;
        $regist_year_id = isset($inputs['regist_year_id']) ? $inputs['regist_year_id'] : 0;


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
            ->groupBy('co2_factory_discharge.regist_year_id' , 'co2_factory.major_business_type_id')
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

                /*
                    if ($sum_data!=null)
                    {
                        $pre_sum = $sum_data->sum_of_exharst;
                    }
                    else
                    {
                        $pre_sum = 0;
                    }

                if ($pre_sum!=0) {
                    $temp_data['PRE_PERCENT'] = round(($tmp_data->sum_of_exharst - $pre_sum)/$pre_sum*100 ,2);
                }
                else {
                    $temp_data['PRE_PERCENT'] = -99999999;
                }
                */

            }
            array_push($discharges, $temp_data);
        }

        // グラフデータの作成
        //====================================
        $graph_dataset = array();
        $graph_labels = array();
        $tmp_graph_datas = array();

        $major_business_type = MajorBusinessType::find($major_business_type_id);

        $graph_dataset['NAME'] = $major_business_type->name;

        $tmp_graph_datas =  FactoryDischarge::select(DB::raw("SUM(sum_of_exharst) AS sum_of_exharst"))
                ->join('co2_factory','co2_factory.id','=','co2_factory_discharge.factory_id') 
                ->where('co2_factory.major_business_type_id', '=', $major_business_type_id)
                ->groupBy('co2_factory_discharge.regist_year_id')
                ->get();

        foreach($tmp_graph_datas as $tmp_graph_data)
        {
            $graph_dataset['DATA'][] = $tmp_graph_data->sum_of_exharst;
        }

        foreach ($years as $year)
        {
            $graph_labels[] = $year->name;
        }

        $major_business_types = MajorBusinessType::all()->pluck('name','id');
 
        $regist_years = RegistYear::select()->orderBy('id', 'DESC')->pluck('name','id');
        $regist_years->prepend('未選択', 0);    // 最初に追加

        // ToDO
        return view('compare.major_business_type' ,compact('major_business_types', 'graph_labels', 'graph_dataset', 'regist_years', 'discharges'));
    }
    
}
