<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderHistoryResource extends JsonResource
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
            'SIGN_ACTION' => 'Подписано',
            'CREATED_ORDER' => 'Заявка создана',
            'SENDED_TO_MODERATOR' => 'Отправлено модератору',
            'RETURNED_TO_OPERATOR' => 'Возвращена на доработку',
            'COMMENT' => 'Комментарий',
            default => '',
        };

        return [
            'id' => $this->id,
            'action' => $this->action,
            'action_title' => $action_title,
            'order_id' => $this->order_id,
            'comment' => $this->comment,
            'user_id' => $this->user_id,
            'created_at' => date('d.m.Y H:i', $this->created_at)
        ];
    }
}
