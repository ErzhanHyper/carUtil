<?php


namespace App\Services;


use App\Models\Liner;

class LinerService
{
    public function update($request)
    {
        $user = app(AuthService::class)->auth();
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

                if ($request->title) {
                    $profile->fln = $request->title;
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
    }
}
