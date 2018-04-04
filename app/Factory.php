<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
     /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_factory';
}
