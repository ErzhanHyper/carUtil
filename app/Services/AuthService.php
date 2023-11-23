<?php


namespace App\Services;


use App\Models\Liner;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AuthService
{
    public function auth()
    {
        $request = request();
        $token = $request->header('Authorization');
        $token = Str::substr($token, 7);
        $session_liner = Session::where('token', $token)->where('role', 'liner')->first();
        $session_user = Session::where('token', $token)->where('role', '<>', 'liner')->first();
        $user = null;

        if ($session_liner) {
            $liner = Liner::find($session_liner->uid);
            $liner->profile = json_decode($liner->profile);
            $liner->role = 'liner';
            $user = $liner;
        } else {
            $user = User::find($session_user->uid);
            if ($user && $user->role !== $session_user->role) {
                $session = Session::where('token', $token)->first();
                if ($session) {
                    $session->delete();
                }
            }
        }

        return $user;
    }


    public function validCurrentUser($pem)
    {
        $secure = app(EdsService::class)->secure($pem);

        if ($secure) {
            $idnum = $secure->iin ?? $secure->bin;
            $liner = Liner::where('idnum', $idnum)->first();
            if ($liner) {
                return true;
            }
        }
        return false;
    }

    public function secure($request)
    {
        $pem = $request->data['pem'];
        $secure = app(EdsService::class)->secure($pem);
        $role = '';

        if ($secure) {
            $idnum = $secure->iin ?? $secure->bin;
            $hash = Hash::make(Config::get('APP_SALT') . $idnum);
            $user = null;

            if ($request->auth_point === 'manager') {
                $user = User::where('login', $idnum)->first();
                if ($user) {
                    $role = $user->role;
                } else {
                    throw new InvalidArgumentException(json_encode(['title' => ['Пользователь не существует']]));
                }
            } else {
                $liner = Liner::where('idnum', $idnum)->first();
                if ($liner) {
                    $role = 'liner';
                    $user = $liner;
                }
            }

            if ($user) {
                $session = new Session();
                $session->token = $hash;
                $session->uid = $user->id;
                $session->role = $role;
                $session->save();

                return [
                    'role' => $role,
                    'hash' => $hash,
                ];

            } else {
//                throw new InvalidArgumentException(json_encode(['title' => ['Пользователь не существует']]));
                $hash = $this->newLiner($secure, $request);
                return [
                    'role' => $role,
                    'hash' => $hash,
                ];
            }

        }
    }

//
//    public function secureMobile($request)
//    {
//        $validator = Validator::make($request->all(), [
//            'login' => 'required',
//            'password' => 'required',
//        ]);
//
//        if ($validator->fails()) {
//            throw new InvalidArgumentException($validator->messages());
//        }
//
//        $idnum = $request->login;
//        $password = md5($request->password . 'KZ.UNIDADE.2016');
//        $hash = '';
//        $liner_find = Liner::where('idnum', $idnum)->first();
//        if ($liner_find) {
//            $liner = Liner::where('idnum', $idnum)->where('password', $password)->first();
//            if ($liner) {
//                $hash = Hash::make(Config::get('APP_SALT') . $idnum);
//                $role = 'liner';
//                $session = new Session();
//                $session->token = $hash;
//                $session->uid = $liner->id;
//                $session->role = $role;
//                $session->save();
//            } else {
//                throw new InvalidArgumentException(json_encode(['title' => ['Неверные данные для входа']]));
//            }
//        } else {
//            throw new InvalidArgumentException(json_encode(['title' => ['Пользователь не найден']]));
//        }
//
//        return ['hash' => $hash];
//    }
    public function secureMobile($request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->messages());
        }

        $idnum = $request->login;
        $password = md5($request->password . 'KZ.UNIDADE.2016');
        $hash = '';
        $user_find = User::where('login', $idnum)->where('role', 'operator')->first();
        if ($user_find) {
            $user = User::where('login', $idnum)->where('password', $password)->first();
            if ($user) {
                $hash = Hash::make(Config::get('APP_SALT') . $idnum);
                $role = 'operator';
                $session = new Session();
                $session->token = $hash;
                $session->uid = $user->id;
                $session->role = $role;
                $session->save();
            } else {
                throw new InvalidArgumentException(json_encode(['title' => ['Неверные данные для входа']]));
            }
        } else {
            throw new InvalidArgumentException(json_encode(['title' => ['Пользователь не найден']]));
        }

        return ['hash' => $hash];
    }

    private function newLiner($secure, $request)
    {

        $dte = Carbon::parse(str_replace(' ALMT', '', $secure->dte))->format('Y-m-d H:i');
        $liner = new Liner;
        $liner->idnum = $secure->iin ?? $secure->bin;
        $liner->active = 0;
        $liner->opt = 0;
        $liner->lts = strtotime($dte);
        $liner->profile = json_encode([
            'fln' => $secure->ln . ' ' . $secure->fn . ' ' . $secure->gn,
            'email' => $secure->email,
            'ftype' => $secure->bin != '' ? 'ut' : 'ft'
        ]);
        if ($liner->save()) {
            $idnum = $secure->iin ?? $secure->bin;
            $hash = Hash::make(Config::get('APP_SALT') . $idnum);

            $session = new Session();
            $session->token = $hash;
            $session->uid = $liner->id;
            $session->role = 'liner';
            $session->save();

            return $hash;
        }

    }

}
