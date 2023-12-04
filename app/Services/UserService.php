<?php


namespace App\Services;


use App\Models\User;

class UserService
{
    public function update($request)
    {
        $user = app(AuthService::class)->auth();
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

                $manager->title = $request->title;


                if($request->custom_1){
                    $manager->custom_1 = $request->custom_1;
                }
                if($request->for_docs){
                    $manager->for_docs = $request->for_docs;
                }

                $manager->save();
            }
        }
    }
}
