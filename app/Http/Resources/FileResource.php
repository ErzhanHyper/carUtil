<?php

namespace App\Http\Resources;

use App\Models\FileType;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $file_type = FileType::find($this->file_type_id);

        return [
            'id' => $this->id,
            'car_id' => $this->car_id,
            'order_id' => $this->order_id,
            'client_id' => $this->client_id,
            'file_type_id' => $this->file_type_id,
            'file_type' => $file_type,
            'original_name' => $this->original_name,
            'ext' => $this->ext,
            'created_at' => date('d.m.Y H:i', $this->created_at),
            'hash' => $this->hash,
            'operator_sign' => $this->operator_sign,
            'client_sign' => $this->client_sign,
        ];
    }
}
