<?php


namespace App\Services;


use App\Models\Liner;
use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AuthenticationService
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

    private function checkEIS($pem)
    {
        $token = 'bb885996c794e568181a2c919d4a140fd90b577c';
        header('Content-Type: application/json');
        $ch = curl_init('192.168.0.93/api/eis');
        $post = json_encode(["pem" => $pem]);
        $authorization = "Authorization: Bearer " . $token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);

//        $b64 = base64_encode($request->pem);
//        $kalkan = new KalkanCrypt;
//        $kalkan->secure($b64);

    }

    public function secure($request)
    {
//        if ($this->checkEIS($request->data['pem'])) {
//            $idnum = $this->checkEIS($request->data['pem'])->iin;
            $idnum = '960213350271';
            $hash = Hash::make(Config::get('APP_SALT') . $idnum);
            $user = null;

            if ($request->auth_point === 'manager') {
                $user = User::where('login', $idnum)->first();
                if ($user) {
                    $role = $user->role;
                } else {
                    throw new InvalidArgumentException(json_encode(['Пользователь не существует']));
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
            } else {
                throw new InvalidArgumentException(json_encode(['Пользователь не существует']));
            }

//        }

        return [
            'hash' => $hash,
        ];
    }


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
        $liner_find = Liner::where('idnum', $idnum)->first();
        if ($liner_find) {
            $liner = Liner::where('idnum', $idnum)->where('password', $password)->first();
            if($liner) {
                $hash = Hash::make(Config::get('APP_SALT') . $idnum);
                $role = 'liner';
                $session = new Session();
                $session->token = $hash;
                $session->uid = $liner->id;
                $session->role = $role;
                $session->save();
            }else{
                throw new InvalidArgumentException(json_encode(['title' => ['Неверные данные для входа']]));
            }
        } else {
            throw new InvalidArgumentException(json_encode(['title' => ['Пользователь не найден']]));
        }

        return ['hash' => $hash];
    }

}
