<?php


namespace App\Services\Order;


use App\Http\Resources\FileResource;
use App\Models\Car;
use App\Models\File;
use App\Models\FileType;
use App\Models\FileTypeAgro;
use App\Models\Order;
use App\Services\AuthService;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Request;

class OrderSendService
{

    protected OrderService $orderService;
    protected AuthService $authService;
    private string $message;
    private bool $success;

    public function __construct(
        AuthService $authService,
        OrderService $orderService,
    )
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

//            $this->orderService->storeHistory(new Request([
//                'action' => 'SENDED_TO_MODERATOR',
//                'order_id' => $order->id,
//                'user_id' => $user->id,
//            ]));
        }

        return [
            'success' => $this->success,
            'message' => $this->message
        ];
    }

    private function processFile($order)
    {
        $car = Car::where('order_id', $order->id)->first();

        if($car) {

            if (in_array($car->car_type_id, [1, 2])) {
                $file_type_ids = [8,9,10,11,12,13,14,15,16,36,37];
            } else {
                $file_type_ids = [4,5,6,7,8,9,10,11,12];
            }

            $files = File::select(['order_id', 'file_type_id'])->where('order_id', $order->id)->whereIn('file_type_id', $file_type_ids)->get();
            $docs = File::where('order_id', $order->id)->whereIn('file_type_id', $file_type_ids)->get();

            $required_ids = [];
            if(count($docs) > 0) {
                foreach ($docs as $file) {
                    $required_ids[] = $file->file_type_id;
                }
            }

            if(count($files) <= 11) {
                $names = [Lang::get('messages.file_credentials')];
                if (in_array($car->car_type_id, [1, 2])) {
                    $file_types = FileType::whereIn('id', $file_type_ids)->whereNotIn('id', $required_ids)->orderBy('weight')->get();
                } else {
                    $file_types = FileTypeAgro::whereIn('id', $file_type_ids)->whereNotIn('id', $required_ids)->orderBy('weight')->get();
                }
                foreach ($file_types as $item){
                    $names[] = $item->title;
                }
                $this->setMessage(json_encode($names));
                return false;
            }

            return true;
        }
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
