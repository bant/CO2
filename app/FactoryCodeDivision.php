<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FactoryCodeDivision extends Model
{
     /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_factory_code_division';

        /**
     * 会社テーブルと関連付け
     */
    public function factory()
    {
        return $this->belongsTo('App\Factory', 'id', 'factory_id'); 
    }



}
