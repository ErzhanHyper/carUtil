<?php


namespace App\Services\PreOrder;


use App\Models\Car;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\PreorderComment;
use InvalidArgumentException;

class ModeratorPreOrderService
{

    public function approve($request, $id)
    {
        $preorder = PreOrderCar::find($id);

        $car = Car::find($preorder->car_id);

        $comment_text = $request->comment;
        if ($request->status) {
            if ($request->status === 'decline') {
                $preorder->status = 3;
                $preorder->save();
            } else if ($request->status === 'revision') {
                $preorder->status = 4;
                $preorder->save();
            }

            if ($request->status === 'approve') {
                $preorder->status = 2;
                $comment_text = '';

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
                        if ($preorder->recycle_type) {
                            $car->car_type_id = ($preorder->recycle_type === 1) ? 1 : 3;
                            if ($car->save()) {
                                $preorder->order_id = $order->id;
                                $preorder->save();
                            }
                        }
                    }
                }
            }

            $comment = new PreorderComment;
            $comment->preorder_id = $preorder->id;
            $comment->text = $comment_text;
            $comment->created_at = time();
            $comment->action = $request->status;
            $comment->save();
        }

        return $preorder;
    }
}
