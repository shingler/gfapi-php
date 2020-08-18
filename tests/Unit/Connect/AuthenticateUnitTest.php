<?php
/**
 * 使用未过期的refresh_code去获取新的access_token。测试用例无法获得第三方平台的auth_code
 */
namespace Tests\Unit\Connect;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticateUnitTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
}
