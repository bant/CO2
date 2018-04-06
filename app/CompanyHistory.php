<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyHistory extends Model
{
     /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_company_history';

    /**
     * 会社テーブルと関連付け
     */
    public function company()
    {
        return $this->belongsTo('App\Company', 'id', 'company_id'); 
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
        return $this->hasOne('App\CompanyDivision','id','company_division_id');
    }


}
