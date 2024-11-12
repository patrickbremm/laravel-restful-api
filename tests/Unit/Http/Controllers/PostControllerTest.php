<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\PostController;
use App\Repositories\PostRepositoryInterface;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Mockery;
use Throwable;
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

    /**
     * @test
     */
    public function test_index_returns_empty_posts()
    {
        // Mockando o repositório de posts
        $mockPostRepository = $this->createMock(PostRepositoryInterface::class);
        // Dados simulados de posts
        $user1 = new User(['id' => 1, 'name' => 'Name 1']);
        $posts = new Collection();
        // Definindo o que o mock deve retornar ao ser chamado
        $mockPostRepository->method('all')->willReturn($posts);

        // Criando uma instância do controlador com o repositório mockado
        $controller = new PostController($mockPostRepository);
        // Chamando o método index do controlador
        $response = $controller->index();
        // Verificando se a resposta tem o formato esperado
        $this->assertEquals(200, $response->getStatusCode());
        // Convertendo a resposta JSON para um array para validação
        $responseData = $response->getData(true);
        // Verificando se a chave 'posts' está vazia
        $this->assertTrue($responseData['ok']);
        $this->assertIsArray($responseData['posts']);
        $this->assertEmpty($responseData['posts']);
    }

    /**
     * @test
     */
    public function test_index_handles_exception()
    {
        // Step 1: Mock the repository and make it throw an exception
        $postRepositoryMock = Mockery::mock(PostRepositoryInterface::class);
        $postRepositoryMock->shouldReceive('all')
            ->once()
            ->andThrow(new \Exception("Database error"));
        // Step 2: Create an instance of PostController with the mock
        $controller = new PostController($postRepositoryMock);
        // Step 3: Mock the Log facade to capture any logged errors
        Log::shouldReceive('error')->once()->with('Failed to fetch posts: Database error');
        // Step 4: Call the index method and capture the response
        $response = $controller->index();
        // Step 5: Assert the response is a JSON response with the expected structure
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'ok' => false,
            'message' => 'Failed to retrieve posts. Please try again later.',
        ], $response->getData(true));
    }
}
