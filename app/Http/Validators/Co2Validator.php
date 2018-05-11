<?php
namespace App\Http\Validators;

use Illuminate\Validation\Validator;

class Co2Validator extends Validator
{
    /*
    * 
    *  空白か数値なら真を返す関数
    */
    public function validateNumeric2($attribute, $value, $parameters)
    {
        if ($value=='')
        {
            return true;
        }

        return is_numeric($value);
    }
}