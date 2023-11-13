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
                $message = 'ТС с таким VIN кодом уже одобрена в другой заявке';
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

    public function decline($request, $id)
    {
        $user = app(AuthService::class)->auth();

        $order = Order::find($id);
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

    public function revision($request, $id)
    {
        $user = app(AuthService::class)->auth();
        $order = Order::find($id);
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

    public function revisionVideo($request, $id)
    {
        $user = app(AuthService::class)->auth();
        $order = Order::find($id);
        $order->status = 4;
        $order->save();

        $this->storeHistory(new Request([
            'action' => 'revisionVideo',
            'order_id' => $order->id,
            'comment' => $request->comment,
            'user_id' => $user->id,
        ]));

        return ['revisionVideo'];
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
