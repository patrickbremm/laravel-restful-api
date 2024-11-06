<?php

namespace App\Http\Controllers;

use App\Events\PostDeleted;
use App\Models\Post;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json([
            "ok" => true, 
            'posts' => PostResource::collection($posts) // Usa a coleção de recursos
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id(); // Associando o post ao usuário autenticado
        $post = Post::create($validated);
        return response()->json([
            "ok" => true, 
            "message" => "Post criado com sucesso!",
            "post" => new PostResource($post)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json([
            "ok" => true, 
            "post" => new PostResource($post)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        // Validar se o usuário foi ele que criou e assim pode excluir o post (PostPolicy.php)
        Gate::authorize('canChangePost', $post);
        // Validação dos dados (Ele pega as rules da classe PostRequest importada)
        $validatedData = $request->validated();
        // É necessário fazer o fill para ai validar se teve alteração em algum atributo para ser atualizado
        $post->fill($validatedData);
        // Atualização do put
        // isDirty valida se teve alguma alteração dos atributos, se sim ai ele chama o BD
        if ($post->isDirty()) {
            $post->save();
            // Retorno de uma resposta de sucesso
            return response()->json([
                'message' => 'Post atualizado com sucesso!',
                'post' => new PostResource($post)
            ], 200);
        }
        return response()->json([
            'message' => 'Nenhuma mudança detectada.',
            'post' => new PostResource($post)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Essa validação abaixo não faz sentido, dado que o Laravel valida antes se o POST não é encontrado
        // a partir disso foi implementado em bootstrap/app.php o retorno para quando der 404 no endpoint /posts
        // if (!$post) {
        //     return response()->json([
        //         'message' => 'Post não encontrado.'
        //     ], 404);
        // }
        // Validar se o usuário foi ele que criou e assim pode excluir o post (PostPolicy.php)
        Gate::authorize('canChangePost', $post);
        // Soft delete do post
        $post->delete();
        // Disparando o evento PostDeleted
        event(new PostDeleted($post));
        return response()->json([
            'message' => 'Post excluído (soft delete) com sucesso!'
        ], 200);
    }
}
