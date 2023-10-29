<?php


namespace App\Services\PreOrder;


use App\Models\Car;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use Illuminate\Http\Request;

class ModeratorPreOrderService
{

    public function approve($request, $id)
    {
        $preorder = PreOrderCar::find($id);
        $car = Car::find($preorder->car_id);
        $preorder->status = 2;

        $order = new Order;
        $order->client_id = $preorder->client_id;
        $order->created = time();
        $order->user_id = null;
        $order->approve = 0;
        $order->sended_to_approve = 0;
        $order->order_type = 2;
        $order->pay_approve = 0;
        $order->sended_to_pay = 0;
        $order->status = 0;
        $order->executor_uid = null;

        if ($order->save()) {
            if ($car) {
                $car->order_id = $order->id;
                $car->car_type_id = ($preorder->recycle_type === 1) ? 1 : 3;
                if ($car->save()) {
                    $preorder->order_id = $order->id;
                    $preorder->save();
                }
            }
        }

        app(PreorderCommentService::class)->run(new Request([
            'status' => 'approve',
            'comment' => ''
        ]), $preorder->id);

        return ['approve'];
    }


    public function decline($request, $id)
    {
        $preorder = PreOrderCar::find($id);
        $commentText = $request->comment;

        $preorder->status = 3;
        $preorder->save();

        app(PreorderCommentService::class)->run(new Request([
            'status' => 'decline',
            'comment' => $commentText
        ]), $preorder->id);

        return ['decline'];
    }

    public function revision($request, $id)
    {
        $preorder = PreOrderCar::find($id);
        $commentText = $request->comment;

        $preorder->status = 4;
        $preorder->save();

        app(PreorderCommentService::class)->run(new Request([
            'status' => 'revision',
            'comment' => $commentText
        ]), $preorder->id);

        return ['revision'];
    }
}
