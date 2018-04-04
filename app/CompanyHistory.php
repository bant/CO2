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
}
