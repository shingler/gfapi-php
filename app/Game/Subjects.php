<?php

namespace App\Game;

use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    protected $table = "subjects";
    protected $primaryKey = "id";
    public $timestamps = false;

    /**
     * 获取最新的人民币报价
     */
    public function getLastPriceCNY():float {
        $currency = new Currency();
        $rate = $currency->where('currency', '=', $this->currency)->value("rate");
        return floatval($this->latestPrice * $rate);
    }
}
