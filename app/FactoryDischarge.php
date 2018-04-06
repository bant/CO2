<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FactoryDischarge extends Model
{
     /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_factory_discharge';

    /**
     * 工場テーブルと関連付け
     */
    public function factory()
    {
        return $this->belongsTo('App\Factory', 'id', 'factory_id'); 
    }

    /**
     * 登録年度テーブル関連付け
     */
    public function regist_year()
    {
        return $this->hasOne('App\RegistYear', 'id', 'regist_year_id');
    }

}
