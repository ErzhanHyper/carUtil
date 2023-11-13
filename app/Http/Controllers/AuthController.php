<?php

namespace App\Http\Controllers;

use App\Models\Liner;
use App\Models\Session;
use App\Models\User;
use App\Services\AuthService;
use App\Services\EdsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $data = app(AuthService::class)->secure($request);
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
            $data = app(AuthService::class)->secureMobile($request);
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
        $idnum = app(AuthService::class)->auth();

        return response()->json($idnum);
    }

    public function validUser(Request $request)
    {
        try {
            $data = app(AuthService::class)->validCurrentUser($request);
            $result = ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
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


    public function update(Request $request)
    {
        $user = app(AuthService::class)->auth();

        if($user->role === 'liner') {
            $liner = Liner::find($request->id);
            if ($liner) {
                if ($liner->id === $user->id) {
                    $profile = json_decode($liner->profile);

                    if ($request->phone) {
                        $profile->phone = $request->phone;
                    }

                    if ($request->email) {
                        $profile->email = $request->email;
                    }

                    if ($request->password && $request->password_confirm) {
                        if ($request->password === $request->password_confirm) {
                            $liner->password = md5($request->password . 'KZ.UNIDADE.2016');
                        }
                    }

                    $profile = json_encode($profile);
                    $liner->profile = $profile;

                }
                $liner->save();
            }
        }else{
            $manager = User::find($request->id);
            if($manager){
                if($manager->id === $user->id){
                    if ($request->password && $request->password_confirm) {
                        if ($request->password === $request->password_confirm) {
                            $manager->password = md5($request->password . 'KZ.UNIDADE.2016');
                        }
                    }
                    $manager->email = $request->email;
                    $manager->phone = $request->phone;
                    $manager->save();
                }
            }
        }

    }


    public function checkEds(Request $request) {
        $check = app(EdsService::class)->check($request);

        return response()->json($check);
    }
}
