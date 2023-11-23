<?php

namespace App\Http\Resources;

use App\Models\Manufacture;
use App\Models\RefFactory;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Resources\Json\JsonResource;

class SellResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = app(AuthService::class)->auth();
        $status_title = match ($this->approve) {
            0 => 'Новая',
            1 => 'На рассмотрении',
            2 => 'Одобрена',
            3 => 'Отклонена',
            4 => 'Ждет гашения',
            5 => 'Погашено',
            default => '',
        };

        $status = [
            'title' => $status_title,
            'id' => $this->approve
        ];
        $manufacture = [];
        $canEdit = false;
        $canSend = false;
        $canApprove = false;
        $canDecline = false;
        $canSell = false;
        $canSendToSell = false;

        $dealer = User::find($this->user_id);
        if($dealer) {
            $manufacture = Manufacture::find($dealer->custom_2);
        }
        if($user->role === 'dealer-light' || $user->role === 'dealer-chief'){
            if($this->approve === 0) {
                $canSend = true;
                $canEdit = true;
            }
            if($this->approve === 2){
                $canSendToSell = true;
            }
        }else if($user->role === 'moderator'){
            if($this->approve === 1 || $this->approve === 2 || $this->approve === 4){
                $canDecline = true;
            }
            if($this->approve === 1){
                $canApprove = true;
            }
            if($this->approve === 4){
                $canSell = true;
            }
        }

        $ref_factory = RefFactory::find($this->subject);

        return [
            'id' => $this->id,
            'created_dt' => date('d.m.Y H:i', $this->created),
            'approved_dt' => date('d.m.Y H:i',$this->approved),
            'sended_dt' => date('d.m.Y H:i',$this->sended_to_approve),
            'closed_dt' => date('d.m.Y H:i',$this->closed),
            'user_id' => $this->user_id,
            'subject' => $this->subject,
            'vehicle' => $ref_factory,
            'vin' => $this->vin,
            'year' => $this->year,
            'sum' => number_format($this->sum, 0, ".", " "),
            'phone' => $this->phone,
            '__meta' => $this->__meta,

            'cert_1' => $this->cert_1 ? str_pad($this->cert_1, 9, 0, STR_PAD_LEFT) : 'нет',
            'cert_2' => $this->cert_2 ? str_pad($this->cert_2, 9, 0, STR_PAD_LEFT) : 'нет',
            'cert_3' => $this->cert_3 ? str_pad($this->cert_3, 9, 0, STR_PAD_LEFT) : 'нет',
            'cert_4' => $this->cert_4 ? str_pad($this->cert_4, 9, 0, STR_PAD_LEFT) : 'нет',
            'manufacture' => $manufacture,
            'status' => $status,
            'canEdit' => $canEdit,
            'canSend' => $canSend,
            'canApprove' => $canApprove,
            'canSell' => $canSell,
            'canDecline' => $canDecline,
            'canSendToSell' => $canSendToSell
        ];
    }
}
