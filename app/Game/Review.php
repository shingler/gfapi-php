<?php

namespace App\Game;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = "magzine_scores";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function shelf() {
        return $this->belongsTo("App\Game\Shelf", "gameId", "gameId");
    }
}
