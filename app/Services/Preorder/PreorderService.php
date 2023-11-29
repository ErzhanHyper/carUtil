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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class PreorderService
{
    protected AuthService $authService;
    private bool $success;
    private string $message;

    public function __construct(
        AuthService $authService,
    )
    {
        $this->success = false;
        $this->message = '';

        $this->authService = $authService;
    }

    public function getCollection($request)
    {
        $user = $this->authService->auth();
        $data = [];

        if ($user->role === 'liner' || $user->role === 'moderator') {
            $preorder = PreOrderCar::with(['car', 'client']);

            if($preorder) {
                if ($user->role === 'liner') {
                    $preorder->where('liner_id', $user->id);
                } else if ($user->role === 'moderator') {
                    $preorder->whereNot('status', 0);
                    $this->applyFilters($preorder, $request);
                }
            }
        }

        if (isset($preorder)) {
            $paginate = 10;
            $pages = round($preorder->count() / $paginate);
            if ($pages == 0) {
                $pages = 1;
            }
            $data = [
                'pages' => $pages,
                'page' => $request->page ?? 1,
                'items' => PreOrderResource::collection($preorder->orderByDesc('date')->paginate($paginate))
            ];
        }
        return $data;
    }

    private function applyFilters($preorder, $request): void
    {
        foreach (['title', 'idnum'] as $filter) {
            if (isset($request->$filter) && $request->$filter != '') {
                $clientIds = Client::select(['id', 'title', 'idnum'])->where($filter, 'like', '%' . $request->$filter . '%')->pluck('id')->toArray();
                $preorder->whereIn('client_id', $clientIds);
            }
        }
        foreach (['vin', 'grnz'] as $filter) {
            if (isset($request->$filter) && $request->$filter != '') {
                $orderIds = Car::select(['vin', 'grnz', 'order_id'])->where($filter, 'like', '%' . $request->$filter . '%')->pluck('order_id')->toArray();
                $preorder->whereIn('order_id', $orderIds);
            }
        }
        foreach (['status'] as $filter) {
            if (isset($request->$filter) && $request->$filter != '') {
                $preorder->where('status', $request->$filter);
            }
        }
    }

    public function store($query)
    {
        $user = $this->authService->auth();
        $client1 = Client::where('idnum', $user->idnum)->first();

        $client = null;
        if ($client1) {
            $client = $client1->replicate();
            $client->push();
        }

        $recycleType = intval($query->recycle_type);

        if ($recycleType) {
            $preorder = new PreOrderCar([
                'status' => config("constants.NEW_PREORDER"),
                'date' => time(),
                'liner_id' => $user->id,
                'recycle_type' => $recycleType,
                'client_id' => $client ? $client->id : null,
            ]);
            $preorder->save();
            $this->success = true;
            $this->message = Lang::get('messages.preorder_created');
        }

        return [
            'data' => $preorder ?? null,
            'success' => $this->success,
            'message' => $this->message
        ];
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

    //Проверка заявки на одобрение
    public function isOrderAlreadyApproved($order_id): bool
    {
        $order = Order::find($order_id);
        return $order && $order->approve === config("constants.APPROVED_ORDER");
    }

    //Проверка на истечение срока рассмотрение заявки
    public function getById($id)
    {
        $user = app(AuthService::class)->auth();
        $preorder = PreOrderCar::find($id);

        $data = [];

        $can = false;

        if ($user->role === 'moderator') {
            $can = true;
        } else if ($user->role === 'liner') {
            if ($user->id === $preorder->liner_id) {
                $can = true;
            }
        }

        $transferResource = null;
        $order = Order::find($preorder->order_id);
        if($order) {
            $transfer = TransferOrder::where('order_id', $order->id)->first();
            if($transfer) {
                $transferResource = new TransferOrderResource($transfer);
            }
        }

        if ($can) {
            $data = [
                'item' => new PreOrderResource($preorder),
                'transfer' => $transferResource,
                'permissions' => $this->permission($preorder)
            ];
        }

        return $data;
    }

    //Предоставление доступа о действиях для фронта(для отображения кнопок)
    private function permission($preorder): array
    {
        $user = $this->authService->auth();
        $order = Order::find($preorder->order_id);

        $canTransfer = false;
        $canSend = false;
        $canApprove = false;
        $blocked = true;
        $blockedBooking = true;

        if ($order) {
            $transfer = TransferOrder::where('order_id', $order->id)->first();
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
            'transferOrder' => $canTransfer,
            'sendToApprove' => $canSend,
            'approveOrder' => $canApprove,
            'blocked' => $blocked,
            'blockedBooking' => $blockedBooking
        ];
    }

    public function checkOrderReviewDate($car_id): bool
    {
        $user = app(AuthService::class)->auth();
        $preorder = PreOrderCar::where('liner_id', $user->id)->where('car_id', $car_id)->whereIn('status', [1, 2])->first();

        if (!$preorder) {
            return false;
        }
        $closedDate = strtotime(date('d.m.Y', $preorder->date) . ' + 15 days');

        return $closedDate >= time();
    }
}
