<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\File;
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

        $categories = [];

        $status = match ($this->status) {
            0 => 'Формирование заявки',
            1 => 'На рассмотрении',
            2 => 'Одобрена',
            3 => 'Отклонена',
            4 => 'Возвращена на доработку',
            default => '',
        };

        if ($this->recycle_type === 1) {
            $categories = Category::whereIn('id', [1, 2, 3, 4, 5, 6])->get();
        } else {
            $categories = Category::whereIn('id', [7, 8])->get();
        }

        $transfer = TransferOrder::where('order_id', $this->order_id)->first();

        $transferShow = false;
        if ($this->status === 2) {
            if ($this->order && $this->order->status === 0 && !$transfer && $this->order->approve === 0) {
                $transferShow = true;
            }
        }
        $video = '';
        if($this->order){
            $orderFile = File::where('order_id', $this->order['id'])->where('file_type_id', 29)->get();
            if(count($orderFile) > 0) {
                $video = $orderFile;
            }
        }

        $blocked = true;
        $blockedBooking = false;
        if($user && $user->role === 'liner'){
            if($this->status === 0 || $this->status === 4){
                $blocked = false;
                $blockedBooking = true;
            }else if($this->status === 1 || $this->status === 3){
                $blocked = true;
                $blockedBooking = true;
            }else{
                if($this->order && $this->order->status !== 0){
                    $blocked = true;
                    $blockedBooking = true;
                }
            }
        }else{
            $blockedBooking = true;
            $blocked = true;
        }

        return [
            'id' => $this->id,
            'status' => [
                'id' => $this->status,
                'title' => $status
            ],
            'car' => new CarResource($this->car),
            'client' => $this->client,
            'car_id' => $this->car_id,
            'client_id' => $this->client_id,
            'order_id' => $this->order_id,
            'order' => new OrderResource($this->order),
            'liner_id' => $this->liner_id,
            'factory_id' => $this->factory_id,
            'booking_id' => $this->booking_id,
            'booking' => new BookingOrderResource($this->booking),
            'recycle_type' => $this->recycle_type,
            'date' => Carbon::parse($this->date)->format('Y-m-d H:i'),
            'files' => $this->recycle_type === 1 ? $this->car_file : $this->agro_file,
            'categories' => $categories,
            'comment' => CommentResource::collection($this->comment),
            'transferShow' => $transferShow,
            'transfer' => $transfer,
            'video' => $video,
            'blockedVideo' => $this->order && $this->order['status'] === 3,
            'blocked' => $blocked,
            'blockedBooking' => $blockedBooking
        ];
    }
}
