<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function get(Request $request)
    {
        $user = User::with(['factory', 'region', 'manufacture']);

        if($request->idnum){
            $user->where('login', 'like', '%'.$request->idnum. '%');
        }

        if($request->title){
            $user->where('title', 'like', '%'.$request->title.'%');
        }

        if($request->role){
            $user->where('role', $request->role);
        }
        return UserResource::collection($user->paginate(15))->response();
    }

    public function getById($id)
    {
        $user = User::where('id', $id)->with(['factory', 'region', 'manufacture'])->first();
        return response()->json(new UserResource($user));
    }

    public function store(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(),
            [
                'login' => 'required',
                'title' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'role' => 'required',
                'for_docs' => 'required',
            ],
        );

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $login = $request->login;

        if ($login) {
            $userFind = User::where('login', $login)->first();
        }

        if (!$userFind) {
            $user = new User;
            $user->login = $login;
            $user->title = $request->title;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->role = $request->role;
            $user->bin = $request->bin;
            $user->base = $request->base;
            $user->for_docs = $request->for_docs;
            $user->custom_1 = $request->custom_1;
            $user->custom_2 = $request->custom_2;
            $user->custom_3 = $request->custom_3;
            $user->custom_4 = $request->custom_4;
            $user->factory_id = $request->factory;

            if ($request->active && $request->active === 'false') {
                $user->password = 'disabled';
            }
            $user->save();

            $message = 'Пользователь успешно создан';
            $success = true;
            $status = 200;
        } else {
            $message = 'Пользователь уже существует';
            $success = false;
            $status = 400;
        }

        return response()->json([
            'message' => $message,
            'success' => $success
        ], $status);

    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(),
            [
                'login' => 'required',
                'title' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'role' => 'required',
                'for_docs' => 'required',
            ],
        );

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $login = $request->login;

        $user = User::find($id);
        if($user) {
            $user->login = $login;
            $user->title = $request->title;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->role = $request->role;
            $user->bin = $request->bin;
            $user->base = $request->base;
            $user->for_docs = $request->for_docs;
            $user->custom_1 = $request->custom_1;
            $user->custom_2 = $request->custom_2;
            $user->custom_3 = $request->custom_3;
            $user->custom_4 = $request->custom_4;
            $user->factory_id = $request->factory;

            if ($request->active && $request->active === 'false') {
                $user->password = 'disabled';
            }
            $user->save();
        }

        return response()->json([
            'message' => 'Успешно обновлено',
            'success' => true
        ], 200);

    }

    public function role()
    {
        $role = [
            [
                'title' => 'Админ',
                'name' => 'admin'
            ],
            [
                'title' => 'Модератор',
                'name' => 'moderator'
            ],
            [
                'title' => 'Бухгалтер',
                'name' => 'accountant'
            ],
            [
                'title' => 'Руководитель дилерского центра (для отчетов)',
                'name' => 'dealer-chief'
            ],
            [
                'title' => 'Дилер (для погашений)',
                'name' => 'dealer-light'
            ],
            [
                'title' => 'Региональный менеджер',
                'name' => 'operator'
            ],
            [
                'title' => 'Руководитель регионального менеджера (для отчетов)',
                'name' => 'operator-chief'
            ],
        ];
        return response()->json($role);
    }
}
