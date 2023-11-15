<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Factory;
use App\Models\File;
use App\Models\Liner;
use App\Models\Order;
use App\Models\TransferOrder;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PreOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        $status = match ($this->status) {
            0 => 'Формирование заявки',
            1 => 'На рассмотрении',
            2 => 'Одобрена',
            3 => 'Отклонена',
            4 => 'Возвращена на доработку',
            default => '',
        };

        if ($this->recycle_type === 1) {
            $vehicleType = 'car';
        } else {
            $vehicleType = 'agro';
        }

        $order = Order::find($this->order_id);
        $transfer = null;
        if($order) {
            $transfer = new TransferOrderResource(TransferOrder::where('order_id', $order->id)->first());
        }

        $date = date('d.m.Y', $this->date);
        $closedDate = strtotime($date . ' + 15 days');
        if($closedDate >= time()){
            $diffDate = $closedDate - time();
            $closedDays = date('j', $diffDate);
        }else{
            $closedDays = 0;
        }

        return [
            'id' => $this->id,
            'status' => [
                'id' => $this->status,
                'title' => $status
            ],
            'car' => new CarResource($this->car),
            'client' => new ClientResource($this->client),
            'order' => new OrderResource($this->order),
            'liner' => Liner::find($this->liner_id),
            'factory' => Factory::find($this->factory_id),
            'booking' => new BookingOrderResource($this->booking),
            'transfer' => $transfer,
            'date' => date('d.m.Y H:i', $this->date),
            'files' => $vehicleType === 'car' ? $this->car_file : $this->agro_file,
            'comment' => CommentResource::collection($this->comment),
            'vehicleType' => $vehicleType,
            'closedDate' => $closedDays
        ];
    }
}
