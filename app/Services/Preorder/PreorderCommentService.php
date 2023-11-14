<?php


namespace App\Services\Preorder;


use App\Models\PreorderComment;
use App\Services\AuthService;

class PreorderCommentService
{

    public function run($request, $id)
    {

        $auth = app(AuthService::class)->auth();
        $user_id = null;
        $liner_id = null;
        if($auth->role === 'liner'){
            $liner_id = $auth->id;
        }
        if($auth->role === 'moderator'){
            $user_id = $auth->id;
        }
        $comment = new PreorderComment;
        $comment->preorder_id = $id;
        $comment->comment = $request->comment;
        $comment->action = $request->status;
        $comment->created_at = time();
        $comment->user_id = $user_id;
        $comment->liner_id = $liner_id;
        $comment->save();
    }
}
