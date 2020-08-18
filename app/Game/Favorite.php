<?php

namespace App\Game;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = "favorite";
    protected $primaryKey = "id";
    public $timestamps = false;

    public $fillable = ['user_id', 'shelf_id', 'state', 'created', 'updated'];

    public function shelf() {
        $shelf = $this->hasOne('App\Game\Shelf', 'gameId', 'shelf_id');
//        $shelf = $this->belongsTo('App\Game\Shelf', 'shelf_id', 'gameId');
        return $shelf;
    }

    public function user() {
        return $this->belongsTo('App\Auth\User', 'user_id', 'id');
    }
}
