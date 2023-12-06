<?php


namespace App\Services\Order;

use App\Http\Resources\OrderResource;
use App\Models\Car;
use App\Models\Client;
use App\Models\File;
use App\Models\Order;
use App\Services\AuthService;

class OrderDataService
{
    public function getCollection($request): array
    {
        $user = app(AuthService::class)->auth();
        if ($user->role === 'moderator' || $user->role === 'operator') {
            $orders = Order::select(['id', 'client_id', 'created', 'sended_to_approve', 'user_id', 'executor_uid', 'approve', 'status', 'blocked'])->orderByDesc('created');

            if ($user->role === 'moderator') {
                $orders->where('approve', '<>', 0)
                    ->when($request->filled('type'), function ($query) use ($request) {
                        $query->with('car:id,car_type_id,order_id')->whereHas('car', function ($q) use ($request) {
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
            $paginate = 20;
            $items = $orders->paginate($paginate, ['*'], 'page', $request->page ?? 1);
            return [
                'pages' => $items->lastPage(),
                'page' => $items->currentPage(),
                'items' => OrderResource::collection($items),
            ];
        }
        return [];
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
                $orders->whereIn('approve', $request->$filter);
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

        return [];
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
                    if($order->executor_uid === $user->id) {
                        if ($video && $order->car->certificate == '') {
                            $canIssueCert = true;
                        }
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
