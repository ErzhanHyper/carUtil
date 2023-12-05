<?php


namespace App\Services\Preorder;


use App\Models\Car;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class PreorderApproveService
{

    protected AuthService $authService;
    protected PreorderService $preorderService;
    protected OrderService $orderService;

    private string $message;
    private bool $success;

    public function __construct(
        AuthService $authService,
        PreorderService $preorderService,
        OrderService $orderService
    )
    {
        $this->message = '';
        $this->success = false;
        $this->authService = $authService;
        $this->preorderService = $preorderService;
        $this->orderService = $orderService;
    }

    //Отклонение предзаявки
    public function decline($request, $id)
    {
        $preorder = PreOrderCar::find($id);
        $commentText = $request->comment;

        $preorder->status = config("constants.DECLINED_PREORDER");
        $preorder->save();

        $this->preorderService->comment(new Request([
            'status' => 'DECLINED',
            'comment' => $commentText
        ]), $preorder->id);

        return ['decline'];
    }

    //Возвращение на доработку клиенту
    public function revision($request, $id)
    {
        $preorder = PreOrderCar::find($id);
        $commentText = $request->comment;

        $preorder->status = config("constants.RETURNED_BACK_PREORDER");
        $preorder->save();

        $this->preorderService->comment(new Request([
            'status' => 'RETURNED_BACK_LINER',
            'comment' => $commentText
        ]), $preorder->id);

        return ['revision'];
    }

    //Одобрение заявки модератором
    public function approve($request, int $id)
    {
        $preorder = PreOrderCar::find($id);
        $car = Car::find($preorder->car_id);

        if ($this->canApprovePreorder($preorder)) {
            $preorder->status = config("constants.APPROVED_PREORDER");

            try {
                $order = $this->orderService->store(new Request([
                    'client_id' => $preorder->client_id
                ]));

                if ($order && $car) {
                    $car->order_id = $order->id;
                    if ($car->save()) {
                        $preorder->order_id = $order->id;
                        $preorder->save();
                        $this->preorderService->comment(new Request([
                            'status' => 'APPROVED',
                        ]), $preorder->id);

                        $this->success = true;
                        $this->message = Lang::get('messages.preorder_approved');
                    }
                }
            } catch (Exception $e) {
                // Обработка исключений при сохранении заказа
            }
        }

        return [
            'success' => $this->success,
            'message' => $this->message
        ];
    }

    //Проверка на возможность одобрение
    public function canApprovePreorder($preorder): bool
    {
        if ($this->isPreorderAlreadyApproved($preorder)) {
            $this->message = Lang::get('messages.preorder_credentials_1');
            return false;
        }

        $car = Car::find($preorder->car_id);
        if ($car && $this->findCarDuplicates($car)) {
            $this->message = Lang::get('messages.vin_credentials_1');
            return false;
        }

        return true;
    }

    //Проверка предзаявки на одобренность
    public function isPreorderAlreadyApproved($preorder): bool
    {
        return $preorder->status === config("constants.APPROVED_PREORDER");
    }

    //Проверка заявки на существование и одобренность
    public function findCarDuplicates($car): bool
    {
        $cars = Car::where('vin', $car->vin)->get();

        foreach ($cars as $item) {
            return $this->preorderService->isOrderAlreadyApproved($item->order_id);
        }

        return false;
    }
}
