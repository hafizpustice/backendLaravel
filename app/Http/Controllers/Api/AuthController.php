<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Validator\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        auth()->setDefaultDriver('api');
        $this->apiResponse = $apiResponse;
    }
    protected $apiResponse;
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return $this->apiResponse->responseApiWithError('Unauthorized access', $credentials, 401);
        }
        $data = [];
        $data['userDetails'] = auth()->user();
        $data['access_token'] = $token;
        $data['token_type'] = 'bearer';
        $data['expires_in'] = auth('api')->factory()->getTTL() * 60;

        return $this->apiResponse->responseApiWithSuccess('Login successfully', $data);
    }

    public function logout()
    {
        auth()->logout();
        return $this->apiResponse->responseApiWithSuccess('Successfully logged out', []);
    }

    public function refresh()
    {
        $data = [];
        $data['userDetails'] = auth()->user();
        $data['access_token'] = auth()->refresh();
        $data['token_type'] = 'bearer';
        $data['expires_in'] = auth('api')->factory()->getTTL() * 60;

        return $this->apiResponse->responseApiWithSuccess('login user token refresh successfully', $data);

    }
}
