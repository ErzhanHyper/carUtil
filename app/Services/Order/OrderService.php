<?php


namespace App\Services\Order;


use App\Http\Resources\OrderResource;
use App\Models\Car;
use App\Models\File;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Services\AuthService;
use App\Services\EdsService;
use App\Services\SignService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class OrderService
{

    public function getCollection($request)
    {
        $user = app(AuthService::class)->auth();
        $factory_id = $user->factory_id;
        if ($user->role === 'moderator') {
            $orders = Order::with(['car', 'client', 'preorder']);
            $orders->where('approve', '<>', 0);
        } else if ($user->role === 'operator') {
            $orders = Order::with(['car', 'client', 'preorder', 'transfer'])->whereIn('approve', [0, 1, 2, 3, 4, 5])
                ->select('order.id', 'order.client_id', 'order.approve', 'order.status', 'order.blocked', 'order.executor_uid')
                ->join('pre_order_car', 'order.id', 'pre_order_car.order_id')
                ->where('pre_order_car.factory_id', $factory_id)->where('blocked', 0);
        }else if($user->role === 'admin'){
            $orders = Order::with(['car', 'client', 'preorder']);
        }
        if (isset($orders)) {
            $paginate = 10;
            $pages = round($orders->count() / $paginate);
            if ($pages == 0) {
                $pages = 1;
            }
            return [
                'pages' => $pages,
                'page' => $request->page ?? 1,
                'items' => OrderResource::collection($orders->orderByDesc('created')
//                ->where('created', '>', 1697755551)
                    ->paginate($paginate))
            ];
        }
    }

    public function getById($id)
    {
        $user = app(AuthService::class)->auth();

        if($user->role === 'moderator' || $user->role === 'operator') {

            $order = Order::find($id);
            $order->with(['car', 'client', 'preorder', 'history']);

            $video = false;
            $orderFile = File::where('order_id', $order->id)->where('file_type_id', 29)->first();
            if ($orderFile) {
                $video = true;
            }

            $canApprove = false;
            $canExecute = false;
            $canIssueCert = false;
            $canSend = false;
            $canRevisionVideo = false;
            $canUploadVideo = false;
            $canSendToIssueCert = false;
            $blocked = true;
            $blockedVideo = true;

            if ($user->role === 'operator') {
                if ($order->status === 4 && !$video) {
                    $canUploadVideo = true;
                }
                if ($order->status === 4 && $video) {
                    $canSendToIssueCert = true;
                    $blockedVideo = false;
                }
                if ($order->approve === 0 || $order->approve === 4) {
                    $canSend = true;
                    $blocked = false;
                }
            } else if ($user->role === 'moderator') {
                if ($order->status === 5) {
                    $canRevisionVideo = true;
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
                'item' => new OrderResource($order),
                'permissions' => [
                    'approveOrder' => $canApprove,
                    'executeOrder' => $canExecute,
                    'sendToApprove' => $canSend,
                    'issueCert' => $canIssueCert,
                    'uploadVideo' => $canUploadVideo,
                    'revisionVideo' => $canRevisionVideo,
                    'sendToIssueCert' => $canSendToIssueCert,
                    'blocked' => $blocked,
                    'blockedVideo' => $blockedVideo
                ],
            ];
        }
    }

    public function sign($request, $id)
    {
        $user = app(AuthService::class)->auth();
        $response = ['success', false];

        $order = Order::find($id);
        if($order){
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
                    }
                    $response = ['success', true];
                }
            }
        }

        return $response;
    }

    public function send($request, $id)
    {
        $user = app(AuthService::class)->auth();

        $order = Order::find($id);

        if($order) {
            $order->user_id = $user->id;
            $order->approve = 1;
            $order->status = 2;
            $order->sended_to_approve = time();
            $order->save();
        }

        return [
            'success' => true
        ];
    }

    public function executeRun($id)
    {
        $user = app(AuthService::class)->auth();
        $order = Order::find($id);
        $success = false;
        if($order->approve !== 1 || $order->approve !== 0) {
            if (!$order->executor_uid) {
                $order->executor_uid = $user->id;
                $order->save();
                $success = true;
            }
        }

        return [
            'success' => $success,
        ];
    }

    public function executeClose($id)
    {
        $user = app(AuthService::class)->auth();
        $success = false;
        $order = Order::find($id);
        if($order->approve !== 1 || $order->approve !== 0) {
            if ($order->executor_uid === $user->id) {
                $order->executor_uid = null;
                $order->save();
                $success = true;
            }
        }
        return [
            'success' => $success,
        ];
    }

    public function video($request, $id)
    {

        $message = 'Не загружена';
        $success = false;
        $upload = false;
        $order = Order::find($id);

        if ($order) {
            if ($order->approve === 0 || $order->approve === 1 || $order->approve === 2) {
                $message = 'Заявка не одобрена';
            } else if ($order->approve === 4) {
                $message = 'Невозможно загрузить видеозапись! Заявка отклонена';
            }
            if ($order->approve === 3 && $order->status === 3) {
                $message = 'Невозможно загрузить видеозапись! Сертификат уже выдан';
            }
            $car = Car::where('order_id', $order->id)->first();
            if ($order && $car && $order->approve === 3 && $order->status === 4) {
                $upload = true;
            }
        } else {
            $message = 'Заявка не найдена в базе';
        }

        if($upload) {

            if(isset($_FILES['voice']) && $_FILES['voice']['error'] === 0) {
                $extension = explode('/', $_FILES['voice']['type']);
                $original_name = basename(md5($_FILES['voice']['tmp_name'] . time())) . '.' . $extension[1];

                $path = public_path('storage/uploads/order/files/' . $order->id . '/' . $original_name);
                move_uploaded_file($_FILES['voice']['tmp_name'], $path);

                $file = new File();
                $file->order_id = $order->id;
                $file->car_id = $car->id;
                $file->file_type_id = $car->id;
                $file->file_type_id = 29;
                $file->client_id = $order->client_id;
                $file->ext = $extension[1];
                $file->original_name = $original_name;
                $file->save();

                $message = 'Видеозапись успешно отправлена';
                $success = true;
            }
        }

        return [
            'message' => $message,
            'success' => $success
        ];
    }

}
