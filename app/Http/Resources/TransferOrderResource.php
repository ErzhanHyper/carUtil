<?php

namespace App\Http\Resources;

use App\Models\Client;
use App\Models\TransferDeal;
use App\Models\TransferOrder;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        $user = app(AuthService::class)->auth();
        $transferDeal = TransferDeal::where('transfer_order_id', $this->id)->where('liner_id', $user->id)->first();
        if ($transferDeal) {
            $transferDeal->signed = false;
        }
        $isOwner = false;

        $deals = [];
        $signAccess = false;

        if ($this->owner_sign != '' && $this->owner_sign_time != '') {
            $signAccess = true;
        }

        if ($user->id === $this->owner_liner_id) {
            $isOwner = true;
            $deals = TransferDealResource::collection(TransferDeal::where('transfer_order_id', $this->id)->get());
        }

        if ($transferDeal) {
            if ($this->recipient_liner_id === $user->id) {
                if ($this->recipient_sign != '') {
                    $transferDeal->signed = true;
                }
            }
        }

        $client = Client::where('idnum', $user->idnum)->first();

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order' => new OrderResource($this->order),
            'owner_liner_id' => $this->owner_liner_id,
            'recipient_liner_id' => $this->recipient_liner_id,
            'closed' => $this->closed,
            'date' => Carbon::parse($this->date)->format('Y-m-d H:i'),
            'transfer_deal_id' => $this->transfer_deal_id,
            'owner_sign' => $this->owner_sign,
            'owner_sign_time' => $this->owner_sign_time,
            'recipient_sign' => $this->recipient_sign,
            'recipient_sign_time' => $this->recipient_sign_time,
            'isOwner' => $isOwner,
            'dealExist' => (bool)$transferDeal,
            'deal' => $transferDeal,
            'signAccess' => $signAccess,
            'client' => $client
        ];

    }
}
