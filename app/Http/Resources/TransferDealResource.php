<?php

namespace App\Http\Resources;

use App\Models\Client;
use App\Models\Liner;
use App\Models\TransferOrder;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferDealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        $auth = app(AuthService::class)->auth();
        $authClient = Client::where('idnum', $auth->idnum)->first();

        $client = Client::find($this->client_id);
        $client->region;

        $transferOrder = TransferOrder::where('id', $this->transfer_order_id)->where('transfer_deal_id', $this->id)->first();

        $canSign = false;
        $canClose = false;
        $selected = false;
        $ownerSigned = false;
        $receiverSigned = false;

        if($transferOrder->owner_sign != ''){
            $ownerSigned = true;
        }

        if($transferOrder->receiver_sign != ''){
            $receiverSigned = true;
        }

        if ($transferOrder) {
            $selected = true;

            if ($transferOrder->client_id === $authClient->id) {
                if($transferOrder->closed !== 2){
                    $canClose = true;
                }
                if ($transferOrder->owner_sign == '') {
                    $canSign = true;
                }
            }

            if ($this->client_id === $authClient->id) {
                if ($transferOrder->receiver_sign != '') {
                    $canSign = true;
                }
            }
        }

        return [
            'client' => $client,
            'id' => $this->id,
            'transfer_order_id' => $this->transfer_order_id,
            'amount' => $this->amount,
            'date' => Carbon::parse($this->date)->format('Y-m-d H:i'),
            'selected' => $selected,
            'canSign' => $canSign,
            'canClose' => $canClose,
            'ownerSigned' => $ownerSigned,
            'receiverSigned' => $receiverSigned
        ];
    }
}
