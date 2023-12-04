<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Client;
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
        $user = app(AuthService::class)->auth();

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

        $date = date('d.m.Y', $this->date);
        $closedDate = strtotime($date . ' + 15 days');
        if($closedDate >= time()){
            $diffDate = $closedDate - time();
            $closedDays = date('j', $diffDate);
        }else{
            $closedDays = 0;
        }

        $client = null;
        if($user->role === 'liner') {
            if ($this->client && $this->client->idnum === $user->idnum) {
                $client = new ClientResource($this->client);
            }else{
                $clientFind = Client::where('idnum', $user->idnum)->orderByDesc('id')->first();
                $client = $clientFind ? new ClientResource($clientFind) : null;
            }
        }else{
            $client = new ClientResource($this->client);
        }

        return [
            'id' => $this->id,
            'status' => [
                'id' => $this->status,
                'title' => $status
            ],
            'car' => new CarResource($this->car),
            'client' => $client,
            'order' => new OrderResource($this->order),
            'liner' => Liner::find($this->liner_id),
            'factory' => Factory::find($this->factory_id),
            'booking' => new BookingOrderResource($this->booking),
            'date' => date('d.m.Y H:i', $this->date),
            'files' => $vehicleType === 'car' ? $this->car_file : $this->agro_file,
            'comment' => PreOrderHistoryResource::collection($this->history),
            'vehicleType' => $vehicleType,
            'closedDate' => $closedDays
        ];
    }
}
