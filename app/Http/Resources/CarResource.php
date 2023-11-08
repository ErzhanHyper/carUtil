<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'vin' => $this->vin,
            'grnz' => $this->grnz,
            'body_no' => $this->body_no,
            'chassis_no' => $this->chassis_no,
            'year' => $this->year,
            'category' => $this->category,
            'category_id' => $this->category_id,
            'car_type_id' => $this->car_type_id,
            'order_id ' => $this->order_id ,
            'proxy' => $this->proxy,
            'owner_title' => $this->owner_title,
            'owner_iban' => $this->owner_iban,
            'proxy_num' => $this->proxy_num,
            'proxy_date' => $this->proxy_date,
            'booking' => $this->booking,
            'order_type' => $this->order_type,
            'cert_title' => $this->cert_title,
            'cert_idnum' => $this->cert_idnum,
            'owner_type' => $this->owner_type,
            'weight' => $this->weight,
            'color' => $this->color,
            'doors_count' => $this->doors_count,
            'wheels_count' => $this->wheels_count,
            'wheels_protector_count' => $this->wheels_protector_count,
            'hash' => $this->hash,
            'operator_sign' => $this->operator_sign,
            'operator_sign_time' => $this->operator_sign_time,
            'k_status' => $this->k_status,
            'k_data' => $this->k_data,
            '__meta' => $this->__meta,
            'moderator_accept_sign' => $this->moderator_accept_sign,
            'moderator_decline_sign' => $this->moderator_decline_sign,
            'use_client_eds' => $this->use_client_eds,
            'contract_ids' => $this->contract_ids,
            'm_model' => $this->m_model,
            'owner_idnum	' => $this->owner_idnum,
            'certificate' => $this->certificate,
        ];
    }
}
