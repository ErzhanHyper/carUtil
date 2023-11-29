<?php

namespace App\Http\Resources;

use App\Models\Car;
use App\Models\Client;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\TransferDeal;
use App\Services\AuthService;
use Carbon\Carbon;
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
        $preorder = PreOrderCar::where('order_id', $this->order_id)->first();

        $car = Car::where('order_id', $this->order_id)->first();

        $vehicleType = '';

        if($car) {
            if ($car->car_type_id === 1 || $car->car_type_id === 2) {
                $vehicleType = 'car';
            } else {
                $vehicleType = 'agro';
            }
        }

        $deal = null;
        $amount = 0;
        if($currentClient) {
            $deal = TransferDeal::where('liner_id', $auth->id)->where('transfer_order_id', $this->id)->first();
            if($deal) {
                $currentClient = Client::find($deal->client_id);
            }
        }
        $transferDealAccept = TransferDeal::where('transfer_order_id', $this->id)->where('id', $this->transfer_deal_id)->first();

        if($auth->id === $this->liner_id) {
            if ($transferDealAccept) {
                $amount = $transferDealAccept->amount;
            }
        }else{
            $transferDeal = TransferDeal::where('transfer_order_id', $this->id)->where('liner_id', $auth->id)->first();
            if($transferDeal) {
                $amount = $transferDeal->amount;
            }
        }

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

        if($auth->id === $this->liner_id){
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
            'blocked' => $blocked,
            'isOwner' => $isOwner,
            'canSign' => $canSign,
            'canDeal' => $canDeal,
            'canAccept' => $canAccept,
            'vehicleType' => $vehicleType,
            'preorder_id' => $preorder->id,
            'amount' => $amount,
            'status' => [
                'id' => $this->closed,
                'title' => $status
            ]
        ];

    }
}
