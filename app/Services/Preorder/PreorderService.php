<?php


namespace App\Services\Preorder;

use App\Http\Controllers\FileController;
use App\Models\AgroFile;
use App\Models\Car;
use App\Models\CarFile;
use App\Models\Client;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\PreorderComment;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class PreorderService
{
    protected AuthService $authService;
    private bool $success;
    private string $message;

    public function __construct(AuthService $authService)
    {
        $this->success = false;
        $this->message = '';

        $this->authService = $authService;
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
    public function checkOrderReviewDate($car_id): bool
    {
        $user = app(AuthService::class)->auth();
        $preorder = PreOrderCar::select(['liner_id', 'car_id', 'status', 'sended_dt'])->where('liner_id', $user->id)->where('car_id', $car_id)->whereIn('status', [1, 2])->first();

        if (!$preorder) {
            return false;
        }
        $closedDate = strtotime(date('d.m.Y', $preorder->sended_dt) . ' + 15 days');

        return $closedDate >= time();
    }

    public function getClosedDays($preorder_id): int
    {
        $preorder = PreOrderCar::find($preorder_id);
        $closedDays = 0;

        if ($preorder) {
            $datetime = date('d.m.Y', $preorder->sended_dt);
            $closedDate = strtotime($datetime . ' + 15 days');
            if ($closedDate >= time()) {
                $diffDate = $closedDate - time();
                $closedDays = (int)date('j', $diffDate);
            }
        }
        return $closedDays;
    }

    public function comment($request, $id)
    {
        $user = $this->authService->auth();
        $user_id = null;
        $liner_id = null;
        if ($user->role === 'liner') {
            $liner_id = $user->id;
        }
        if ($user->role === 'moderator') {
            $user_id = $user->id;
        }
        $comment = new PreorderComment;
        $comment->preorder_id = $id;
        $comment->comment = $request->comment;
        $comment->action = $request->status;
        $comment->created_at = time();
        $comment->user_id = $user_id;
        $comment->liner_id = $liner_id;
        $comment->save();
    }

    public function status($id): array
    {
        $globalStatus = [];
        $preorder = PreOrderCar::find($id);

        if ($preorder->status === 0) {
            $globalStatus['title'] = 'Формирование заявки';
            $globalStatus['id'] = 0;
        }else if ($preorder->status === 1 && !$preorder->order && !$preorder->booking) {
            $globalStatus['title'] = 'На рассмотрении';
            $globalStatus['id'] = 1;
        }else if ($preorder->status === 2 && $preorder->order->status === 0 && !$preorder->booking && $preorder->order) {
            $globalStatus['title'] = 'Одобрена';
            $globalStatus['id'] = 2;
        }else if ($preorder->status === 3) {
            $globalStatus['title'] = 'Отклонена';
            $globalStatus['id'] = 3;
        }else if ($preorder->status === 4) {
            $globalStatus['title'] = 'Возвращена на доработку';
            $globalStatus['id'] = 4;
        }else if ($preorder->status === 2 && $preorder->order && $preorder->order->status === 0 && $preorder->booking) {
            $globalStatus['title'] = 'На бронировании';
            $globalStatus['id'] = 5;
        }else if ($preorder->status === 2 && $preorder->order && $preorder->booking && ($preorder->order->status === 1 || $preorder->order->status === 2 || $preorder->order->status === 4)) {
            $globalStatus['title'] = 'На рассмотрении у менеджера завода';
            $globalStatus['id'] = 6;
        }else if ($preorder->status === 2 && $preorder->booking && $preorder->order->status === 5) {
            $globalStatus['title'] = 'На выдаче сертификата';
            $globalStatus['id'] = 7;
        }else if ($preorder->status === 2 && $preorder->booking && $preorder->order->status === 3) {
            $globalStatus['title'] = 'Завершено';
            $globalStatus['id'] = 8;
        }

       return $globalStatus;
    }
}
