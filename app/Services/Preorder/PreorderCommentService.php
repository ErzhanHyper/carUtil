<?php


namespace App\Services\Preorder;


use App\Models\PreorderComment;

class PreorderCommentService
{

    public function run($request, $id)
    {
        $comment = new PreorderComment;
        $comment->preorder_id = $id;
        $comment->text = $request->comment;
        $comment->action = $request->status;
        $comment->created_at = time();
        $comment->save();
    }
}
