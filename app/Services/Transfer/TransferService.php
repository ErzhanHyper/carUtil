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
use App\Models\TransferOrder;
use App\Services\AuthenticationService;

class TransferService
{

    public function getCollection($request)
    {
        $user = app(AuthenticationService::class)->auth();
        if ($user && $user->role === 'liner') {
            $orders = TransferOrder::where('closed', 0);
            return TransferOrderResource::collection($orders->orderByDesc('date')->get());
        }
    }

    public function getCurrenCollection($request)
    {
        $user = app(AuthenticationService::class)->auth();
        if ($user && $user->role === 'liner') {
            $orders = TransferOrder::where('owner_liner_id', $user->id)->orWhere('recipient_liner_id', $user->id);
            return TransferOrderResource::collection($orders->orderByDesc('date')->get());
        }
    }

    public function getById($id)
    {
        $user = app(AuthenticationService::class)->auth();
        if ($user && $user->role === 'liner') {
            $order = TransferOrder::find($id);
            return new TransferOrderResource($order);
        }
    }

    public function pfs($request)
    {
        $user = app(AuthenticationService::class)->auth();
        $order = TransferOrder::find($request->transfer_order_id);
        return $order;
    }

    public function store($request)
    {
        $user = app(AuthenticationService::class)->auth();
        $order = Order::find($request->order_id);
        $transfer = TransferOrder::where('order_id', $order->id)->first();
        if (!$transfer) {
            $transfer = new TransferOrder;
            $transfer->order_id = $order->id;
            $transfer->owner_liner_id = $user->id;
            $transfer->recipient_liner_id = null;
            $transfer->closed = 0;
            $transfer->date = time();
            $transfer->transfer_deal_id = null;
            $transfer->owner_sign = null;
            $transfer->recipient_sign = null;
            $transfer->owner_sign_time = null;
            $transfer->recipient_sign_time = null;
            $transfer->save();
            return ['created'];
        }

        return ['Уже выставлен на продажу'];
    }


    public function getCollectionDeal($request)
    {
        $user = app(AuthenticationService::class)->auth();
        if ($user && $user->role === 'liner') {
            $transfer_order = TransferOrder::find($request->transfer_order_id);
            if ($transfer_order->owner_liner_id === $user->id) {
                $deals = TransferDeal::where('transfer_order_id', $request->transfer_order_id);
                return TransferDealResource::collection($deals->orderByDesc('date')->get());
            }
        }
    }

    public function storeDeal($request)
    {
        $user = app(AuthenticationService::class)->auth();
        $transfer_order_id = $request->transfer_order_id;
        $amount = $request->amount;

        $transferOrder = TransferOrder::find($transfer_order_id);

        if ($user->id !== $transferOrder->ownner_liner_id) {
            $deal = new TransferDeal;
            $deal->liner_id = $user->id;
            $deal->transfer_order_id = $transfer_order_id;
            $deal->amount = $amount;
            $deal->date = time();
            $deal->save();

            return ['Сделка отправлена'];
        }
    }

    public function acceptDeal($id): array
    {
        $user = app(AuthenticationService::class)->auth();
        $transferDeal = TransferDeal::find($id);
        $transferOrder = $transferDeal->transfer_order;

        if ($user->id !== $transferOrder->ownner_liner_id) {
            $transferOrder->transfer_deal_id = $transferDeal->id;
            $transferOrder->recipient_liner_id = $transferDeal->liner_id;
            $transferOrder->closed = 1;
            $transferOrder->save();

            return ['Сделка выбрана'];
        }
    }

    public function closeDeal($id): array
    {
        $user = app(AuthenticationService::class)->auth();
        $transferDeal = TransferDeal::find($id);
        $transferOrder = $transferDeal->transfer_order;

        if ($user->id !== $transferOrder->owner_liner_id) {
            $transferOrder->transfer_deal_id = null;
            $transferOrder->recipient_liner_id = null;
            $transferOrder->closed = 0;
            $transferOrder->save();

            return ['Сделка выбрана'];
        }
    }

    public function sign($request): array
    {
        $user = app(AuthenticationService::class)->auth();
        $sign = $request->sign;
        $transfer_order_id = $request->transfer_order_id;

        $transfer_order = TransferOrder::find($transfer_order_id);
        $recipient_liner = Liner::find($transfer_order->recipient_liner_id);

        $client = Client::where('idnum', $recipient_liner->idnum)->first();
        $order = Order::find($transfer_order->order->id);

        if ($user->role === 'liner') {

            if ($transfer_order->owner_liner_id === $user->id) {
                $transfer_order->owner_sign = $sign;
                $transfer_order->owner_sign_time = time();
            }

            if ($transfer_order->recipient_liner_id === $user->id) {
                $transfer_order->recipient_sign = $sign;
                $transfer_order->recipient_sign_time = time();
                $transfer_order->closed = 2;
                $car = Car::where('order_id', $transfer_order->order_id)->first();
                $car->cert_title = $client->title;
                $car->cert_idnum = $client->idnum;
                $car->save();

                $order->client_id = $client->id;
                $order->save();
            }

            if (!$client) {
                $client = new Client;
                $client->title = json_decode($recipient_liner->profile)->fln;
                $client->idnum = $recipient_liner->idnum;
                $client->save();
            }

            if ($transfer_order) {

                $preorder = PreOrderCar::where('order_id', $transfer_order->order_id)->first();
                $preorder->client_id = $client->id;
                $preorder->liner_id = $recipient_liner->id;
                $preorder->save();

                if ($transfer_order->owner_sign != '' && $transfer_order->recipient_sign != '') {
                    $order = Order::find($transfer_order->order_id);
                    $order->client_id = $client->id;
                    $order->save();
                }
                $transfer_order->save();
            }
        }

        return ['Сделка подписана'];
    }

    public function close($request)
    {
        $user = app(AuthenticationService::class)->auth();
        $transfer_order_id = $request->id;

        $transferOrder = TransferOrder::find($transfer_order_id);

        if ($user->id === $transferOrder->owner_liner_id) {
            $transferOrder->delete();
            return ['закрыта'];
        }
    }
}
