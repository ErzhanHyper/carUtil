<?php

namespace App\Http\Resources;

use App\Models\Client;
use App\Models\Order;
use App\Models\PreOrderCar;
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

        $status = match ($this->closed) {
            0 => 'Новая',
            1 => 'Выбрана',
            2 => 'Завершена',
            default => '',
        };

        $auth = app(AuthService::class)->auth();
        $client = Client::find($this->client_id);
        $currentClient = Client::where('idnum', $auth->idnum)->first();
        $preorder = PreOrderCar::where('order_id', $this->order_id)->select(['recycle_type', 'order_id'])->first();

        $deal = [];
        if($currentClient) {
            $deal = TransferDeal::where('client_id', $currentClient->id)->where('transfer_order_id', $this->id)->first();
        }

        $transferDealAccept = TransferDeal::where('transfer_order_id', $this->id)->where('id', $this->transfer_deal_id)->first();

        $isOwner = false;
        $canSign = false;
        $canDeal = true;
        $canAccept = true;
        $blocked = false;

        if($transferDealAccept){
            $canAccept = false;
        }

        if($deal){
            if($this->closed === 1) {
                if($this->owner_sign != '' && $this->hash != ''){
                    $canSign = true;
                }
            }

            $canDeal = false;
            $blocked = true;
        }

        if($auth->idnum === $client->idnum){
            $isOwner = true;
        }

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order' => new OrderResource(Order::find($this->order_id)),
            'client_id' => $this->client_id,
            'closed' => $this->closed,
            'date' => Carbon::parse($this->date)->format('Y-m-d H:i'),
            'transfer_deal_id' => $this->transfer_deal_id,
            'owner_sign' => $this->owner_sign,
            'owner_sign_time' => $this->owner_sign_time,
            'receiver_sign' => $this->receiver_sign,
            'receiver_sign_time' => $this->receiver_sign_time,
            'deal' => $deal,
            'client' => $client,
            'currentClient' => $currentClient,
            'recycle_type' => $preorder->recycle_type,
            'blocked' => $blocked,
            'isOwner' => $isOwner,
            'canSign' => $canSign,
            'canDeal' => $canDeal,
            'canAccept' => $canAccept,
            'status' => [
                'id' => $this->closed,
                'title' => $status
            ]
        ];

    }
}
