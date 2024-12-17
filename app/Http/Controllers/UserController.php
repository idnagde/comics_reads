<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $readerRole = Role::where('name', 'reader')->first();
        $user->roles()->attach($readerRole);

        return response()->json([
            'message' => 'User registered Successfully.',
            'data' => new UserResource($user)
        ], 201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Login failed.',
                'errors' => [
                    'credential' => 'Invalid credentials'
                ]
            ], 401);
        }

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'user' => new UserResource($user),
                'accessToken' => $token
            ]
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $per_page = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        $users = User::when($search, function ($query, $search) {
            $query
                ->where('name', 'like', "%{$search}")
                ->orWhere('email', 'like', "%{$search}");
        })
            ->orderBy('id', 'desc')
            ->paginate($per_page, ['*'], 'page', $page);

        $result = UserResource::collection($users)->response()->getData();

        return response()->json([
            'message' => 'Users fetched successfully.',
            'data' => $result->data,
            'links' => $result->links,
            'meta' => $result->meta,
        ]);
    }
}
