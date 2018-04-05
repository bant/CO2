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

    public function regist_year()
    {
        return $this->belongsTo('App\RegistYear','regist_year_id');
    }

    public function company_division()
    {
        return $this->belongsTo('App\CompanyDivision','company_division_id');
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
