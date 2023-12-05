<?php


namespace App\Services\Preorder;


use App\Http\Resources\PreOrderResource;
use App\Http\Resources\TransferOrderResource;
use App\Models\Car;
use App\Models\Client;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\TransferOrder;
use App\Services\AuthService;
use Carbon\CarbonPeriod;

class PreorderDataService
{
    protected AuthService $authService;
    protected PreorderService $preorderService;

    public function __construct(AuthService $authService, PreorderService $preorderService)
    {
        $this->authService = $authService;
        $this->preorderService = $preorderService;
    }

    public function getById($id): array
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

        $closedDate = strtotime(date('d.m.Y', $preorder->sended_dt) . ' + 15 days');
        $period = CarbonPeriod::create(date('d.m.Y', $preorder->sended_dt), date('Y-m-d', $closedDate));
        $bookingDates = [];

        foreach ($period as $date) {
            if(strtotime($date) > strtotime(date('d.m.Y'))) {
                $bookingDates[] = $date->format('Y/m/d');
            }
        }
        if ($can) {
            $data = [
                'item' => new PreOrderResource($preorder),
                'closedDays' => $this->preorderService->getClosedDays($preorder->id),
                'bookingDates' => $bookingDates,
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
        $car = Car::find($preorder->car_id);
        $canTransfer = false;
        $canSend = false;
        $canApprove = false;
        $blocked = true;
        $blockedCar = true;
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
                if ($order->status === 0 && !$transfer && $order->approve === 0 && $this->preorderService->checkOrderReviewDate($preorder->car_id)) {
                    $canTransfer = true;
                }
            }
        }
        if (($preorder->status === 0 || $preorder->status === 4)) {
            if ($user->role === 'liner') {
                $canSend = true;
                $blocked = false;
            }
        }

        if(!$car){
            $blockedCar = false;
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
            'blockedCar' => $blockedCar,
            'blockedBooking' => $blockedBooking
        ];
    }

    public function getCollection($request): array
    {
        $user = $this->authService->auth();
        if ($user->role === 'liner' || $user->role === 'moderator') {
            $preorder = PreOrderCar::orderByDesc('date');
            if($preorder) {
                if ($user->role === 'liner') {
                    $preorder->where('liner_id', $user->id);
                } else if ($user->role === 'moderator') {
                    $preorder->whereNot('status', 0);
                    $this->applyFilters($preorder, $request);
                }
            }

            $paginate = 20;
            $items = $preorder->paginate($paginate, ['*'], 'page', $request->page ?? 1);
            return [
                'pages' => $items->lastPage(),
                'page' => $items->currentPage(),
                'items' => PreOrderResource::collection($items),
            ];
        }

        return [];
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
}
