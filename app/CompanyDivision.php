<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyDivision extends Model
{
    /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_company_division';

    /**
     * 会社テーブルと関連付け
     */
    public function company()
    {
        return $this->belongsTo('App\Company'); 
    }

}
