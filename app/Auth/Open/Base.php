<?php
namespace App\Auth\Open;

/**
 * 第三方登录接口
 * Interface Base
 * @package App\Auth\Open
 */
interface Base
{
    /**
     * 使用第三方平台的oauth code换取access_token
     * @param string $auth_code
     * @return mixed
     */
    public function getAccessToken(string $auth_code);

    /**
     * 使用第三方平台的access_token获取用户信息
     * @param string $access_token
     * @return mixed
     */
    public function getUserData(string $access_token);
}