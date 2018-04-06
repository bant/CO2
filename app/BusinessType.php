<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_business_type';

    /**
     * 会社テーブルと関連付け
     */
    public function factory()
    {
        return $this->belongsTo('App\Factory', 'business_type_id' , 'id'); 
    }
}
