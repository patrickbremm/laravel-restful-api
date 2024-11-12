<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\PostController;
use App\Repositories\PostRepositoryInterface;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    // Método setUp() que será executado antes de cada teste
    protected function setUp(): void
    {
        parent::setUp();  // Chama o método setUp() da classe base (se necessário)

        // Desabilitando o comportamento de fillable/guarded
        Post::unguard();
        User::unguard();
    }

    /**
     * Teste para garantir que o método index retorna os posts corretamente.
     * @test
     */
    public function test_index_returns_all_posts()
    {
        // Mockando o repositório de posts
        $mockPostRepository = $this->createMock(PostRepositoryInterface::class);
        // Dados simulados de posts
        $user1 = new User(['id' => 1, 'name' => 'Name 1']);
        $posts = new Collection([
            new Post([
                'id' => 1, 
                'title' => 'Post 1', 
                'author' => 'Author 1', 
                'excerpt' => 'Excerpt 1', 
                'text' => 'Text 1',
                'user' => $user1
            ]),
            new Post([
                'id' => 2, 
                'title' => 'Post 2', 
                'author' => 'Author 2', 
                'excerpt' => 'Excerpt 2', 
                'text' => 'Text 2',
                'user' => new User(['id' => 2, 'name' => 'Name 2'])
            ])
        ]);
        // Definindo o que o mock deve retornar ao ser chamado
        $mockPostRepository->method('all')->willReturn($posts);

        // Criando uma instância do controlador com o repositório mockado
        $controller = new PostController($mockPostRepository);
        // Chamando o método index do controlador
        $response = $controller->index();
        // Verificando se a resposta tem o formato esperado
        $this->assertEquals(200, $response->getStatusCode());
        // Validando a estrutura JSON manualmente
        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('posts', $responseData);
        foreach ($responseData['posts'] as $post) {
            $this->assertArrayHasKey('title', $post);
            $this->assertArrayHasKey('author', $post);
            $this->assertArrayHasKey('excerpt', $post);
            $this->assertArrayHasKey('text', $post);
            $this->assertArrayHasKey('user', $post);
            // Verificando se 'user' não é null
            $this->assertNotNull($post['user'], "User key is null for post with title {$post['title']}");
            $this->assertArrayHasKey('id', $post['user']);
            $this->assertArrayHasKey('name', $post['user']);
        }
    }
}
