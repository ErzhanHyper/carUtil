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
use Illuminate\Http\Request;

class PreorderService
{

    public function getCollection($request)
    {
        $user = app(AuthService::class)->auth();

        if ($user->role === 'liner' || $user->role === 'moderator') {

            $orders = PreOrderCar::with(['car', 'client']);

            if ($user->role === 'liner') {
                $orders->where('liner_id', $user->id);
            } else if ($user->role === 'moderator') {
                $orders->whereNot('status', 0);
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

                if (isset($request->status) && $request->status != '') {
                    $orders->where('status', $request->status);
                }
//                $orders->whereNotIn('status', [0,2]);
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
        $preorder = PreOrderCar::find($id);

        $can = false;

        if ($user->role === 'moderator') {
            $can = true;
        } else if ($user->role === 'liner') {
            if ($user->id === $preorder->liner_id) {
                $can = true;
            }
        }

        if ($can) {
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
                if ($transfer) {
                    $transferResource = new TransferOrderResource(TransferOrder::where('order_id', $order->id)->first());
                }
                if ($user->role === 'liner') {
                    if ($preorder->status === 2 && $order->status === 0 && $order->approve === 0) {
                        if ($transfer && $transfer->closed !== 2) {
                            $blockedBooking = true;
                        } else {
                            $blockedBooking = false;
                        }
                    }
                    if ($order->status === 0 && !$transfer && $order->approve === 0 && $this->checkOrderReviewDate($preorder->car_id)) {
                        $canTransfer = true;
                    }
                }
            }
            if ($preorder->status === 0 || $preorder->status === 4) {
                if ($user->role === 'liner') {
                    $canSend = true;
                    $blocked = false;
                }
            }
            if ($preorder->status === 1) {
                if ($user->role === 'moderator') {
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

    public function store($query)
    {
        $liner = app(AuthService::class)->auth();
        $client1 = Client::where('idnum', $liner->idnum)->first();
        $client = null;
        if ($client1) {
            $client = $client1->replicate();
            $client->push();
        }
        $recycleType = intval($query->recycle_type);

        if ($recycleType) {
            $preorder = new PreOrderCar;
            $preorder->status = config("constants.NEW_PREORDER");
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

    public function delete($id)
    {
        $preorder = PreOrderCar::find($id);
        if ($preorder && ($preorder->status === config("constants.NEW_PREORDER") || $preorder->status === config("constants.RETURNED_BACK_PREORDER"))) {
            $client = Client::find($preorder->client_id);
            $car = Car::find($preorder->car_id);
            $files = [];
            if ($preorder->recycle_type === 1) {
                $files = CarFile::where('preorder_id', $preorder->id)->get();
            } else if ($preorder->recycle_type === 2) {
                $files = AgroFile::where('preorder_id', $preorder->id)->get();
            }
            if (count($files) > 0) {
                foreach ($files as $file) {
                    app(FileController::class)->deletePreOrderFile(new Request([
                        'preorder_id' => $preorder->id,
                        'file_id' => $file->id
                    ]));
                }
            }

            if ($car) {
                $car->delete();
            }
            if ($client) {
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

    public function checkOrderReviewDate($car_id): bool
    {
        $user = app(AuthService::class)->auth();
        $preorder = PreOrderCar::where('liner_id', $user->id)->where('car_id', $car_id)->whereIn('status', [1, 2])->first();

        if (!$preorder) {
            return false;
        }

        $closedDate = strtotime(date('d.m.Y', $preorder->date) . ' + 15 days');

        if ($closedDate >= time()) {
            return true;
        }

        return false;
    }
}
