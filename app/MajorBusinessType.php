<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MajorBusinessType extends Model
{
    /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_major_business_type';

    /**
     * 中分類テーブル関連付け
     */
    public function middle_business_types()
    {
        return $this->hasMany('App\MiddleBusinessType', 'major_business_type_id', 'id');
    }
    
}
