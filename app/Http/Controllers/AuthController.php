<?php

namespace App\Http\Controllers;

use App\Modules\User\Services\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;    
    }

    /**
     * Handle user login and return a token.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $response = $this->userService->handleUserLogin($credentials);

        return response()->json($response, $response['success'] ? 200 : 401);
    }

    /**
     * Handle user logout and revoke the token.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $response = $this->userService->handleUserLogout($request);

        return response()->json($response);
    }
}
