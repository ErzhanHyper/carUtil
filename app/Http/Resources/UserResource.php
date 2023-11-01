<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'login' => $this->login,
            'phone' => $this->phone,
            'email' => $this->email,
            'title' => $this->title,
            'role' => $this->role,
            'bin' => $this->bin,
            'base' => $this->base,
            'for_docs' => $this->for_docs,
            'custom_1' => $this->custom_1,
            'custom_2' => $this->custom_2,
            'custom_3' => $this->custom_3,
            'custom_4' => $this->custom_4,
            'gp_id' => $this->gp_id,
            'factory' => $this->factory,
            'manufactory' => $this->manufactory,
            'region' => $this->region,
            'active' => ($this->password === 'disabled')
        ];
    }
}
