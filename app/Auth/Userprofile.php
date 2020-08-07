<?php

namespace App\Auth;

use Illuminate\Database\Eloquent\Model;

class Userprofile extends Model
{
    protected $table = "user_profile";
    protected $primaryKey = "id";
    public $timestamps = false;
    public $fillable = ['avatar', 'nickname', 'created', 'user_id'];

}
