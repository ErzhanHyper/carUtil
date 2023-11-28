<?php


namespace App\Services\Order;


use App\Models\Car;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Services\AuthService;
use App\Services\EdsService;
use App\Services\SignService;
use Illuminate\Http\Request;

class OrderApproveService
{

    public function approve($request, $id)
    {
        $user = app(AuthService::class)->auth();
        $order = Order::find($id);
        $can = true;
        $success = false;
        $message = '';

        if($user->id !== $order->executor_uid){
            $can = false;
        }

        if ($order) {
            $car = Car::where('order_id', $order->id)->first();

            $carDuplicate = Car::where('vin', $car->vin)->get();
            if($carDuplicate) {
                foreach ($carDuplicate as $item) {
                    $orderRel = Order::find($item->order_id);
                    if ($orderRel && $orderRel->approve === 3) {
                        $can = false;
                    }
                }
            }
            if($can === false) {
                $message = 'ТС с таким VIN кодом уже одобрено в другой заявке';
            }

            if($can) {
                $hash = app(SignService::class)->__signData($car->id);
                $edsSign = app(EdsService::class)->sign(new Request(['hash' => $hash]));
                if ($edsSign && $car) {
                    $car->moderator_accept_sign = $edsSign->sign;
                    if ($car->save()) {
                        $order->approve = 3;
                        $order->status = 4;
                        $order->save();
                        $message = 'Одобрено';
                        $success = true;
                    }
                }

                app(OrderService::class)->storeHistory(new Request([
                    'action' => 'APPROVED',
                    'order_id' => $order->id,
                    'comment' => '#'.$user->title.'('. $user->role. ')'. ': одобрил(а) заявку',
                    'user_id' => $user->id,
                ]));

                return [
                    'message' => $message,
                    'success' => $success
                ];
            }
        }
    }

    public function decline($request, $id)
    {
        $user = app(AuthService::class)->auth();

        $can = false;

        $order = Order::find($id);

        if($user->id === $order->executor_uid){
            $can = true;
        }

        if($can) {
            $order->approve = 2;
            $order->status = 3;
            $order->save();

            app(OrderService::class)->storeHistory(new Request([
                'action' => 'DECLINED',
                'order_id' => $order->id,
                'comment' => $request->comment,
                'user_id' => $user->id,
            ]));

            return ['decline'];
        }
    }

    public function revision($request, $id)
    {
        $user = app(AuthService::class)->auth();
        $order = Order::find($id);
        $can = false;

        if($user->id === $order->executor_uid){
            $can = true;
        }
        if($can) {
            $order->approve = 4;
            $order->save();

            app(OrderService::class)->storeHistory(new Request([
                'action' => 'RETURNED_TO_OPERATOR',
                'order_id' => $order->id,
                'comment' => $request->comment,
                'user_id' => $user->id,
            ]));

            return ['revision'];
        }
    }

    public function revisionVideo($request, $id)
    {
        $user = app(AuthService::class)->auth();
        $order = Order::find($id);

        if($user->id === $order->executor_uid) {
            $order->status = 4;
            $order->save();

            app(OrderService::class)->storeHistory(new Request([
                'action' => 'RETURNED_TO_OPERATOR_AFTER_SIGN',
                'order_id' => $order->id,
                'comment' => $request->comment,
                'user_id' => $user->id,
            ]));

            return ['revisionVideo'];
        }
    }


}
