<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function regist_year()
    {
        return $this->belongsTo('App\RegistYear','regist_year_id');
    }

    public function company_division()
    {
        return $this->belongsTo('App\CompanyDivision','company_division_id');
    }


}
