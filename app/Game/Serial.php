<?php

namespace App\Game;

use Illuminate\Database\Eloquent\Model;

class Serial extends Model
{
    protected $table = "serial";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function shelf() {
        return $this->hasMany('App\Game\Shelf', "gameId", "shelf_id");
    }

    public function getRelatedGames(array $except=[]) {
        $shelf_model = Shelf::where('show', '=', 1)->where('serial_id', '=', $this->id);
        if (count($except) > 0) {
            $shelf_model->whereNotIn('gameId', $except);
        }
        $shelf_list = $shelf_model->orderBy('gameId', 'desc')->get();
        foreach ($shelf_list as $k => $item) {
            $shelf_list[$k]->loadCoverUrl();
        }
        return $shelf_list;
    }
}
