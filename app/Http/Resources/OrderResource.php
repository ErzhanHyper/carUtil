<?php

namespace App\Http\Resources;

use App\Models\BookingOrder;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        $approve = match ($this->approve) {
            0 => 'Новая заявка',
            1 => 'На рассмотрении',
            2 => 'Отказана',
            3 => 'Одобрена',
            4 => 'Возвращена на доработку',
            default => '',
        };

        $status = match ($this->status) {
            0 => 'Не определено',
            1 => 'Открыта',
            2 => 'В работе',
            3 => 'Завершено',
            4 => 'В ожидании видеозаписи',
            5 => 'На выдаче сертификата',
            default => '',
        };

        $globalStatus = '';
        if ($this->status === 0) {
            $globalStatus = 'Новая заявка';
        }else if ($this->status === 2 || $this->status === 1) {
            $globalStatus = 'На рассмотрении';
        }else if ($this->status === 2 && $this->approve === 3) {
            $globalStatus = 'Одобрено';
        }else if ($this->status === 2 && $this->approve === 2) {
            $globalStatus = 'Отказано';
        }else if ($this->status === 4) {
            $globalStatus = 'В ожидании видеозаписи';
        }else if ($this->status === 5) {
            $globalStatus = 'На выдаче сертификата';
        }else if ($this->status === 3) {
            $globalStatus = 'Завершено';
        }

        $booking = null;
        $vehicleType = '';

        if ($this->car) {
            if ($this->car->car_type_id === 1 || $this->car->car_type_id === 2) {
                $vehicleType = 'car';
            } else {
                $vehicleType = 'agro';
            }
        }

        if ($this->preorder && $this->preorder->booking_id) {
            $booking = BookingOrder::find($this->preorder->booking_id);
            $booking = new BookingOrderResource($booking);
        }

        $history = [];
        if ($this->history) {
            $history = OrderHistoryResource::collection($this->history);
        }

        return [
            'approve' => [
                'id' => $this->approve,
                'title' => $approve
            ],
            'status' => [
                'id' => $this->status,
                'title' => $status
            ],
            'id' => $this->id,
            'car' => new CarResource($this->car),
            'client' => new ClientResource($this->client),
            'created' => date('d.m.Y H:i', $this->created),
            'user' => User::find($this->user_id),
            'executor' => User::find($this->executor_uid),
            'sended_to_approve' => date('d.m.Y H:i', $this->sended_to_approve),
            'preorder_id' => $this->preorder ? $this->preorder->id : null,
            'booking' => $booking,
            'blocked' => $this->blocked,
            'transfer' => $this->transfer,
            'vehicleType' => $vehicleType,
            'history' => $history,
            'order_type' => $this->order_type === 1 ? 'Компенсация' : 'Сертификат',
            'globalStatus' => $globalStatus
        ];
    }
}
