<?php


namespace App\Services\PreOrder;


use App\Http\Resources\PreOrderResource;
use App\Models\Car;
use App\Models\Client;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use App\Services\BookingOrder\BookingOrderService;
use App\Services\Car\CarService;
use App\Services\Client\ClientService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PreOrderService
{

    public function getCollection()
    {
        $user = app(AuthService::class)->auth();

        $orders = PreOrderCar::with(['car', 'client']);
        if ($user) {
            if ($user->role === 'liner') {
                $orders->where('liner_id', $user->id);
            } else {
                if ($user->role === 'moderator') {
                    $orders->where('status', '<>', 0);
                }
            }
        }

        return PreOrderResource::collection($orders->orderByDesc('date')->paginate(15));
    }

    public function getById($id)
    {
        $preorder = PreOrderCar::find($id);

        return new PreOrderResource($preorder);
    }

    public function send($request, $id)
    {

        $auth = app(AuthService::class)->auth();
        $preorder = PreOrderCar::find($id);
        if ($preorder->status === 0 || $preorder->status === 4) {

            if ($auth->idnum !== $request->client['idnum']) {
                throw new InvalidArgumentException(json_encode(['car' => ['Неправильный ИИН']]));
            }

            if ($preorder->status === 0) {
                $client = Client::where('idnum', $request->client['idnum'])->first();
                $car = Car::where('vin', $request->car['vin'])->where('cert_idnum', $client->idnum)->first();
                if($car) {
                    $order = Order::find($car->order_id);
                    if ($order && $order->approve === 3) {
                        throw new InvalidArgumentException(json_encode(['car' => ['ТС с таким VIN кодом уже был обработан']]));
                    }
                }
                if($car && $client){
                    $preorderDuplicate = PreOrderCar::where('client_id', $client->id)->where('car_id', $car->id)->first();
                    if($preorderDuplicate){
                        throw new InvalidArgumentException(json_encode(['car' => ['ТС с таким VIN кодом уже привязан к другой заявке']]));
                    }
                }
            } else if ($preorder->status === 4) {
                $client = Client::where('id', $preorder->client_id)->first();
                $car = Car::where('id', $preorder->car_id)->first();
            }

            $car_request = new Request([
                'vin' => $request->car['vin'],
                'grnz' => $request->car['grnz'],
                'category_id' => $request->car['category_id'],
                'year' => $request->car['year'],
                'color' => $request->car['color'],
                'engine_no' => $request->car['engine_no'],
                'm_model' => $request->car['m_model'],
                'body_no' => $request->car['body_no'],
                'chassis_no' => $request->car['chassis_no'],
                'weight' => $request->car['weight'],
                'doors_count' => $request->car['doors_count'] ?? '',
                'wheels_count' => $request->car['wheels_count'] ?? '',
                'wheels_protector_count' => $request->car['wheels_protector_count'] ?? '',
                'proxy_num' => $request->car['proxy_num'] ?? '',
                'proxy_date' => $request->car['proxy_date'] ?? '',
                'cert_idnum' => $request->client['idnum'],
                'cert_title' => $request->client['title'],
                'owner_idnum' => $request->client['idnum'],
                'onwer_title' => $request->client['title'],
            ]);

//            $car_find = Car::where('vin', $request->car['vin'])->first();
//            if ($car && $car_find) {
//                if ($car->vin === $car_find->vin) {
//                    throw new InvalidArgumentException(json_encode(['car' => ['ТС с таким VIN кодом уже привязан к другой заявке']]));
//                }
//            }

            if ($client) {
                $client = app(ClientService::class)->update($request->client, $client->id);
            } else {
                $client = app(ClientService::class)->create($request->client);
            }

            if ($car) {
                $car = app(CarService::class)->update($car_request, $car->id);
            } else {
                $car = app(CarService::class)->create($car_request);
            }

            if ($car && $client) {
                $preorder->client_id = $client->id;
                $preorder->car_id = $car->id;
                $preorder->status = 1;
                $preorder->save();
            }
        }

        return response()->json(['message' => 'send']);
    }

    public function store($query)
    {
        $liner = app(AuthService::class)->auth();
        $client = Client::where('idnum', $liner->idnum)->first();

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

    public function delete($request)
    {
        $id = $request->preorder_id;
        if ($id) {
            $preorder = PreOrderCar::find($id);
            if ($preorder && $preorder->status === 0) {
                $preorder->delete();
            }
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
