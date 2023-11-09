<?php


namespace App\Services\Order;


use App\Http\Resources\OrderResource;
use App\Models\Car;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Services\AuthService;
use App\Services\EdsService;
use App\Services\SignService;
use Illuminate\Http\Request;


class OrderService
{
    public function getCollection()
    {
        $user = app(AuthService::class)->auth();
        $factory_id = $user->factory_id;

//            if ($user->role === 'liner') {
//                $orders = Order::with(['car', 'client', 'preorder']);
//                $orders->where('liner_id', $user->id);
//            } else {
            if ($user->role === 'moderator') {
                $orders = Order::with(['car', 'client', 'preorder']);
                $orders->where('approve', '<>', 0);
            } else if ($user->role === 'operator') {
                $orders = Order::with(['car', 'client', 'preorder', 'transfer'])->whereIn('approve', [0, 1, 2, 3, 4, 5])
                    ->select('order.id', 'order.client_id', 'order.approve', 'order.status', 'order.blocked')
                    ->join('pre_order_car', 'order.id', 'pre_order_car.order_id')
                    ->where('pre_order_car.factory_id', $factory_id)->where('blocked', 0);
            }else if($user->role === 'admin'){
                $orders = Order::with(['car', 'client', 'preorder']);
            }
//
//            }

            if ($orders) {
                return OrderResource::collection($orders->orderByDesc('created')->where('created', '>', 1697755551)->paginate(10));
            }
    }

    public function getById($id)
    {
        $order = Order::find($id);
        $order->with(['car', 'client', 'preorder', 'history']);
        return new OrderResource($order);
    }

    public function sign($request)
    {
        $user = app(AuthService::class)->auth();
        $response = ['success', false];

        $order_id = $request->order_id;
        if ($order_id) {
            $order = Order::find($order_id);
            $car = Car::where('order_id', $order_id)->first();
            $sign = $request->sign;
            $hash = app(SignService::class)->__signData($car->id);

            $sign_data = app(EdsService::class)->check(new Request([
                'sign' => $sign,
                'hash' => $hash
            ]));

            if ($sign_data) {
                if ($user && $user->role === 'operator') {
                    $car->operator_sign = $sign;
                    $car->operator_sign_time = time();
                    if ($car->save()) {
                        $order->user_id = $user->id;
                        $order->approve = 1;
                        $order->status = 2;
                        $order->sended_to_approve = time();
                        $order->save();
                    }
                    $response = ['success', true];
                }
            }
        }

        return $response;
    }

    public function approve($request)
    {
        $user = app(AuthService::class)->auth();
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $can = true;
        $success = false;
        $message = '';

        if ($order) {
            $car = Car::where('order_id', $order_id)->first();

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
                $message = 'ТС с таким VIN кодом уже одобрена в другой заявке';
            }

            if($can) {
                $hash = app(SignService::class)->__signData($car->id);
                $edsSign = app(EdsService::class)->sign(new Request(['hash' => $hash]));
                if ($edsSign && $car) {
                    $car->moderator_accept_sign = $edsSign->sign;
                    if ($car->save()) {
                        $order->approve = 3;
                        $order->status = 2;
                        $order->save();
                        $message = 'Одобрена!';
                        $success = true;
                    }
                }

                $this->storeHistory(new Request([
                    'action' => 'approve',
                    'order_id' => $order->id,
                    'comment' => 'Одобрено',
                    'user_id' => $user->id,
                ]));

                return [
                    'message' => $message,
                    'success' => $success
                ];
            }
        }
    }

    public function decline($request)
    {
        $user = app(AuthService::class)->auth();

        $order_id = $request->order_id;

        $order = Order::find($order_id);
        $order->approve = 2;
        $order->status = 3;
        $order->save();

        $this->storeHistory(new Request([
            'action' => 'decline',
            'order_id' => $order->id,
            'comment' => $request->comment,
            'user_id' => $user->id,
        ]));

        return ['decline'];
    }

    public function revision($request)
    {
        $user = app(AuthService::class)->auth();
        $order_id = $request->order_id;

        $order = Order::find($order_id);
        $order->approve = 4;
        $order->status = 1;
        $order->save();

        $this->storeHistory(new Request([
            'action' => 'revision',
            'order_id' => $order->id,
            'comment' => $request->comment,
            'user_id' => $user->id,
        ]));

        return ['revision'];
    }

    public function storeHistory($request)
    {
        $history = new OrderHistory();
        $history->action = $request->action;
        $history->order_id = $request->order_id;
        $history->comment = $request->comment;
        $history->user_id = $request->user_id;
        $history->created_at = time();
        $history->save();

        return $history;

    }

}
