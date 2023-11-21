<?php


namespace App\Services\Preorder;


use App\Http\Controllers\FileController;
use App\Http\Resources\PreOrderResource;
use App\Http\Resources\TransferOrderResource;
use App\Models\AgroFile;
use App\Models\Car;
use App\Models\CarFile;
use App\Models\Client;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\TransferOrder;
use App\Services\AuthService;
use App\Services\BookingOrder\BookingOrderService;
use App\Services\Car\CarService;
use App\Services\Client\ClientService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PreorderService
{

    public function getCollection($request)
    {
        $user = app(AuthService::class)->auth();

        if ($user->role === 'liner' || $user->role === 'moderator' ) {

            $orders = PreOrderCar::with(['car', 'client']);

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
                $car = Car::select(['id', 'vin'])->where('vin', 'like', '%' . $request->vin . '%')->get();
                $car_ids = [];
                if (count($car) > 0) {
                    foreach ($car as $c) {
                        $car_ids[] = $c->id;
                    }
                }
                $orders->whereIn('car_id', $car_ids);
            }

            if (isset($request->grnz) && $request->grnz != '') {
                $car = Car::select(['id', 'grnz'])->where('grnz', 'like', '%' . $request->grnz . '%')->get();
                $car_ids = [];
                if (count($car) > 0) {
                    foreach ($car as $c) {
                        $car_ids[] = $c->id;
                    }
                }
                $orders->whereIn('car_id', $car_ids);
            }

            if ($user->role === 'liner') {
                $orders->where('liner_id', $user->id);
            } else if ($user->role === 'moderator') {
                $orders->whereNotIn('status', [0,2]);
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
                    'items' => PreOrderResource::collection($orders->orderByDesc('date')->paginate($paginate))
                ];
            }
        }
    }

    public function getById($id)
    {
        $user = app(AuthService::class)->auth();
        if($user->role === 'liner' || $user->role === 'moderator') {
            $preorder = PreOrderCar::find($id);
            $order = Order::find($preorder->order_id);

            $canTransfer = false;
            $canSend = false;
            $canApprove = false;
            $blocked = true;
            $blockedBooking = true;

            $transfer = null;
            $transferResource = null;

            if ($order) {
                $transfer = TransferOrder::where('order_id', $order->id)->first();
                if($transfer) {
                    $transferResource = new TransferOrderResource(TransferOrder::where('order_id', $order->id)->first());
                }
                if($user->role === 'liner') {
                    if ($preorder->status === 2 && $order->status === 0 && $order->approve === 0) {
                        if($transfer && $transfer->closed !== 2){
                            $blockedBooking = true;
                        }else{
                            $blockedBooking = false;
                        }
                    }
                    if ($order->status === 0 && !$transfer && $order->approve === 0) {
                        $canTransfer = true;
                    }
                }
            }
            if($preorder->status === 0 || $preorder->status === 4){
                if($user->role === 'liner') {
                    $canSend = true;
                    $blocked = false;
                }
            }
            if($preorder->status === 1){
                if($user->role === 'moderator') {
                    $canApprove = true;
                }
            }


            return [
                'item' => new PreOrderResource($preorder),
                'transfer' => $transferResource,
                'permissions' => [
                    'transferOrder' => $canTransfer,
                    'sendToApprove' => $canSend,
                    'approveOrder' => $canApprove,
                    'blocked' => $blocked,
                    'blockedBooking' => $blockedBooking
                ]
            ];
        }
    }

    public function send($request, $id)
    {

        $success = false;
        $can = true;
        $message = '';

        $auth = app(AuthService::class)->auth();
        $preorder = PreOrderCar::find($id);
        $car = null;
        $car_find = null;
        $client = null;
        if ($preorder->status === 0 || $preorder->status === 4) {
            if ($auth->idnum !== $request->client['idnum']) {
                $message = 'ИИН не совпадает с учетными данными';
                $can = false;
            }

                if ($preorder->status === 0) {
                    $client = Client::where('idnum', $request->client['idnum'])->first();
                    if ($client) {
                        $car = Car::where('vin', $request->car['vin'])->first();
                        if ($car) {
                            $order = Order::find($car->order_id);
                            if ($order && $order->approve === 3) {
                                $message = 'ТС с таким VIN кодом уже был обработан';
                                $can = false;
                            }
                        }
                        if ($car) {
                            $preorderDuplicate = PreOrderCar::where('liner_id', $auth->id)->where('car_id', $car->id)->whereIn('status', [1,2])->first();
                            if ($preorderDuplicate) {
                                $date = date('d.m.Y', $preorderDuplicate->date);
                                $closedDate = strtotime($date . ' + 15 days');
                                if($closedDate >= time()){
                                    $diffDate = $closedDate - time();
                                    $closedDays = date('j', $diffDate);
                                }else{
                                    $closedDays = 0;
                                }
                                if($closedDays > 0) {
                                    $message = 'ТС с таким VIN кодом уже привязан к другой заявке';
                                    $can = false;
                                }else{
                                    $car_find = true;
                                }
                            }
                        }
                    }
                } else if ($preorder->status === 4) {
                    $client = Client::where('id', $preorder->client_id)->first();
                    $car = Car::where('id', $preorder->car_id)->first();
                }

                if($can){

                if($request->car) {
                    $car_request = new Request([
                        'vin' => $request->car['vin'],
                        'grnz' => $request->car['grnz'],
                        'category_id' => $request->car['category_id'],
                        'year' => $request->car['year'] ?? '',
                        'color' => $request->car['color'] ?? '',
                        'engine_no' => $request->car['engine_no'] ?? '',
                        'm_model' => $request->car['m_model'],
                        'body_no' => $request->car['body_no'] ?? '',
                        'chassis_no' => $request->car['chassis_no'] ?? '',
                        'weight' => $request->car['weight'] ?? '',
                        'doors_count' => $request->car['doors_count'] ?? '',
                        'wheels_count' => $request->car['wheels_count'] ?? '',
                        'wheels_protector_count' => $request->car['wheels_protector_count'] ?? '',
                        'proxy_num' => $request->car['proxy_num'] ?? '',
                        'proxy_date' => $request->car['proxy_date'] ?? '',
                        'cert_idnum' => $request->client['idnum'] ?? '',
                        'cert_title' => $request->client['title'] ?? '',
                        'owner_idnum' => $request->client['idnum'] ?? '',
                        'onwer_title' => $request->client['title'] ?? '',
                    ]);
                }



                if ($preorder->status === 0) {
                    $client = app(ClientService::class)->create($request->client);
                }else if($preorder->status === 4){
                    $client = Client::where('id', $preorder->client_id)->first();
                    $client = app(ClientService::class)->update($request->client, $client->id);
                }else{
                    $client = Client::where('id', $preorder->client_id)->first();
                }

                if($client) {
                    $preorder->client_id = $client->id;
                    $preorder->save();
                }

                if ($car && !$car_find) {
                    $car = app(CarService::class)->update($car_request, $car->id);
                } else {
                    $car = app(CarService::class)->create($car_request);
                }

                if ($car && $client) {
                    $preorder->client_id = $client->id;
                    $preorder->car_id = $car->id;
                    $preorder->status = 1;
                    $preorder->sended_dt = time();
                    $preorder->save();
                    $message = 'Отправлено на рассмотрение!';
                    $success = true;
                }
            }
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function store($query)
    {
        $liner = app(AuthService::class)->auth();
        $client1 = Client::where('idnum', $liner->idnum)->first();
        $client = null;
        if($client1) {
            $client = $client1->replicate();
            $client->push();
        }
        $recycleType = intval($query->recycle_type);

        if ($recycleType) {
            $preorder = new PreOrderCar;
            $preorder->status = 0;
            $preorder->date = time();
            $preorder->liner_id = $liner->id;
            $preorder->recycle_type = $recycleType;

            if ($client) {
                $preorder->client_id = $client->id;
            }

            $preorder->save();

            return $preorder;
        }
    }

    public function status($status_value)
    {
        return match ($status_value) {
            0 => 'Формирование заявки',
            1 => 'На рассмотрении у модератора',
            2 => 'Одобрена',
            3 => 'Отклонена',
            4 => 'Возвращена на доработку',
            default => '',
        };
    }

    public function delete($id)
    {
        $preorder = PreOrderCar::find($id);
        if ($preorder && ($preorder->status === 0 || $preorder->status === 4)) {
            $client = Client::find($preorder->client_id);
            $car = Car::find($preorder->car_id);
            $files = [];
            if($preorder->recycle_type === 1) {
                $files = CarFile::where('preorder_id', $preorder->id)->get();
            }else if($preorder->recycle_type === 2) {
                $files = AgroFile::where('preorder_id', $preorder->id)->get();
            }
            if(count($files) > 0) {
                foreach ($files as $file){
                    app(FileController::class)->deletePreOrderFile(new Request([
                        'preorder_id' => $preorder->id,
                        'file_id' => $file->id
                    ]));
                }
            }

            if($car) {
                $car->delete();
            }
            if($client) {
                $client->delete();
            }
            $preorder->delete();
            return [
                'message' => 'Заявка удалена',
                'success' => true,
            ];
        }
    }

    public function booking($request, $id)
    {
        $preorder = PreOrderCar::find($id);

        if ($request->datetime && $request->factory_id) {
            $booking = app(BookingOrderService::class)->store(new Request([
                'preorder_id' => $preorder->id,
                'datetime' => $request->datetime,
                'factory_id' => $request->factory_id,
            ]));

            $preorder->booking_id = $booking->id;
            $preorder->factory_id = $request->factory_id;
            $preorder->save();

        }
        return $preorder;
    }
}
