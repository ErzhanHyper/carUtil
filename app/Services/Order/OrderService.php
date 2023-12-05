<?php


namespace App\Services\Order;

use App\Http\Resources\OrderResource;
use App\Models\Car;
use App\Models\Client;
use App\Models\File;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Services\AuthService;
use App\Services\EdsService;
use App\Services\SignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderService
{
    //Подписать и отправить заявку  на выдачу сертифката модератору
    public function sign($request, $id)
    {
        $user = app(AuthService::class)->auth();
        $response = ['success', false];

        $order = Order::find($id);
        if ($order) {
            $car = Car::where('order_id', $order->id)->first();
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
                        $order->status = 5;
                        $order->save();

                        $this->storeHistory(new Request([
                            'action' => 'SIGN_ACTION',
                            'order_id' => $order->id,
                            'user_id' => $user->id,
                            'comment' => '#' . $user->title . '(' . $user->role . ')' . ': подписал(а) заявку'
                        ]));
                    }
                    $response = ['success' => true];
                }
            }
        }

        return $response;
    }

    //Взять заявку на исполнение модератору
    public function executeRun($id)
    {
        $user = app(AuthService::class)->auth();
        $order = Order::find($id);
        $success = false;

        if ($order->approve === 1 && !$order->executor_uid) {
            $order->executor_uid = $user->id;
            $order->status = 2;
            $order->save();
            $success = true;

            $this->storeHistory(new Request([
                'action' => 'ORDER_STATUS_IN_PROCESSING',
                'order_id' => $order->id,
                'user_id' => $user->id,
                'comment' => '#' . $user->title . '(' . $user->role . ')' . ': взял(а) заявку на исполнение'
            ]));
        }

        return [
            'success' => $success,
        ];
    }

    //Отменить исполнение если не совершено никаких действий
    public function executeClose($id)
    {
        $user = app(AuthService::class)->auth();
        $success = false;
        $order = Order::find($id);
        if ($order->approve === 1) {
            if ($order->executor_uid === $user->id) {
                $order->executor_uid = null;
                if ($order->status === 2) {
                    $order->status = 1;
                }
                $order->save();
                $success = true;
            }
        }
        return [
            'success' => $success,
        ];
    }

    //Загрузка видеофайла оператором
    public function video($request, $id)
    {

        $message = 'Не загружена';
        $success = false;
        $upload = false;
        $order = Order::find($id);

        if ($order) {
            $car = Car::where('order_id', $order->id)->first();
            if ($order->approve === 0 || $order->approve === 1 || $order->approve === 2) {
                $message = 'Заявка не одобрена';
            } else if ($order->approve === 4) {
                $message = 'Невозможно загрузить видеозапись! Заявка отклонена';
            }
            if ($order->approve === 3 && $order->status === 3) {
                $message = 'Невозможно загрузить видеозапись! Сертификат уже выдан';
            }
            if ($order && $car && $order->approve === 3 && $order->status === 4) {
                $upload = true;
            }
        } else {
            $message = 'Заявка не найдена в базе';
        }

        if ($upload) {
            if (isset($_FILES['voice']) && $_FILES['voice']['error'] === 0) {
                $extension = explode('/', $_FILES['voice']['type']);
                $original_name = basename(md5($_FILES['voice']['tmp_name'] . time())) . '.' . $extension[1];
                $filePath = '/order/files/' . $order->id . '/' . $original_name;
                $path = Storage::disk('local')->path($filePath);
                move_uploaded_file($_FILES['voice']['tmp_name'], $path);

                app(OrderFileService::class)->store(new Request([
                    'order_id' => $order->id,
                    'car_id' => $car->id,
                    'file_type_id' => 29,
                    'client_id' => $order->client_id,
                    'extension' => $extension[1],
                    'original_name' => $original_name
                ]));

                $message = 'Видеозапись успешно отправлена';
                $success = true;
            }
        }

        return [
            'message' => $message,
            'success' => $success
        ];
    }

    //История действия модератора и оператора(История изменений)
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

    public function store($request)
    {
        $order = new Order;
        $order->client_id = $request->client_id;
        $order->created = time();
        $order->user_id = null;
        $order->approve = 0;
        $order->sended_to_approve = 0;
        $order->order_type = 2;
        $order->pay_approve = 0;
        $order->sended_to_pay = 0;
        $order->status = 0;
        $order->executor_uid = null;
        $order->save();

        return $order;
    }
}
