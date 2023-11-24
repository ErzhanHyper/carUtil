<?php

namespace App\Http\Resources;

use App\Services\AuthService;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $status = match ($this->approve) {
            0 => 'Новая',
            1 => 'На рассмотрении',
            2 => 'Одобрена',
            3 => 'Отклонена',
            default => '',
        };

        $auth = app(AuthService::class)->auth();
        $cert = $this->certificate;

        $blocked = true;
        $canSign = false;
        $canDelete = false;
        if ($cert->idnum_1 === $auth->idnum) {

            if ($this->approve === 0) {
                $canDelete = true;
            }

            if ($this->approve === 0) {
                if ($this->owner_sign == '' && $this->owner_sign_time == '') {
                    $blocked = false;
                    $canSign = true;
                }
            }
        } else {
            if ($this->idnum === $auth->idnum) {
                if ($this->approve === 0) {
                    if ($this->receiver_sign == '' && $this->receiver_sign_time == '') {
                        $canSign = true;
                    }
                }
            }
        }


        $info = 'На переоформлении';
        if ($this->approve == 1) {
            $info = 'На рассмотрении у модератора';
        }

        return [
            'id' => $this->id,
            'created' => date('Y-m-d H:i', $this->created),
            'user_id' => $this->user_id,
            'certificate_id' => $this->certificate_id,
            'certificate' => $cert,
            'title' => $this->title,
            'idnum' => $this->idnum,
            'phone' => $this->phone,
            'approve' => $this->approve,
            'approved' => date('Y-m-d H:i',$this->approved),
            'sended_to_approve' => date('Y-m-d H:i', $this->sended_to_approve),
            '__meta' => $this->__meta,
            'owner_sign' => $this->owner_sign,
            'owner_sign_time' => $this->owner_sign_time,
            'receiver_sign' => $this->receiver_sign,
            'receiver_sign_time' => $this->receiver_sign_time,
            'hash' => $this->hash,
            'page' => $this->page,
            'cert_owner_address' => $this->cert_owner_address,
            'blocked' => $blocked,
            'canSign' => $canSign,
            'canDelete' => $canDelete,
            'status' => [
                'id' => $this->approve,
                'title' => $status
            ],
            'info' => $info
        ];
    }
}
