<?php


namespace App\Services\Preorder;


use App\Models\Car;
use App\Models\Order;
use App\Models\PreOrderCar;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PreorderApproveService
{

    public function approve($request, $id)
    {
        $success = false;
        $message = 'Нет доступа';
        $can = true;
        $preorder = PreOrderCar::find($id);
        $car = Car::find($preorder->car_id);

        if($preorder){
            if($preorder->status !== 1){
                $can = false;
                $message = 'Предзаявка уже была одобрена';
            }
        }
        if($car){
            $carDuplicate = Car::where('vin', $car->vin)->get();
            if($carDuplicate) {
                foreach ($carDuplicate as $item) {
                    $orderRel = Order::find($item->order_id);
                    if ($orderRel && $orderRel->approve === 3) {
                        $can = false;
                        $message = 'ТС с таким VIN кодом уже одобрена';
                    }
                }
            }
        }

        if($can) {
            $preorder->status = config("constants.APPROVED_PREORDER");
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

                        app(PreorderCommentService::class)->run(new Request([
                            'status' => 'APPROVED',
                            'comment' => ''
                        ]), $preorder->id);

                        $success = true;
                        $message = 'Предзаявка одобрена';
                    }
                }
            }
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }


    public function decline($request, $id)
    {
        $preorder = PreOrderCar::find($id);
        $commentText = $request->comment;

        $preorder->status = config("constants.DECLINED_PREORDER");
        $preorder->save();

        app(PreorderCommentService::class)->run(new Request([
            'status' => 'DECLINED',
            'comment' => $commentText
        ]), $preorder->id);

        return ['decline'];
    }

    public function revision($request, $id)
    {
        $preorder = PreOrderCar::find($id);
        $commentText = $request->comment;

        $preorder->status = config("constants.RETURNED_BACK_PREORDER");
        $preorder->save();

        app(PreorderCommentService::class)->run(new Request([
            'status' => 'RETURNED_BACK_LINER',
            'comment' => $commentText
        ]), $preorder->id);

        return ['revision'];
    }
}
