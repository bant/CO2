<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PrtrCo2;

class Company extends Model
{
    /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_company';

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
     * 登録年度テーブル関連付け
     */
    public function regist_year()
    {
        return $this->hasOne('App\RegistYear', 'id', 'regist_year_id');
    }

    /**
     * 会社分類テーブル関連付け
     */
    public function company_division()
    {
        return $this->hasOne('App\CompanyDivision', 'id', 'company_division_id');
    }

    /**
     * 会社登録履歴テーブル関連付け
     */
    public function company_histories()
    {
        return $this->hasMany('App\CompanyHistory','company_id', 'id');
    }

    /**
     * 工場テーブル関連付け
     */
    public function factories()
    {
//        return $this->hasMany('App\Factory','company_id', 'id');
        return $this->hasMany('App\Factory');
    }


    /**
     * 輸送者テーブル関連付け
     */
    public function transporters()
    {
//        return $this->hasMany('App\Factory','company_id', 'id');
        return $this->hasMany('App\Transporter');
    }



    public function getFactoryCount()
    {
        return Factory::where('company_id', $this->id)->count();
    }

    public function getPrtrCo2()
    {
        $prtr = PrtrCo2::where('co2_company_id', $this->id)->first();

        if ($prtr == null)
            return 0;
        else
            return $prtr->prtr_company_id;
    }

}
