<?php


namespace App\Services\Preorder;


use App\Models\AgroFile;
use App\Models\Car;
use App\Models\CarFile;
use App\Models\Client;
use App\Models\FileType;
use App\Models\FileTypeAgro;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use App\Services\Car\CarService;
use App\Services\Client\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;

class PreorderSendService
{
    protected AuthService $authService;
    protected ClientService $clientService;
    protected CarService $carService;
    protected PreorderService $preorderService;
    private string $message;
    private bool $success;

    public function __construct(AuthService $authService,ClientService $clientService, CarService $carService, PreorderService $preorderService)
    {
        $this->message = '';
        $this->success = false;

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
            $file = $this->processFile($preorder);

            if ($car) {
                $preorder->car_id = $car->id;
                $preorder->save();
            }

            if ($client && $car && $file) {
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
                    throw new InvalidArgumentException(json_encode(['vin' => [Lang::get('messages.iin_credentials_1')]]));
                }
            }

            if ($preorder->status === config("constants.NEW_PREORDER")) {
                $car = Car::select(['id', 'order_id', 'vin'])->where('vin', $request->car['vin'])->get();

                if (count($car) > 0) {
                    foreach ($car as $item) {
                        //Проверка на одобренную заявку с текущим VIN
                        if ($this->preorderService->isOrderAlreadyApproved($item->order_id)) {
                            throw new InvalidArgumentException(json_encode(['vin' => [Lang::get('messages.vin_credentials_1')]]));
                        }

                        //Поиск дубликатов предзаявок
                        $preorderDuplicate = PreOrderCar::select(['id', 'liner_id', 'status', 'car_id'])->where('liner_id', $user->id)
                            ->where('car_id', $item->id)->whereIn('status', [1, 2])
                            ->first();

                        //Проверка на срок истечение дублирующих предзаявок
                        if ($preorderDuplicate && $this->preorderService->checkOrderReviewDate($item->id)) {
                            throw new InvalidArgumentException(json_encode(['vin' => [Lang::get('messages.vin_credentials_2')]]));
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

    //Проверка и заполнение данных о клиенте
    private function processClient($preorder, $request)
    {
        $client = Client::find($preorder->client_id);

        if ($preorder->status === config("constants.NEW_PREORDER")) {
            if ($client) {
                return $this->clientService->update($request, $client->id);
            }else{
                $client1 = $this->clientService->create($request);
                if ($client1) {
                    $preorder->client_id = $client1->id;
                    $preorder->save();
                }
                return $client1;
            }
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

    private function processFile($preorder)
    {
        if ($preorder->recycle_type === 1) {
            $file_type_ids = [1, 2, 8, 9, 10, 11, 12, 13, 14];
            $count = 9;
            $files = CarFile::select(['preorder_id', 'file_type_id'])->where('preorder_id', $preorder->id)->whereIn('file_type_id', $file_type_ids)->get();
            $file_types = FileType::whereIn('id', $file_type_ids)->orderBy('weight');

        } else {
            $count = 10;
            $file_type_ids = [1, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            $files = AgroFile::select(['preorder_id', 'file_type_id'])->where('preorder_id', $preorder->id)->whereIn('file_type_id', $file_type_ids)->get();
            $file_types = FileTypeAgro::whereIn('id', $file_type_ids);
        }

        $required_ids = [];
        if (count($files) > 0) {
            foreach ($files as $file) {
                $required_ids[] = $file->file_type_id;
            }
        }
        $file_types = $file_types->whereNotIn('id', $required_ids)->get();

        if(count($file_types) > 0) {
            if (count($file_types) <= $count) {
                $names = [Lang::get('messages.file_credentials')];
                foreach ($file_types as $item) {
                    $names[] = [$item->title];
                }
                $this->setMessage(json_encode($names));
                return false;
            }
        }

        return true;
    }

    //Сохранение данных предзаявки
    private function sendToModerator($preorder, $client, $car)
    {
        $preorder->status = config("constants.SENDED_PREORDER");
        $preorder->sended_dt = time();
        $preorder->save();

        $car->car_type_id = ($preorder->recycle_type === 1) ? 1 : 3;
        $car->save();

        $this->preorderService->comment(new Request([
            'status' => 'SEND_TO_MODERATOR',
        ]), $preorder->id);

        $this->setMessage(Lang::get('messages.sended_to_moderator'));
        $this->success = true;
    }
}
