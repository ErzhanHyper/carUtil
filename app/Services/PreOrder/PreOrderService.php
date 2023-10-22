<?php


namespace App\Services\PreOrder;


use App\Http\Resources\PreOrderResource;
use App\Models\Car;
use App\Models\Client;
use App\Models\PreOrderCar;
use App\Services\AuthenticationService;
use App\Services\BookingOrder\BookingOrderService;
use App\Services\Car\CarService;
use App\Services\Client\ClientService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PreOrderService
{

    public function getCollection()
    {
        $user = app(AuthenticationService::class)->auth();

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
        $preorder = PreOrderCar::find($id);

        if ($preorder->status === 0 || $preorder->status === 4) {
            $client = Client::where('idnum', $request->client['idnum'])->first();
            $car = Car::where('vin', $request->car['vin'])->first();

            $car_find = Car::where('vin', $request->car['vin'])->where('order_id', '<>', '')->first();
            if ($car && $car_find) {
                if ($car->vin === $car_find->vin) {
                    throw new InvalidArgumentException(json_encode(['car' => ['ТС с таким VIN кодом уже привязан к другой заявке']]));
                }
            }
            if ($client) {
                $client = app(ClientService::class)->update($request->client, $client->id);
            } else {
                $client = app(ClientService::class)->create($request->client);
            }

            if ($car) {
                $car = app(CarService::class)->update($request->car, $car->id);
            } else {
                $car = app(CarService::class)->create($request->car);
            }


            if ($request->booking['factory_id'] && $car && $client) {
                $booking = app(BookingOrderService::class)->store(new Request([
                    'preorder_id' => $preorder->id,
                    'datetime' => $request->booking['datetime'],
                    'factory_id' => $request->booking['factory_id'],
                ]));

                $preorder->client_id = $client->id;
                $preorder->car_id = $car->id;
                $preorder->booking_id = $booking->id;
                $preorder->factory_id = $request->booking['factory_id'];
                $preorder->status = 1;
                $preorder->save();
            } else {
                throw new InvalidArgumentException(json_encode(['factory' => ['Выберите завод из списка']]));
            }
        }

        return response()->json(['message' => 'send']);
    }

    public function store($query)
    {
        $liner = app(AuthenticationService::class)->auth();

        $client = Client::where('idnum', $liner->idnum)->first();

        $response = [
            'status' => 401
        ];

        if ($query->recycle_type) {
            $preorder = new PreOrderCar;
            $preorder->status = 0;
            $preorder->liner_id = $liner->id;

            if ($client) {
                $preorder->client_id = $client->id;
            }
            $preorder->date = time();
            $preorder->recycle_type = $query->recycle_type;

            if ($preorder->save()) {
                $response = [
                    'status' => 200,
                    'data' => $preorder
                ];
            }
        }


        return $response;

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

        if($request->datetime && $request->factory_id) {
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
