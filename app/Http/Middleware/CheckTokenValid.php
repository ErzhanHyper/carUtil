<?php

namespace App\Http\Middleware;

use App\Models\Liner;
use App\Models\Session;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenValid
{

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $token = Str::substr($token, 7);

        $session_liner = Session::where('token', $token)->where('role', 'liner')->first();
        $session_user = Session::where('token', $token)->where('role', '<>', 'liner')->first();

        if($session_liner) {
            $liner = Liner::find($session_liner->uid);
            $idnum = $liner->idnum;
        }else{
            $user = User::find($session_user->uid);
            $idnum = $user->login;
        }

        $response = response()->json([
            'message' => 'Unauthenticated',
        ], 401);

        if ($token && $idnum) {
            $hash = Config::get('APP_SALT') . $idnum;
            if (Hash::check($hash, $token)) {
                $response = $next($request);
            }
        }

        return $response;

    }

}
