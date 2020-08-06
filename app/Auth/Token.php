<?php

namespace App\Auth;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = "auth_token";
    protected $primaryKey = "id";
    public $timestamps = false;
    // user_id这个字段被laravel保护起来了，需要设置白名单
    protected $fillable = ['user_id', 'expired', 'token', 'scope'];
}
