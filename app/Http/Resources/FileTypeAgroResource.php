<?php

namespace App\Http\Resources;

use App\Models\FileTypeAgro;
use Illuminate\Http\Resources\Json\JsonResource;

class FileTypeAgroResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $file_type = FileTypeAgro::find($this->file_type_id);

        return [
            'id' => $this->id,
            'preorder_id' => $this->preorder_id,
            'file_type_id' => $this->file_type_id,
            'file_type' => $file_type,
            'original_name' => $this->original_name,
            'client_id' => $this->client_id,
            'extension' => $this->extension,
            'created_at' => date('d.m.Y H:i', $this->created_at),
        ];
    }
}
