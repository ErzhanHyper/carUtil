<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'preorder_id' => $this->preorder_id,
            'file_type_id' => $this->file_type_id,
            'original_name' => $this->original_name,
            'client_id' => $this->client_id,
            'extension' => $this->extension,
            'created_at' => date('d.m.Y H:i', $this->created_at),
        ];
    }
}
