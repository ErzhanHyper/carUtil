<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i'),
            'text' => $this->text,
            'action' => $this->action,
            'preorder_id' => $this->preorder_id,
            'user_id' => $this->user_id
        ];
    }
}
