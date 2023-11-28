<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PreOrderHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $action_title = match ($this->action) {
            'APPROVED' => 'Одобрено',
            'DECLINED' => 'Отклонено',
            'SEND_TO_MODERATOR' => 'Отправлено на рассмотрение',
            'RETURNED_BACK_LINER' => 'Возвращена на доработку',
            'COMMENT' => 'Комментарий',
            default => '',
        };

        return [
            'id' => $this->id,
            'action' => $this->action,
            'action_title' => $action_title,
            'preorder_id' => $this->preorder_id,
            'comment' => $this->comment,
            'user_id' => $this->user_id,
            'created_at' => date('d.m.Y H:i', $this->created_at)
        ];
    }
}
