<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    /**
    * モデルに関連付けるデータベースのテーブルを指定
    *
    * @var string
    */
   protected $table = 'co2_transporter';

    /**
     * 会社テーブルと関連付け
     */
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id'); 
    }

    /**
     * 輸送者コードテーブル関連付け
     */
    public function factory_code_division()
    {
        return $this->hasOne('App\TransporterDivision', 'id', 'transporter_division_id');
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
     * 中業種テーブル関連付け
     */
    public function business_type()
    {
        return $this->hasOne('App\BusinessType', 'id', 'business_type_id');
    }

    /**
     * 登録年度テーブル関連付け
     */
    public function regist_year()
    {
        return $this->hasOne('App\RegistYear', 'id', 'regist_year_id');
    }

    /**
     * CO2排出量を取り出す
     */
    public function getEnergyCO2($year_id)
    {
        $discharge = TransporterDischarge::where('transporter_id', '=', $this->id)->where('regist_year_id', '=', $year_id)->first();

        if ($discharge == null)
            return 0;
        else
            return $discharge->energy_co2;   
    }

}
