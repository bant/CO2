<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleBusinessType extends Model
{
    /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_middle_business_type';

    /**
     * 大分類テーブルと関連付け
     */
    public function major_business_type()
    {
        return $this->belongsTo('App\MajorBusinessType', 'id', 'major_business_type_id'); 
    }
}
