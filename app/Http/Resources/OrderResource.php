<?php

namespace App\Http\Resources;

use App\Models\BookingOrder;
use App\Models\CarFile;
use App\Models\Category;
use App\Models\File;
use App\Models\PreOrderCar;
use App\Services\AuthenticationService;
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
       $user = app(AuthenticationService::class)->auth();

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
            default => '',
        };

        $find_video = File::where('order_id', $this->id)->where('file_type_id', 29)->first();

        $booking = [];
        $files = [];
        $categories = [];
        $recycle_type = '';
        if($this->preorder && $this->preorder->booking_id) {
            $booking = BookingOrder::find($this->preorder->booking_id);
            $booking = new BookingOrderResource($booking);
            if($this->preorder->recycle_type === 1){
                $categories = Category::whereIn('id', [1, 2, 3, 4, 5, 6])->get();
                $recycle_type = 'ВЭТС';
                $files = CarFile::where('preorder_id', $this->preorder->id)->get();
            }else{
                $recycle_type = 'ВЭССХТ';
                $categories = Category::whereIn('id', [7, 8])->get();
            }
        }

        return [
            'id' => $this->id,
            'car' => new CarResource($this->car),
            'client' => $this->client,
            'created' => Carbon::parse($this->created)->format('Y-m-d H:i'),
            'user_id' => $this->user_id,
            'status' => [
                'id' => $this->status,
                'title' => $status
            ],
            'sended_to_approve' => $this->sended_to_approve,
            'order_type' => $this->order_type,
            'pay_approve' => $this->pay_approve,
            'sended_to_pay' => $this->sended_to_pay,
            'approve' => [
                'id' => $this->approve,
                'title' => $approve
            ],
            'executor_uid' => $this->executor_uid,
            'preorder_id' => $this->preorder ? $this->preorder->id : null,
            'booking' => $booking,
            'files' => $files,
            'signed' => false,
            'setCert' => ($this->approve === 3 && $find_video && $user->role === 'moderator' && $this->car->certificate == '') ? true : false,
            'recycle_type' => $recycle_type,
            'categories' => $categories,
            'transfer' => $this->transfer
        ];
    }
}
