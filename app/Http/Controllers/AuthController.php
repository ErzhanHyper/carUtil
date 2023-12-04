<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Services\AuthService;
use App\Services\EdsService;
use App\Services\LinerService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(AuthService::class)->secure($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function loginMobile(Request $request): JsonResponse
    {
        try {
            $data = app(AuthService::class)->secureMobile($request);
            $result['status'] = 200;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function get()
    {
        return response()->json(app(AuthService::class)->auth());
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->header('Authorization');
        $token = Str::substr($token, 7);
        $session = Session::where('token', $token)->first();
        if ($session) {
            $session->delete();
        }
        return response()->json('logout');
    }

    public function update(Request $request)
    {
        $user = app(AuthService::class)->auth();
        if ($user->role === 'liner') {
            return app(LinerService::class)->update($request);
        } else {
            return app(UserService::class)->update($request);
        }
    }

    public function checkEds(Request $request): JsonResponse
    {
        return response()->json(app(EdsService::class)->check($request));
    }
}
