<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserShowResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request) {

        try {
            $perPage = 2;
            $limit = $request->input('limit', 1);
            $search = $request->input('search');
            
            $usersQuery = User::query()->with(['posts' => function ($query) {
                $query->where('published', true);
            }]);

            if ($search) {
                $usersQuery->whereRaw("name LIKE ?", ['%' . $search . '%']);
            }

            $totalValue = $usersQuery->count();

            $result = $usersQuery->offset(($limit - 1) * $perPage)->limit($perPage)->get();

            if (!$result) {
                return response()->json([
                    'code' => 404,
                    'message' => "No Results"
                ], 404);
            }

            return response()->json([
                'code' => 200,
                'current_limit' => $limit,
                'last_limit' => ceil($totalValue / $perPage),
                'data' => UserResource::collection($result)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store() {
        //
    }


    public function show($id)
    {
        try {
            $userWithPosts = User::with(['posts' => function ($query) {
                $query->where('published', true);
            }])->find($id);

            if (!$userWithPosts) {
                return response()->json([
                    'code' => 404,
                    'message' => "User not found"
                ], 404);
            }

            return response()->json([
                'code' => 200,
                'data' =>new UserShowResource($userWithPosts),
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => 'User not found',
            ], 404);
        }

        try {
            if (Auth::check()) {
                if (Auth::id() !== $user->id) {
                    return response()->json([
                        'code' => 403,
                        'message' => 'You are not authorized to update this post.',
                    ], 403);
                }

                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                
                $user->update();

                return response()->json([
                    'code' => 200,
                    'message' => 'User updated successfully'
                ], 200);
            } 
            else {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized',
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

    public function destroy($id)
    {
        //
    }

    public function upload() {
        //
    }
}
