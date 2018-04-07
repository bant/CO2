<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Factory;
use App\Pref;
use App\MajorBusinessType;

class FactoryController extends Controller
{
    /**
     * 事業所検索(indexより)
     */
    public function search(Request $request)
    {
        $factory_prefs = Pref::all()->pluck('name','id');
        $factory_prefs->prepend('全都道府県', 0);    // 最初に追加
    
        $major_business_types = MajorBusinessType::all()->pluck('name','id');
        $major_business_types->prepend('未選択', 0);    // 最初に追加

        return view('factory.search', compact('factory_prefs', 'major_business_types'));
    }


    /**
     * 事業所リスト(searchより)
     */
    public function list(Request $request)
    {
        // inputs
        $inputs = $request->all();

        $factory_name = isset($inputs['factory_name']) ? trim($inputs['factory_name']) : null;
        $factory_pref_id = isset($inputs['factory_pref_id']) ? $inputs['factory_pref_id'] : 0;
        $factory_address = isset($inputs['factory_address']) ? trim($inputs['factory_address']) : null;
        $major_business_type_id = isset($inputs['major_business_type_id']) ? $inputs['major_business_type_id'] : 0;

        // 問い合わせSQLを構築
        $query = Factory::query();
        if (!is_null($factory_name))
        {
            $query->where('name','like', "%$factory_name%");
        }
        if ($factory_pref_id != '0')
        {
            $query->where('factory_pref_id', '=', $factory_pref_id);
        }
        if (!is_null($factory_address))
        {
            $query->where('address','like', "%$factory_address%");
        }

        if ($major_business_type_id != '0')
        {
            $query->where('major_business_type_id', '=', $major_business_type_id);
        }
        $query->orderBy('regist_year_id', 'DESC');
        $query->distinct('name');
        $factory_count = $query->count();
        $factories = $query->paginate(10);

        $factory_prefs = Pref::all()->pluck('name','id');
        $factory_prefs->prepend('全都道府県', 0);    // 最初に追加
    
        $major_business_types = MajorBusinessType::all()->pluck('name','id');
        $major_business_types->prepend('未選択', 0);    // 最初に追加

        $pagement_params =  $inputs;
        unset($pagement_params['_token']);
 
        return view('factory.list', compact('factory_prefs', 'major_business_types', 'factory_count','factories', 'pagement_params'));
    }
}
