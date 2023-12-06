<?php

namespace App\Http\Resources;

use App\Models\Client;
use App\Models\Factory;
use App\Models\Liner;
use App\Services\AuthService;
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

        $client = null;
        if ($user->role === 'liner') {
            if ($this->client && $this->client->idnum === $user->idnum) {
                $client = new ClientResource($this->client);
            } else {
                $clientFind = Client::where('idnum', $user->idnum)->orderByDesc('id')->first();
                $client = $clientFind ? new ClientResource($clientFind) : null;
            }
        } else {
            if ($this->client) {
                $client = new ClientResource($this->client);
            }
        }

        return [
            'id' => $this->id,
            'status' => [
                'id' => $this->status,
                'title' => $status
            ],
            'date' => date('d.m.Y H:i', $this->date),
            'sended_dt' => date('d.m.Y H:i', $this->sended_dt),

            'car' => new CarResource($this->car),
            'client' => $client,
            'order' => new OrderResource($this->order),
            'liner' => Liner::find($this->liner_id),
            'factory' => Factory::find($this->factory_id),
            'booking' => $this->booking ? new BookingOrderResource($this->booking) : null,

            'comment' => PreOrderHistoryResource::collection($this->history),
            'vehicleType' => $this->recycle_type === 1 ? 'car' : 'agro',
        ];
    }
}
