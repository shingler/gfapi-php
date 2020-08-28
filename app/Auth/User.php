<?php

namespace App\Auth;

use App\Aliyunoss\Manager;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use Notifiable;
    protected $table = "auth_user";
    protected $primaryKey = "id";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'is_superuser', 'is_staff', 'is_active',
        'last_login', 'date_joined', 'first_name', 'last_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function userprofile() {
        return $this->hasOne('App\Auth\Userprofile', 'user_id', 'id');
    }

    public static function createNew(array $oauth_data, string $scope) {
        if ($scope == "alipay") {
            return self::createNewFromAlipay($oauth_data);
        }
    }

    public static function createNewFromAlipay(array $oauth_data) {
        $inst = User::create([
            'username' => $oauth_data["nick_name"],
            'password' => self::makePassword(),
            'email' => '',
            'first_name' => '',
            'last_name' => '',
            'is_superuser' => 0,
            'is_staff' => 0,
            'is_active' => 1,
            'last_login' => now(),
            'date_joined' => now()
        ]);

        Userprofile::create([
            'avatar' => $oauth_data["avatar"],
            'nickname' => $oauth_data["nick_name"],
            'created' => now()->getTimestamp(),
            'user_id' => $inst->id
        ]);
        return $inst;
    }

    /**
     * 实现和django相同的密码生成机制
     * @param string $password
     * @return pdkpbkdf2_sha256加密的密码或为空密码加密的叹号加随机40位密码
     */
    public static function makePassword(string $password=''):string {
        if (empty($password)) {
            return sprintf("!%s", self::_random_string(40));
        } else {
            $algorithm = "pbkdf2_sha256";
            $iterations = 10000;
            $salt = self::_random_string();
            $hash = hash_pbkdf2("sha256", $password, $salt, $iterations);
            $hash = base64_encode($hash);
            return sprintf("%s$%d$%s$%s", $algorithm, $iterations, $salt, $hash);
        }
    }

    protected static function _random_string(int $length=12) {
        $res = "";
        $allowed_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $allowed_arr = range(0, $length);

        for ($i = 0; $i < $length; $i++) {
            shuffle($allowed_arr);
            $res .= $allowed_chars[array_rand($allowed_arr)];
        }

        return $res;
    }

    /**
     * 更新头像。格式：avatar/uid?v=timestamp
     */
    public function updateAvatar() {
        $new_key = sprintf("%s/%d?v=%d", "avatar", $this->id, now()->getTimestamp());
        $ossManager = new Manager();
        $this->userprofile->avatar = $ossManager->getUrl($new_key, "avatar", "", "https");;
        $this->userprofile->save();
    }

}
