<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="API Endpoints para gerenciamento de posts"
 * )
 */
class PostController extends Controller
{
    use ApiResponse;

    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @OA\Get(
     *     path="/posts",
     *     tags={"Posts"},
     *     summary="Lista todos os posts",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts recuperada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Posts recuperados com sucesso"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Post"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $posts = $request->has('tag') 
                ? $this->postRepository->findByTag($request->tag)
                : $this->postRepository->all();
            
            return $this->success($posts, 'Posts recuperados com sucesso');
        } catch (\Exception $e) {
            return $this->error('Erro ao recuperar posts', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/posts",
     *     tags={"Posts"},
     *     summary="Cria um novo post",
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePostRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post criado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function store(StorePostRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            
            $post = $this->postRepository->create($data);
            
            return $this->success($post, 'Post criado com sucesso', 201);
        } catch (\Exception $e) {
            return $this->error('Erro ao criar post', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/posts/{id}",
     *     tags={"Posts"},
     *     summary="Atualiza um post",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePostRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post atualizado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function update(UpdatePostRequest $request, $id)
    {
        try {
            $post = $this->postRepository->find($id);

            if (!$post) {
                return $this->notFound('Post não encontrado');
            }

            if ($post->user_id !== Auth::id()) {
                return $this->unauthorized('Você não tem permissão para atualizar este post');
            }

            $post = $this->postRepository->update($id, $request->validated());
            
            return $this->success($post, 'Post atualizado com sucesso');
        } catch (\Exception $e) {
            return $this->error('Erro ao atualizar post', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/posts/{id}",
     *     tags={"Posts"},
     *     summary="Remove um post",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post removido com sucesso")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $post = $this->postRepository->find($id);

            if (!$post) {
                return $this->notFound('Post não encontrado');
            }

            if ($post->user_id !== Auth::id()) {
                return $this->unauthorized('Você não tem permissão para remover este post');
            }

            $this->postRepository->delete($id);
            
            return $this->success(null, 'Post removido com sucesso');
        } catch (\Exception $e) {
            return $this->error('Erro ao remover post', 500);
        }
    }
} 