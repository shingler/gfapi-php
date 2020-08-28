<?php

namespace Tests\Feature\User;

use function GuzzleHttp\Psr7\parse_header;
use function Sodium\compare;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeAvatarTest extends TestCase
{
    protected $token_obj;
    public function setUp() {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->token_obj = parent::createTestUserToken();
    }
    public function tearDown() {
        parent::eraseTestUser($this->token_obj->user_id);
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testChangeAvatar() {
        $token = $this->token_obj->token;
        $response = $this->json("post", route("user.changeavatar"), compact('token'));
        $response->assertStatus(200);
        $result = $response->json();
        $this->assertEquals(1, $result["status"]);
        $this->assertNotEmpty($result["data"]);
        $readable = $this->withHeaders(["Content-Type: image/jpeg"])->get($result["data"]);
        $readable->assertStatus(200);
    }
}
