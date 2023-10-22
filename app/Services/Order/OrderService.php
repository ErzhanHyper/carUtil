<?php


namespace App\Services\Order;


use App\Http\Resources\OrderResource;
use App\Models\Car;
use App\Models\Category;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Services\AuthenticationService;
use Carbon\Carbon;

class OrderService
{
    public function getCollection()
    {
        $user = app(AuthenticationService::class)->auth();
        $factory_id = $user->factory_id;

        if ($user) {
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
            }
//
//            }

            if ($orders) {
                return OrderResource::collection($orders->orderByDesc('created')->where('created','>', 1697755551)->paginate(10));
            }
        }
    }

    public function getById($id)
    {
        $order = Order::find($id);
        $order->with(['car', 'client', 'preorder']);
        return new OrderResource($order);
    }

    public function sign($request)
    {
        $user = app(AuthenticationService::class)->auth();
        $response = ['success', false];

        $order_id = $request->order_id;
        if ($order_id) {
            $sign = $request->sign;
            $order = Order::find($order_id);
            if ($sign) {
                $car = Car::where('order_id', $order_id)->first();
                if ($user && $user->role === 'operator') {
                    $car->operator_sign = $sign;
                    $car->operator_sign_time = time();
                    if ($car->save()) {
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
        $user = app(AuthenticationService::class)->auth();
        $response = ['success', false];

        $order_id = $request->order_id;
        if ($order_id) {
            $order = Order::find($order_id);
            $car = Car::where('order_id', $order_id)->first();
            if ($request->status === 'approve') {
                $sign = $request->sign;
                if ($sign) {
                    if ($car) {
                        $car->moderator_accept_sign = $sign;
                        if ($car->save()) {
                            $order->approve = 3;
                            $order->status = 2;
                            $order->save();
                        }
                    }
                }
            } else if ($request->status === 'decline') {
                $order->approve = 2;
                $order->status = 3;
                $order->save();
            } else if ($request->status === 'revision') {
                $order->approve = 4;
                $order->status = 1;
                $order->save();
            }

            $response = ['success', true];
        }

        return $response;
    }

}
