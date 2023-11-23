<?php

namespace App\Http\Resources;

use App\Models\AgroFile;
use App\Models\BookingOrder;
use App\Models\CarFile;
use App\Models\Category;
use App\Models\File;
use App\Models\PreOrderCar;
use App\Models\User;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            2 => 'Отказано',
            3 => 'Одобрено',
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

        $booking = null;
        $categories = [];
        $files = [];
        $vehicleType = '';

        if($this->car) {
            if ($this->car->car_type_id === 1 || $this->car->car_type_id === 2) {
                $vehicleType = 'car';
                if($this->preorder) {
                    $files = CarFileResource::collection(CarFile::where('preorder_id', $this->preorder->id)->get());
                }
            } else {
                $vehicleType = 'agro';
                if($this->preorder) {
                    $files = AgroFile::where('preorder_id', $this->preorder->id)->get();
                }
            }
        }

        if($this->preorder && $this->preorder->booking_id) {
            $booking = BookingOrder::find($this->preorder->booking_id);
            $booking = new BookingOrderResource($booking);
        }


        $history = [];

        if($this->history) {
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
            'order_type' => $this->order_type,
            'pay_approve' => $this->pay_approve,
            'sended_to_approve' => date('d.m.Y H:i', $this->sended_to_approve),
            'sended_to_pay' => date('d.m.Y H:i', $this->sended_to_pay),
            'preorder_id' => $this->preorder ? $this->preorder->id : null,
            'booking' => $booking,
            'blocked' => $this->blocked,
            'files' => $files,
            'categories' => $categories,
            'transfer' => $this->transfer,
            'vehicleType' => $vehicleType,
            'history' => $history,
        ];
    }
}
