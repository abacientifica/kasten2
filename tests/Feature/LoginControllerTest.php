<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Carbon::setTestNow(Carbon::createFromFormat('Y-m-d H:i:s', '2019-06-06 18:00:00'));

        $response = $this->post(route('login'), [
            'Usuario' => 'diegoa',
            'password' => 'sistemas2015',
        ]);
        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('usuarios', [
            'Usuario' => 'diegoa',
            'last_login' => '2019-06-06 18:00:00',
            'last_ipclient' => '127.0.0.1'
        ]);
    }
}
