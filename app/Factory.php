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
     * Get the Company's name.
     *
     * @param  string  $value
     * @return string
     */
    public function getNameAttribute($value)
    {
        return mb_strimwidth($value, 0, 128, "..");
    }

    /**
     * 会社テーブルと関連付け
     */
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id'); 
    }

    /**
     * 工場分類コードテーブル関連付け
     */
    public function factory_code_division()
    {
        return $this->hasOne('App\FactoryCodeDivision', 'id', 'factory_code_division_id');
    }

    /**
     * 都道府県テーブル関連付け
     */
    public function pref()
    {
        return $this->hasOne('App\Pref', 'id', 'pref_id');
    }

    /**
     * 大業種テーブル関連付け
     */
    public function major_business_type()
    {
        return $this->hasOne('App\MajorBusinessType', 'id', 'major_business_type_id');
    }
    
    /**
     * 中業種テーブル関連付け
     */
    public function middle_business_type()
    {
        return $this->hasOne('App\MiddleBusinessType', 'id', 'middle_business_type_id');
    }

    /**
     * 業種テーブル関連付け
     */
    public function business_type()
    {
        return $this->hasOne('App\BusinessType', 'id', 'business_type_id');
    }

    /**
     * 年度テーブル関連付け
     */
    public function regist_year()
    {
        return $this->hasOne('App\RegistYear', 'id', 'regist_year_id');
    }

    /**
     * 中業種テーブル関連付け
     */
    public function factory_discharges()
    {
        return $this->hasMany('App\FactoryDischarge', 'factory_id', 'id');
    }

    /**
     * 
     */
    public function getPrePercent($year_id)
    {
        $discharge = FactoryDischarge::where('factory_id', '=', $this->id)->where('regist_year_id', '=', $year_id)->first();

        if ($discharge == null)
            return 0;
        else
            return round($discharge->pre_percent, 2);   
    }

    /**
     * 
     */
    public function getSumOfExharst($year_id)
    {
        $discharge = FactoryDischarge::where('factory_id', '=', $this->id)->where('regist_year_id', '=', $year_id)->first();

        if ($discharge == null)
            return 0;
        else
            return $discharge->sum_of_exharst;   
    }

    /**
     * 
     */
    public function getDischargeByYear($year_id)
    {
        $discharge = FactoryDischarge::where('factory_id', '=', $this->id)->where('regist_year_id', '=', $year_id)->first();
        return $discharge;   
    }

}
