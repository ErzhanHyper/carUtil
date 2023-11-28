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

    public function getCollection($request): array
    {
        $user = app(AuthService::class)->auth();
        $orders = Order::with(['car', 'client', 'preorder', 'transfer']);

        if ($user->role === 'moderator') {
            $orders->where('approve', '<>', 0)
                ->when(isset($request->type) && $request->type != '', function ($query) use ($request) {
                    $query->whereHas('car', function ($q) use ($request) {
                        $q->where('car_type_id', $request->type === 'ВЭТС' ? [1, 2] : [3, 4]);
                    });
                });
            $this->applyFilters($orders, $request);

        } else if ($user->role === 'operator') {
            $factory_id = $user->factory_id;
            $orders->whereIn('approve', [0, 1, 2, 3, 4, 5])
                ->select('order.id', 'order.client_id', 'order.approve', 'order.status', 'order.blocked', 'order.executor_uid')
                ->join('pre_order_car', 'order.id', 'pre_order_car.order_id')
                ->where('pre_order_car.factory_id', $factory_id)
                ->where('blocked', 0);
        }

        $data = [];

        if (isset($orders)) {
            $paginate = 15;
            $pages = ceil($orders->count() / $paginate);
            $data = [
                'pages' => $pages,
                'page' => $request->page ?? 1,
                'items' => OrderResource::collection(
                    $orders->orderByDesc('created')->paginate($paginate)
                ),
            ];
        }

        return $data;
    }

    private function applyFilters($orders, $request): void
    {
        foreach (['title', 'idnum'] as $filter) {
            if (isset($request->$filter) && $request->$filter != '') {
                $clientIds = Client::select(['id', 'title', 'idnum'])->where($filter, 'like', '%' . $request->$filter . '%')->pluck('id')->toArray();
                $orders->whereIn('client_id', $clientIds);
            }
        }
        foreach (['vin', 'grnz'] as $filter) {
            if (isset($request->$filter) && $request->$filter != '') {
                $orderIds = Car::select(['vin', 'grnz', 'order_id'])->where($filter, 'like', '%' . $request->$filter . '%')->pluck('order_id')->toArray();
                $orders->whereIn('id', $orderIds);
            }
        }
        foreach (['approve'] as $filter) {
            if (isset($request->$filter) && $request->$filter != '') {
                $orders->where('approve', $request->$filter);
            }
        }
    }

    public function getById($id)
    {
        $user = app(AuthService::class)->auth();

        if ($user->role === 'moderator' || $user->role === 'operator') {
            $order = Order::find($id);
            $order->with(['car', 'client', 'preorder', 'history']);

            return [
                'item' => new OrderResource($order),
                'permissions' => $this->permission($order)
            ];
        }
    }

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

    //Отправить заявку на рассмотрение модератору
    public function send($request, $id)
    {
        $user = app(AuthService::class)->auth();

        $order = Order::find($id);

        if ($order) {
            $order->user_id = $user->id;
            $order->approve = 1;
            if ($order->status === 0) {
                $order->status = 1;
            }
            $order->sended_to_approve = time();
            $order->save();

            $this->storeHistory(new Request([
                'action' => 'SENDED_TO_MODERATOR',
                'order_id' => $order->id,
                'user_id' => $user->id,
            ]));
        }

        return [
            'success' => true
        ];
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

    //Предоставление доступа о действиях для фронта(для отображения кнопок)
    private function permission($order)
    {
        $user = app(AuthService::class)->auth();

        $canSendToApprove = false;
        $canApprove = false;
        $canReturnBackToOperator = false;
        $canExecute = false;
        $canUploadVideo = false;
        $canSendToIssueCert = false;
        $canIssueCert = false;
        $canCheckKap = false;

        $blockedFile = true;
        $blockedVideo = true;

        $video = false;
        $orderFile = File::where('order_id', $order->id)->where('file_type_id', 29)->exists();
        if ($orderFile) {
            $video = true;
        }

        if ($user->role === 'operator') {
            if ($order->status === 4 && !$video) {
                $canUploadVideo = true;
            }
            if ($order->status === 4 && $video) {
                $canSendToIssueCert = true;
                $blockedVideo = false;
            }
            if ($order->approve === 0 || $order->approve === 4) {
                $canSendToApprove = true;
                $blockedFile = false;
            }
        } else if ($user->role === 'moderator') {
            if ($order->status === 5) {
                $canReturnBackToOperator = true;
            }
            if ($order->approve === 1) {
                if (!$order->executor_uid) {
                    $canExecute = true;
                }
                if ($order->executor_uid && $order->executor_uid === $user->id) {
                    $canApprove = true;
                }
            } else if ($order->approve === 3) {
                if ($order->status === 5) {
                    if ($video && $order->car->certificate == '') {
                        $canIssueCert = true;
                    }
                }
            }
        }

        return [
            'can_send_to_approve' => $canSendToApprove,

            'can_execute' => $canExecute,
            'can_check_kap' => $canCheckKap,
            'can_approve' => $canApprove,

            'can_upload_video' => $canUploadVideo,
            'can_return_back' => $canReturnBackToOperator,

            'can_send_to_issue_cert' => $canSendToIssueCert,

            'can_issue_cert' => $canIssueCert,
            'blockedVideo' => $blockedVideo,
            'blockedFile' => $blockedFile,
        ];
    }

}
