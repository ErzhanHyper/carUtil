<?php


namespace App\Services\Transfer;

use App\Http\Resources\TransferDealResource;
use App\Models\Client;
use App\Models\TransferDeal;
use App\Models\TransferOrder;
use App\Services\AuthService;
use App\Services\Client\ClientService;

class TransferDealService
{
    public function getCollection($request)
    {
        $auth = app(AuthService::class)->auth();
        if ($auth->role === 'liner') {
            $transfer_order = TransferOrder::find($request->transfer_order_id);
            if ($transfer_order->liner_id === $auth->id) {
                $deals = TransferDeal::where('transfer_order_id', $transfer_order->id);
                return ['items' => TransferDealResource::collection($deals->orderByDesc('date')->get())];
            }
        }
    }

    public function store($request)
    {
        $auth = app(AuthService::class)->auth();

        $message = 'Нет доступа';
        $data = [];
        $success = false;

        if($auth->role === 'liner') {
            $transfer_order_id = $request->transfer_order_id;
            $amount = $request->amount;

            $transferOrder = TransferOrder::find($transfer_order_id);
            $owner_client = Client::find($transferOrder->client_id);
            $client = app(ClientService::class)->create($request->client);

            if ($amount && $client && $owner_client) {
                if ($auth->idnum !== $owner_client->idnum && $owner_client->idnum !== $client->idnum) {

                    if($auth->idnum === $client->idnum) {
                        $exist = TransferDeal::where('client_id', $client->id)->where('transfer_order_id', $transferOrder->id)->first();

                        $deal = new TransferDeal;
                        $deal->client_id = $client->id;
                        $deal->liner_id = $auth->id;
                        $deal->transfer_order_id = $transfer_order_id;
                        $deal->amount = $amount;
                        $deal->date = time();

                        if (!$exist) {
                            $deal->save();
                            $message = 'Предложение отправлена владельцу';
                            $success = true;
                        }
                    }else{
                        $message = 'ИИН не совпадает с учетными данными';
                    }
                }
            } else {
                $message = 'Не все данные заполнены';
            }
        }

        return [
            'message' => $message,
            'success' => $success,
            'data' => $data,
        ];
    }

    public function accept($id): array
    {
        $auth = app(AuthService::class)->auth();
        $transferDeal = TransferDeal::find($id);
        $transferOrder = TransferOrder::find($transferDeal->transfer_order_id);
        $client = Client::find($transferOrder->client_id);

        $success = false;
        $message = 'Нет доступа';

        if($auth->role === 'liner') {
            if ($auth->idnum === $client->idnum) {
                $transferOrder->transfer_deal_id = $transferDeal->id;
                $transferOrder->closed = 1;
                $transferOrder->approved_dt = time();
                $transferOrder->save();

                $success = true;
                $message = 'Предложение выбрана!';
            }
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function close($id): array
    {
        $user = app(AuthService::class)->auth();

        $transferDeal = TransferDeal::find($id);
        $transferOrder = TransferOrder::find($transferDeal->transfer_order_id);
        $authClient = Client::find($transferOrder->client_id);

        $success = false;
        $message = '';
        $data = [];

        if ($authClient->id === $transferOrder->client_id) {
            $transferOrder->transfer_deal_id = null;
            $transferOrder->approved_dt = 0;
            $transferOrder->owner_sign = '';
            $transferOrder->receiver_sign = '';
            $transferOrder->hash = '';
            $transferOrder->owner_sign_time = 0;
            $transferOrder->receiver_sign_time = 0;
            $transferOrder->closed = 0;
            $transferOrder->save();

            $success = true;
            $message = 'Выбор отменен!';
        }

        return [
            'data' => $data,
            'success' => $success,
            'message' => $message
        ];
    }

    public function delete($id){
        $user = app(AuthService::class)->auth();

        $deal = TransferDeal::find($id);

        $authClient = Client::find($deal->client_id);

        if($deal) {
            if ($authClient->id === $deal->client_id) {
                $transferOrder = TransferOrder::find($deal->transfer_order_id);
                if($transferOrder) {
                    if ($transferOrder->transfer_deal_id === $deal->id) {
                        $transferOrder->transfer_deal_id = null;
                        $transferOrder->approved_dt = 0;
                        $transferOrder->owner_sign = '';
                        $transferOrder->receiver_sign = '';
                        $transferOrder->hash = '';
                        $transferOrder->owner_sign_time = 0;
                        $transferOrder->receiver_sign_time = 0;
                        $transferOrder->closed = 0;
                        $transferOrder->save();
                    }
                }
                $deal->delete();
                return [
                    'success' => true,
                    'message' => 'Предложение отменено'
                ];
            }
        }
    }
}
