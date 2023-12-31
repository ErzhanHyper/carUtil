<?php


namespace App\Services\Order;


use App\Models\Car;
use App\Models\File;
use App\Models\FileType;
use App\Models\FileTypeAgro;
use App\Models\Order;
use App\Services\AuthService;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class OrderSendService
{
    protected OrderService $orderService;
    protected AuthService $authService;
    private string $message;
    private bool $success;

    public function __construct(AuthService $authService, OrderService $orderService)
    {
        $this->message = '';
        $this->success = false;
        $this->authService = $authService;
        $this->orderService = $orderService;
    }

    //Отправить заявку на рассмотрение модератору
    public function send($request, $id)
    {
        $this->resetValidationState();
        $user = $this->authService->auth();
        $order = Order::find($id);
        $can = false;

        if($order) {
            $can = $this->processFile($order);
        }

        if ($can) {
            $order->user_id = $user->id;
            $order->approve = 1;
            if ($order->status === 0) {
                $order->status = 1;
            }
            $order->sended_to_approve = time();
            $order->save();
            $this->success = true;

            $this->orderService->storeHistory(new Request([
                'action' => 'SENDED_TO_MODERATOR',
                'order_id' => $order->id,
                'user_id' => $user->id,
            ]));
        }

        return [
            'success' => $this->success,
            'message' => $this->message
        ];
    }

    private function processFile($order)
    {
        $car = $order->car;

        if (!$car) {
            return false;
        }

        $file_type_ids = $car->car_type_id === 1 || $car->car_type_id === 2
            ? [8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 36, 37]
            : [4, 5, 6, 7, 8, 9, 10, 11, 12];

        $file_types = $car->car_type_id === 1 || $car->car_type_id === 2
            ? FileType::whereIn('id', $file_type_ids)->orderBy('weight')
            : FileTypeAgro::whereIn('id', $file_type_ids);

        $docs = File::where('order_id', $order->id)->whereIn('file_type_id', $file_type_ids)->get();

        $required_ids = $docs->pluck('file_type_id')->all();
        $file_types = $file_types->whereNotIn('id', $required_ids)->get();

        if(count($file_types) > 0) {
            $count = $car->car_type_id === 1 || $car->car_type_id === 2 ? 12 : 9;
            if (count($file_types) <= $count) {
                $names = [Lang::get('messages.file_credentials')];

                foreach ($file_types as $item) {
                    $names[] = [$item->title];
                }
                $this->setMessage(json_encode($names));
                return false;
            }
        }

        return true;
    }


    private function resetValidationState()
    {
        $this->success = false;
        $this->message = '';
    }

    private function setMessage($message)
    {
        $this->message = $message;
    }
}
