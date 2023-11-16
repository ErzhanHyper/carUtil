<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'client_type_id' => $this->client_type_id,
            'title' => $this->title,
            'idnum' => $this->idnum,
            'bank_id' => $this->bank_id,
            'iban' => $this->iban,
            'iban_alt' => $this->iban_alt,
            'region_id' => $this->region_id,
            'region' => $this->region,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'user_id' => $this->user_id,
            'blocked' => $this->bocked,
            'ud_num' => $this->ud_num,
            'ud_expired' => $this->ud_expired,
            'ud_issued_id' => $this->ud_issued_id,
            'proxy' => $this->proxy,
            'proxy_type' => $this->proxy_type,
            '__meta' => $this->__meta,
            'year' => $this->year,
        ];
    }
}
