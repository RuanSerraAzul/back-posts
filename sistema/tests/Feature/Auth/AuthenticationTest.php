<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;
    private $headers;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        $this->headers = ['Authorization' => 'Bearer ' . $this->token];
    }

    public function test_usuario_pode_se_registrar()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Teste Usuario',
            'email' => 'teste@email.com',
            'password' => 'senha123'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token'
                ]
            ]);
    }

    public function test_usuario_pode_fazer_login()
    {
        $user = User::factory()->create([
            'email' => 'teste@email.com',
            'password' => bcrypt('senha123')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'teste@email.com',
            'password' => 'senha123'
        ]);

        $response->assertStatus(200);
    }

    public function test_usuario_pode_fazer_logout()
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logout realizado com sucesso'
            ]);
    }

    public function test_usuario_pode_atualizar_token()
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/v1/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'token'
                ]
            ]);
    }

    public function test_usuario_pode_obter_dados_perfil()
    {
        $response = $this->withHeaders($this->headers)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email'
                ]
            ]);
    }
} 