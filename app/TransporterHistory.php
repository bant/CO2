<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransporterHistory extends Model
{
    /**
     * モデルに関連付けるデータベースのテーブルを指定
     *
     * @var string
     */
    protected $table = 'co2_transporter_history';
}
