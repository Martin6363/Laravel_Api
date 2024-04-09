<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Http\Resources\PostResource;
use App\Models\Image;
use App\Models\Post;
use Dotenv\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request) {

        try{
            $query = Post::query();
            $perPage = 5;
            $page = $request->input('page', 1);
            $search = $request->input('search');
            
            if ($search) {
                $query->whereRaw("title LIKE ?", ['%' . $search . '%']);
            }
            
            $total = $query->count();

            $result = $query->offset(($page - 1) * $perPage)->limit($perPage)
            ->get();

            return response()->json([
                'code' => 200,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'data' => PostResource::collection($result)
            ], 200);


        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreatePostRequest $request) {
        $validData = $request->validated();
        $imageData = $request->file('image');
        $user = Auth::user();

        try{           
            if($user) {
                $post = new Post();
                $post->title = $validData['title'];
                $post->description = $validData['description'];
                $post->user_id = Auth::user()->id;
                $post->published = $validData['published'];
                $post->save();;
        

                if ($imageData && $imageData->isValid()) {
                    $imageName = time() . '_' . $imageData->getClientOriginalName();
                    Storage::disk('public')->put('images/' . $imageName, file_get_contents($imageData));

                    $imageUrl = str_replace('localhost', '127.0.0.1:8000', Storage::disk('public')->url('images/' . $imageName));
                    
                    $image = new Image();
                    $image->image = $imageUrl;
                    $image->post_id = $post->id;
                    $image->user_id = $user->id;
                    $image->save();
                }

                return response()->json([
                    'code' => 201,
                    'message' => 'Post created successfully',
                    'data' => new PostResource($post)
                ], 201);
            } else {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'error' => 'User is not authenticated'
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Post $post) {
        try {
            $postWithUser = Post::with('user')->find($post->id)->first();
            if (!$postWithUser) {
                return response()->json([
                    'code' => 404,
                    'message' => 'No content',
                ], 404);
            } 

            return response()->json([
                'code' => 200,
                'data' => new PostResource($postWithUser)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(EditPostRequest $request, Post $post) {   
        try {
            if (Auth::id() !== $post->user_id) {
                return response()->json([
                    'code' => 403,
                    'message' => 'You are not authorized to update this post.',
                ], 403);
            }

            $post->title = $request->title;
            $post->description = $request->description;
    
            $post->update();

            return response()->json([
                'code' => 201,
                'message' => 'Post updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);;
        }
    }

    public function upload() {
        //
    }

    public function destroy(Post $post) {
        try{
            if (Auth::user()->id !== $post->user_id) {
                return response()->json([
                    'code' => 403,
                    'message' => 'You are not authorized to delete this post.',
                ], 403);
            }

            $post->delete();

            return response()->json([
                'code' => 204,
                'message' => 'Post deleted successfully',
            ], 204);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
