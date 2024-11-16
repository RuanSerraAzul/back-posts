<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
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

    public function test_usuario_pode_criar_post()
    {
        $postData = [
            'title' => 'Título do Post',
            'content' => 'Conteúdo do post',
            'author' => 'Autor do Post',
            'tags' => ['tag1', 'tag2']
        ];

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/v1/posts', $postData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Post criado com sucesso'
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'content',
                    'author',
                    'tags'
                ]
            ]);
    }

    public function test_usuario_nao_autenticado_nao_pode_criar_post()
    {
        $response = $this->postJson('/api/v1/posts', []);
        
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_usuario_pode_listar_posts()
    {
        Post::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Posts recuperados com sucesso'
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'author',
                        'tags'
                    ]
                ]
            ]);
    }

    public function test_usuario_pode_atualizar_seu_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeaders($this->headers)
            ->putJson("/api/v1/posts/{$post->id}", [
                'title' => 'Título Atualizado'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Post atualizado com sucesso',
                'data' => [
                    'title' => 'Título Atualizado'
                ]
            ]);
    }

    public function test_usuario_nao_pode_atualizar_post_de_outro_usuario()
    {
        $outroUsuario = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $outroUsuario->id
        ]);

        $response = $this->withHeaders($this->headers)
            ->putJson("/api/v1/posts/{$post->id}", [
                'title' => 'Título Atualizado'
            ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Você não tem permissão para atualizar este post'
            ]);
    }

    public function test_usuario_pode_deletar_seu_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Post removido com sucesso'
            ]);
    }

    public function test_listar_posts_com_tag()
    {
        Post::factory(3)->create(['user_id' => $this->user->id, 'tags' => ['php']]);
        Post::factory(2)->create(['user_id' => $this->user->id, 'tags' => ['laravel']]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/v1/posts?tag=php');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_validacao_criacao_post()
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/v1/posts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'author', 'tags']);
    }

    public function test_atualizacao_parcial_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Título Original'
        ]);

        $response = $this->withHeaders($this->headers)
            ->putJson("/api/v1/posts/{$post->id}", [
                'title' => 'Novo Título'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Novo Título'
                ]
            ]);
    }

    public function test_deletar_post_inexistente()
    {
        $response = $this->withHeaders($this->headers)
            ->deleteJson('/api/v1/posts/999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Post não encontrado'
            ]);
    }
} 