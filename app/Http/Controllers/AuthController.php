<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Services\AuthenticationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $data = app(AuthenticationService::class)->secure($request);
            $result = ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);

    }

    public function loginMobile(Request $request)
    {
        try {
            $data = app(AuthenticationService::class)->secureMobile($request);
            $result = ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);

    }

    public function get()
    {
        $idnum = app(AuthenticationService::class)->auth();

        return response()->json($idnum);
    }


    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        $token = Str::substr($token, 7);
        $session = Session::where('token', $token)->first();

        if ($session) {
            $session->delete();
        }

        return response()->json('logout');
    }
}
