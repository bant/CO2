<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
     /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_factory';

    /**
     * 
     */
    public function getSumOfExharst($year_id)
    {
        $factory_discharge = FactoryDischarge::where('factory_id', '=', $this->id)->where('regist_year_id', '=', $year_id)->first();

        if ($factory_discharge == null)
            return 0;
        else
            return $factory_discharge->sum_of_exharst;   
    }
}
