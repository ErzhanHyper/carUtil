<?php


namespace App\Services\Transfer;


use App\Http\Resources\TransferDealResource;
use App\Http\Resources\TransferOrderResource;
use App\Models\Car;
use App\Models\Client;
use App\Models\Liner;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\TransferDeal;
use App\Models\TransferLog;
use App\Models\TransferOrder;
use App\Services\AuthService;
use App\Services\Client\ClientService;
use App\Services\EdsService;
use App\Services\SignService;
use Illuminate\Http\Request;

class TransferService
{

    public function getCollection($request)
    {
        $user = app(AuthService::class)->auth();
        if ($user->role === 'liner') {
            $orders = TransferOrder::where('closed', 0);
        }
        if (isset($orders)) {
            $paginate = 15;
            $pages = round($orders->count() / $paginate);
            if ($pages == 0) {
                $pages = 1;
            }
            return [
                'pages' => $pages,
                'page' => $request->page ?? 1,
                'items' => TransferOrderResource::collection($orders->orderByDesc('date')->paginate($paginate))
            ];
        }
    }

    public function getCurrenCollection($request)
    {
        $user = app(AuthService::class)->auth();
        if ($user->role === 'liner') {
            $client = Client::where('idnum', $user->idnum)->first();
            $deal = TransferDeal::select(['id'])->where('client_id', $client->id)->get();
            $deal_ids = [];
            foreach ($deal as $item){
                $deal_ids[] = $item->id;
            }
            if($deal) {
                $orders = TransferOrder::whereIn('transfer_deal_id', $deal_ids)->orWhere('client_id', $client->id)->whereIn('closed', [0, 1, 2]);
            }
        }

        if(isset($orders)) {
            $paginate = 15;
            $pages = round($orders->count() / $paginate);
            if ($pages == 0) {
                $pages = 1;
            }
            return [
                'pages' => $pages,
                'page' => $request->page ?? 1,
                'items' => TransferOrderResource::collection($orders->orderByDesc('date')->orderBy('closed')->paginate($paginate))
            ];
        }
    }

    public function getById($id)
    {
        $user = app(AuthService::class)->auth();
        $client = Client::where('idnum', $user->idnum)->first();
        if ($user->role === 'liner') {
            $order = TransferOrder::find($id);
            $deal = TransferDeal::find($order->transfer_deal_id);
            if ($order->closed === 0) {
                return new TransferOrderResource($order);
            } else {
                if($deal) {
                    if ($order->client_id === $client->id || $deal->client_id === $client->id) {
                        return new TransferOrderResource($order);
                    }
                }
            }
        }
    }

    public function store($request)
    {
        $auth = app(AuthService::class)->auth();
        $client = Client::where('idnum', $auth->idnum)->first();

        $data = [];
        $success = false;
        $message = 'Нет доступа';

        if($auth->role === 'liner' && $client) {
            $order = Order::find($request->order_id);
            $transfer = TransferOrder::where('order_id', $order->id)->first();
            if (!$transfer && $order->blocked === 0) {
                $transfer = new TransferOrder;
                $transfer->order_id = $order->id;
                $transfer->client_id = $client->id;
                $transfer->closed = 0;
                $transfer->date = time();
                if ($transfer->save()) {
                    $order->blocked = 1;
                    $order->save();
                    $data = $transfer;
                    $success = true;
                    $message = 'Транспортное средство выставлена на продажу';
                }
            } else {
                $message = 'Транспортное средство уже была выставлена на продажу';
            }
        }

        return [
            'data' => $data,
            'success' => $success,
            'message' => $message
        ];
    }

    public function close($id)
    {
        $auth = app(AuthService::class)->auth();
        $transferOrder = TransferOrder::find($id);

        $message = 'Нет доступа';
        $success = false;

        if($transferOrder) {
            $can = false;

            $order = Order::find($transferOrder->order_id)->first();
            $client = Client::find($transferOrder->client_id);

            if($order && $order->blocked === 1) {
                if ($client && $auth->idnum === $client->idnum) {
                    if ($transferOrder->closed !== 2) {
                        $can = true;
                    }
                }
            }
            if($can){
                $order->blocked = 0;
                if($order->save()) {
                    $transferOrder->delete();
                    $message = 'Продажа транспортного средство отменена';
                    $success = true;
                }
            }
        }

        return [
            'data' => [],
            'message' => $message,
            'success' => $success
        ];
    }

    public function sign($request, $id): array
    {

        $message = 'Нет доступа';
        $success = false;

        $user = app(AuthService::class)->auth();
        $authClient = Client::where('idnum', $user->idnum)->first();
        $sign = $request->sign;
        $transfer_order = TransferOrder::find($id);
        $order = Order::find($transfer_order->order_id);

        $transfer_deal = TransferDeal::find($transfer_order->transfer_deal_id);
        $client = Client::find($transfer_order->client_id);
        $hash = app(SignService::class)->__signTransferData($transfer_order->id);

        $sign_data = app(EdsService::class)->check(new Request([
            'sign' => $sign,
            'hash' => $hash
        ]));

        if ($sign && $sign_data != '') {
            if($authClient->id === $transfer_order->client_id) {
                $this->ownerSign($id, $sign, $hash);
                $message = 'Успешно подписано!';
                $success = true;
            }

            if($authClient->id === $transfer_deal->client_id) {
                $this->receiverSign($id, $sign, $hash);

                $preorder = PreOrderCar::where('order_id', $transfer_order->order_id)->first();
                $preorder->client_id = $authClient->id;
                $preorder->liner_id = $user->id;

                $car = Car::where('order_id', $transfer_order->order_id)->first();

                $car->cert_title = $authClient->title;
                $car->cert_idnum = $authClient->idnum;

                $car->owner_title = $client->title;
                $car->owner_idnum = $client->idnum;

                $order->blocked = 0;
                $order->client_id = $client->id;

                $preorder->save();
                $car->save();
                $order->save();

                $message = 'Успешно подписано!';
                $success = true;
            }
        }

        return [
            'data' => [],
            'message' => $message,
            'success' => $success
        ];
    }


    private function ownerSign($id, $sign, $hash)
    {
        $transfer_order = TransferOrder::find($id);
        $transfer_order->owner_sign = $sign;
        $transfer_order->owner_sign_time = time();
        $transfer_order->hash = $hash;
        $transfer_order->save();
    }

    private function receiverSign($id, $sign, $hash)
    {
        $transfer_order = TransferOrder::find($id);
        $transfer_order->receiver_sign = $sign;
        $transfer_order->receiver_sign_time = time();
        $transfer_order->closed = 2;
        $transfer_order->save();
    }

    public function logData($request){
        $log = new TransferLog;
        $log->date = time();
        $log->action = $request->action;
        $log->transfer_order_id = $request->transfer_order_id;
        $log->transfer_deal_id = $request->transfer_deal_id;
        $log->sign = $request->sign;
        $log->idnum = $request->idnum;
        $log->title = $request->title;
        $log->save();
    }
}
