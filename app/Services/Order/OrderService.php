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

            if (isset($request->type) && $request->type != '') {
                if($request->type === 'ВЭТС') {
                    $orders->whereHas('car', function($q){
                        $q->where('car_type_id', [1,2]);
                    });
                }else{
                    $orders->whereHas('car', function($q){
                        $q->where('car_type_id', [3,4]);
                    });
                }
            }

            if (isset($request->approve) && $request->approve != '') {
                $orders->where('approve', $request->approve);
            }

            if (isset($request->title) && $request->title != '') {
                $client = Client::select(['id', 'title'])->where('title', 'like', '%' . $request->title . '%')->get();
                $client_ids = [];
                if (count($client) > 0) {
                    foreach ($client as $c) {
                        $client_ids[] = $c->id;
                    }
                }
                $orders->whereIn('client_id', $client_ids);
            }

            if (isset($request->idnum) && $request->idnum != '') {
                $client = Client::select(['id', 'idnum'])->where('idnum', 'like', '%' . $request->idnum . '%')->get();
                $client_ids = [];
                if (count($client) > 0) {
                    foreach ($client as $c) {
                        $client_ids[] = $c->id;
                    }
                }
                $orders->whereIn('client_id', $client_ids);
            }

            if (isset($request->vin) && $request->vin != '') {
                $car = Car::select(['id', 'vin', 'order_id'])->where('vin', 'like', '%' . $request->vin . '%')->get();
                $car_ids = [];
                if (count($car) > 0) {
                    foreach ($car as $c) {
                        $car_ids[] = $c->order_id;
                    }
                }
                $orders->whereIn('id', $car_ids);
            }

            if (isset($request->grnz) && $request->grnz != '') {
                $car = Car::select(['id', 'grnz','order_id'])->where('grnz', 'like', '%' . $request->grnz . '%')->get();
                $car_ids = [];
                if (count($car) > 0) {
                    foreach ($car as $c) {
                        $car_ids[] = $c->order_id;
                    }
                }
                $orders->whereIn('id', $car_ids);
            }

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

    public function checkDuplicates($id){
        $user = app(AuthService::class)->auth();

            $order = Order::find($id);
            $car = Car::where('order_id', $order->id)->first();
            $c_repeat = [];
            $vin = trim($car->vin);
            if (mb_strlen($vin) > 5) {
                $short_vin = mb_substr($vin, -5, 5);
                $t_car = Car::select(['order_id', 'vin', 'id'])->where('vin', 'like', '%' . $short_vin . '%')->where('id', '!=', $car->id)->get();
                $tca = array();
                foreach ($t_car as $nn => $tc) {
                    if($tc->vin) {
                        $tca[] = array('id' => $tc->id, 'order_id' => $tc->order_id, 'vin' => $tc->vin);
                    }
                }
                $c_repeat[$vin] = $tca;
                unset($tca, $t_car);
            }

            $body_repeat = [];
            $body = trim($car->body_no);
            if (mb_strlen($body) > 5) {
                $short_body = mb_substr($body, -5, 5);
                $t_car = Car::select(['order_id', 'body_no', 'id'])->where('body_no', 'like', '%' . $short_body . '%')->where('id', '!=', $car->id)->get();
                $tca = array();
                foreach ($t_car as $nn => $tc) {
                    if($tc->body_no) {
                        $tca[] = array('id' => $tc->id, 'body_no' => $tc->body_no, 'order_id' => $tc->order_id,);
                    }
                }
                $body_repeat[$body] = $tca;
                unset($tca, $t_car);
            }

            $chassis_repeat = [];
            $chassis = trim($car->chassis_no);
            if (mb_strlen($chassis) > 5) {
                $short_chassis = mb_substr($chassis, -5, 5);
                $t_car = Car::select(['order_id', 'chassis_no', 'id'])->where('chassis_no', 'like', '%' . $short_chassis . '%')->where('id', '!=', $car->id)->get();
                $tca = array();
                foreach ($t_car as $nn => $tc) {
                    if($tc->chassis_no) {
                        $tca[] = array('id' => $tc->id, 'chassis_no' => $tc->chassis_no, 'order_id' => $tc->order_id,);
                    }
                }
                $chassis_repeat[$chassis] = $tca;
                unset($tca, $t_car);
            }

            $c_body_repeat = array();
            $vin = trim($car->vin);
            if (mb_strlen($vin) > 5) {
                $short_vin = mb_substr($vin, -5, 5);
                $t_car = Car::select(['order_id', 'body_no', 'id', 'vin'])->where('body_no', 'like', '%' . $short_vin . '%')->where('id', '!=', $car->id)->get();
                $tca = array();
                foreach ($t_car as $nn => $tc) {
                    if($tc->vin) {
                        $tca[] = array('id' => $tc->id, 'vin' => $tc->vin, 'order_id' => $tc->order_id,);
                    }
                }
                $c_body_repeat[$vin] = $tca;
                unset($tca, $t_car);
            }

            $body_vin_repeat = array();
            $body = trim($car->body_no);
            if (mb_strlen($body) > 5) {
                $short_body = mb_substr($body, -5, 5);
                $t_car = Car::select(['order_id', 'body_no', 'id', 'vin'])->where('vin', 'like', '%' . $short_body . '%')->where('id', '!=', $car->id)->get();
                $tca = array();
                foreach ($t_car as $nn => $tc) {
                    if($tc->body_no) {
                        $tca[] = array('id' => $tc->id, 'body_no' => $tc->body_no, 'order_id' => $tc->order_id,);
                    }
                }
                $body_vin_repeat[$body] = $tca;
                unset($tca, $t_car);
            }

        return [
            'duplicates1' => $c_repeat,
            'duplicates2' => $c_body_repeat,
            'body_duplicates1' => $body_repeat,
            'chassis_duplicates1' => $chassis_repeat,
            'body_duplicates2' => $body_vin_repeat,
        ];
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
            $canCheckKap = false;
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
                    'kapCheckOrder' => $canCheckKap,
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

                        app(OrderApproveService::class)->storeHistory(new Request([
                            'action' => 'SIGN_ACTION',
                            'order_id' => $order->id,
                            'user_id' => $user->id,
                            'comment' => '#'.$user->title.'('. $user->role. ')'. ': подписал(а) заявку'
                        ]));
                    }
                    $response = ['success' => true];
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
            $order->status = 1;
            $order->sended_to_approve = time();
            $order->save();

            app(OrderApproveService::class)->storeHistory(new Request([
                'action' => 'SENDED_TO_MODERATOR',
                'order_id' => $order->id,
                'user_id' => $user->id,
            ]));
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

        if($order->approve === 1 && !$order->executor_uid) {
            $order->executor_uid = $user->id;
            $order->status = 2;
            $order->save();
            $success = true;

            app(OrderApproveService::class)->storeHistory(new Request([
                'action' => 'ORDER_STATUS_IN_PROCESSING',
                'order_id' => $order->id,
                'user_id' => $user->id,
                'comment' => '#'.$user->title.'('. $user->role. ')'. ': взял(а) заявку на исполнение'
            ]));
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
        if($order->approve === 1) {
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

                $filePath = '/order/files/'.$order->id.'/'.$original_name;
                $path = Storage::disk('local')->path($filePath);
                move_uploaded_file($_FILES['voice']['tmp_name'], $path);

                $file = new File();
                $file->order_id = $order->id;
                $file->car_id = $car->id;
                $file->file_type_id = $car->id;
                $file->file_type_id = 29;
                $file->client_id = $order->client_id;
                $file->extension = $extension[1];
                $file->original_name = $original_name;
                $file->created_at = time();
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
