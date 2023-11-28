<?php


namespace App\Services\Preorder;


use App\Models\Car;
use App\Models\Client;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use App\Services\Car\CarService;
use App\Services\Client\ClientService;
use Illuminate\Http\Request;

class PreorderSendService
{

    protected PreorderCommentService $history;
    protected AuthService $authService;
    protected ClientService $clientService;
    protected CarService $carService;
    protected PreorderService $preorderService;
    private string $message;
    /**
     * @var false
     */
    private bool $success;

    public function __construct(PreorderCommentService $history, AuthService $authService, ClientService $clientService, CarService $carService, PreorderService $preorderService)
    {
        $this->message = '';
        $this->success = false;
        $this->history = $history;
        $this->authService = $authService;
        $this->clientService = $clientService;
        $this->carService = $carService;
        $this->preorderService = $preorderService;
    }

    public function send($request, int $id): array
    {
        $preorder = PreOrderCar::find($id);

        if ($this->validatePreorder($preorder, $request)) {
            $client = $this->processClient($preorder, $request->client);
            $car = $this->processCar($preorder, $request->car);

            if ($client && $car) {
                $this->sendToModerator($preorder, $client, $car);
            }
        }

        return [
            'success' => $this->success,
            'message' => $this->message,
        ];
    }

    private function validatePreorder($preorder, $request): bool
    {
        $this->resetValidationState();
        $user = $this->authService->auth();

        if ($preorder) {
            if ($preorder->status === config("constants.NEW_PREORDER") || $preorder->status === config("constants.RETURNED_BACK_PREORDER")) {
                if ($user->idnum !== $request->client['idnum']) {
                    $this->setMessage('ИИН не совпадает с учетными данными');
                    return false;
                }
            }

            if ($preorder->status === config("constants.NEW_PREORDER")) {
                $car = Car::select(['id', 'order_id', 'vin'])->where('vin', $request->car['vin'])->get();

                if(count($car) > 0){
                    foreach ($car as $item) {
                        if ($this->checkApprovedOrder($item->order_id)) {
                            $this->setMessage('ТС с таким VIN кодом уже был обработан');
                            return false;
                        }

                        $preorderDuplicate = PreOrderCar::select(['id', 'liner_id', 'status', 'car_id'])->where('liner_id', $user->id)
                            ->where('car_id', $item->id)->whereIn('status', [1, 2])
                            ->first();

                        if ($preorderDuplicate && $this->preorderService->checkOrderReviewDate($item->id)) {
                            $this->setMessage('ТС с таким VIN кодом уже привязан к другой заявке');
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    private function resetValidationState()
    {
        $this->success = false;
        $this->message = '';
    }

    private function setMessage($message)
    {
        $this->message = $message;
    }

    private function checkApprovedOrder($order_id): bool
    {
        $order = Order::find($order_id);
        return $order && $order->approve === config("constants.APPROVED_ORDER");
    }

    private function processClient($preorder, $request)
    {
        $client = Client::find($preorder->client_id);

        if ($preorder->status === config("constants.NEW_PREORDER")) {
            if ($client) {
                return $this->clientService->update($request, $client->id);
            }
            return $this->clientService->create($request);
        } elseif ($preorder->status === config("constants.RETURNED_BACK_PREORDER")) {
            if ($client) {
                return $this->clientService->update($request, $client->id);
            }
        }

        return $client;
    }

    private function processCar($preorder, $request)
    {
        $car_request = new Request([
            'vin' => $request['vin'],
            'grnz' => $request['grnz'],
            'category_id' => $request['category_id'],
            'year' => $request['year'] ?? '',
            'color' => $request['color'] ?? '',
            'engine_no' => $request['engine_no'] ?? '',
            'm_model' => $request['m_model'],
            'body_no' => $request['body_no'] ?? '',
            'chassis_no' => $request['chassis_no'] ?? '',
            'weight' => $request['weight'] ?? '',
            'doors_count' => $request['doors_count'] ?? '',
            'wheels_count' => $request['wheels_count'] ?? '',
            'wheels_protector_count' => $request['wheels_protector_count'] ?? '',
            'proxy_num' => $request['proxy_num'] ?? '',
            'proxy_date' => $request['proxy_date'] ?? '',
            'cert_idnum' => $request['idnum'] ?? '',
            'cert_title' => $request['title'] ?? '',
            'owner_idnum' => $request['idnum'] ?? '',
            'onwer_title' => $request['title'] ?? '',
        ]);

        $car = Car::find($preorder->car_id);

        if ($preorder->status === config("constants.NEW_PREORDER")) {
            if ($car) {
                return $this->carService->update($car_request, $car->id);
            }
            return $this->carService->create($car_request);
        } else if ($preorder->status === config("constants.RETURNED_BACK_PREORDER")) {
            if ($car) {
                return $this->carService->update($car_request, $car->id);
            }
        }

        return $car;
    }

    private function sendToModerator($preorder, $client, $car)
    {
        $preorder->client_id = $client->id;
        $preorder->car_id = $car->id;
        $preorder->status = config("constants.SENDED_PREORDER");
        $preorder->sended_dt = time();
        $preorder->save();

        $this->history->run(new Request([
            'status' => 'SEND_TO_MODERATOR',
        ]), $preorder->id);

        $this->setMessage('Отправлено на рассмотрение!');
        $this->success = true;
    }
}
