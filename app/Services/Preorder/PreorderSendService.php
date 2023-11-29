<?php


namespace App\Services\Preorder;


use App\Models\AgroFile;
use App\Models\Car;
use App\Models\CarFile;
use App\Models\Client;
use App\Models\FileType;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use App\Services\Car\CarService;
use App\Services\Client\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

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

    public function __construct(
        PreorderCommentService $history,
        AuthService $authService,
        ClientService $clientService,
        CarService $carService,
        PreorderService $preorderService,
    )
    {
        $this->message = '';
        $this->success = false;

        $this->history = $history;
        $this->authService = $authService;
        $this->clientService = $clientService;
        $this->carService = $carService;
        $this->preorderService = $preorderService;
    }

    //Отправка предзаявки на рассмотрение модератору
    public function send($request, int $id): array
    {
        $preorder = PreOrderCar::find($id);

        //Проверка на дубликаты и время
        if ($this->canSendPreorder($preorder, $request)) {

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

    //Проверка данных предзаявки на возможность отправки на рассмотрение
    private function canSendPreorder($preorder, $request): bool
    {
        $this->resetValidationState();
        $user = $this->authService->auth();

        if ($preorder) {
            //Проверка клиента на совпадение ИИН с учетной записью
            if ($preorder->status === config("constants.NEW_PREORDER") || $preorder->status === config("constants.RETURNED_BACK_PREORDER")) {
                if ($user->idnum !== $request->client['idnum']) {
                    $this->setMessage(Lang::get('messages.iin_credentials_1'));
                    return false;
                }
            }

            if ($preorder->status === config("constants.NEW_PREORDER")) {
                $car = Car::select(['id', 'order_id', 'vin'])->where('vin', $request->car['vin'])->get();

                if (count($car) > 0) {
                    foreach ($car as $item) {
                        //Проверка на одобренную заявку с текущим VIN
                        if ($this->preorderService->isOrderAlreadyApproved($item->order_id)) {
                            $this->setMessage(Lang::get('messages.vin_credentials_1'));
                            return false;
                        }

                        //Поиск дубликатов предзаявок
                        $preorderDuplicate = PreOrderCar::select(['id', 'liner_id', 'status', 'car_id'])->where('liner_id', $user->id)
                            ->where('car_id', $item->id)->whereIn('status', [1, 2])
                            ->first();

                        //Проверка на срок истечение дублирующих предзаявок
                        if ($preorderDuplicate && $this->preorderService->checkOrderReviewDate($item->id)) {
                            $this->setMessage(Lang::get('messages.vin_credentials_2'));
                            return false;
                        }
                    }
                }
            }

            if($preorder->recycle_type === 1){
                $file_type_ids = [1,2,8,9,10,11,12,13,14];
                $files = CarFile::select(['preorder_id', 'file_type_id'])->where('preorder_id', $preorder->id)->whereIn('file_type_id', $file_type_ids)->get();

                if(count($files) < 9) {
                    $names = '';
                    $file_types = FileType::whereIn('id', $file_type_ids)->get();
                    foreach ($file_types as $item){
                        $names .= $item->title .", ";
                    }
                    $this->setMessage(Lang::get('messages.file_credentials'));
                    return false;
                }
            }else if($preorder->recycle_type === 2){
                $file_type_ids = [1,4,5,6,7,8,9,10,11,12];
                $files = AgroFile::select(['preorder_id', 'file_type_id'])->where('preorder_id', $preorder->id)->whereIn('file_type_id', $file_type_ids)->get();
                if(count($files) < 10) {
                    $this->setMessage(Lang::get('messages.file_credentials'));
                    return false;
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

    //Проверка и заполнение данных о клиенте
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

    //Проверка и заполнение данных о ТС/СХТ
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

    //Сохранение данных предзаявки
    private function sendToModerator($preorder, $client, $car)
    {
        $preorder->client_id = $client->id;
        $preorder->car_id = $car->id;
        $preorder->status = config("constants.SENDED_PREORDER");
        $preorder->sended_dt = time();
        $preorder->save();

        $car->car_type_id = ($preorder->recycle_type === 1) ? 1 : 3;
        $car->save();

        $this->history->run(new Request([
            'status' => 'SEND_TO_MODERATOR',
        ]), $preorder->id);

        $this->setMessage(Lang::get('messages.sended_to_moderator'));
        $this->success = true;
    }
}
