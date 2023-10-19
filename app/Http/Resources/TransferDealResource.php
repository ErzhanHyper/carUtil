<?php

namespace App\Http\Resources;

use App\Models\Liner;
use App\Models\TransferOrder;
use App\Services\AuthenticationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $user = app(AuthenticationService::class)->auth();

        $liner = Liner::find($this->liner_id);

        $showAccept = true;
        $signed = false;

        $transferOrder = TransferOrder::where('id', $this->transfer_order_id)->where('transfer_deal_id', $this->id)->first();
        if ($transferOrder) {
            $showAccept = false;

            if ($transferOrder->recipient_liner_id === $user->id) {
                if ($transferOrder->recipient_sign != '') {
                    $signed = true;
                }
            }

            if ($transferOrder->owner_liner_id === $user->id) {
                if ($transferOrder->owner_sign != '') {
                    $signed = true;
                }
            }
        }

        return [
            'id' => $this->id,
            'transfer_order_id' => $this->transfer_order_id,
            'liner_id' => $this->liner_id,
            'liner' => new LinerResource($liner),
            'amount' => $this->amount,
            'date' => Carbon::parse($this->date)->format('Y-m-d H:i'),
            'transfer_order' => $transferOrder,
            'showAccept' => $showAccept,
            'signed' => $signed
        ];
    }
}
