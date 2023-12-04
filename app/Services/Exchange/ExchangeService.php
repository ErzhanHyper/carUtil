<?php


namespace App\Services\Exchange;


use App\Http\Resources\ExchangeResource;
use App\Models\Certificate;
use App\Models\Exchange;
use App\Models\Log;
use App\Services\AuthService;
use App\Services\EdsService;
use App\Services\SignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExchangeService
{

    private function setData($data, object $object)
    {
        if (isset($data->created)) {
            $object->created = $data->created;
        }
        if (isset($data->certificate_id)) {
            $object->certificate_id = $data->certificate_id;
        }
        if (isset($data->title)) {
            $object->title = $data->title;
        }
        if (isset($data->idnum)) {
            $object->idnum = $data->idnum;
        }
        if (isset($data->approve)) {
            $object->approve = $data->approve;
        }
        if (isset($data->phone)) {
            $object->phone = $data->phone;
        }
        if (isset($data->page)) {
            $object->page = $data->page;
        }else{
            $object->page = 2;
        }
        if (isset($data->cert_owner_address)) {
            $object->cert_owner_address = $data->cert_owner_address;
        }
        if (isset($data->approved)) {
            $object->approved = $data->approved;
        }
        if (isset($data->sended_to_approve)) {
            $object->sended_to_approve = $data->sended_to_approve;
        }
        if (isset($data->owner_sign)) {
            $object->owner_sign = $data->owner_sign;
        }
        if (isset($data->owner_sign_time)) {
            $object->owner_sign_time = $data->owner_sign_time;
        }

        if (isset($data->receiver_sign)) {
            $object->receiver_sign = $data->receiver_sign;
        }
        if (isset($data->receiver_sign_time)) {
            $object->receiver_sign_time = $data->receiver_sign_time;
        }
        if (isset($data->hash)) {
            $object->hash = $data->hash;
        }
        $object->save();

    }

    public function getCollection($request)
    {
        $auth = app(AuthService::class)->auth();

        if ($auth->role === 'liner') {
            $exchange = Exchange::where('idnum', $auth->idnum)->whereNotNull('owner_sign')
                ->whereIn('approve', [0, 1, 2, 3]);
        } else if ($auth->role === 'moderator') {
            $exchange = Exchange::whereIn('approve', [1,2,3]);
        }

        if (isset($exchange)) {
            $paginate = 15;
            $pages = round($exchange->count() / $paginate);
            if ($pages == 0) {
                $pages = 1;
            }
            return [
                'pages' => $pages,
                'page' => $request->page ?? 1,
                'items' => ExchangeResource::collection($exchange->orderByDesc('created')->paginate($paginate))
            ];
        }
    }

    public function getById($id)
    {
        $exchange = Exchange::find($id);
        if ($exchange) {
            return new ExchangeResource($exchange);
        }
    }

    public function create($request)
    {
        $auth = app(AuthService::class)->auth();

        $cert_id = $request->certificateId;
        $validator = Validator::make($request->all(), [
            'certificateId' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['messages' => $validator->messages()]);
        }

        $cert = Certificate::find($cert_id);

        $success = false;
        $message = 'Нет доступа';
        $data = [];

        if ($cert->idnum_1 === $auth->idnum) {

            $new_date = 1705514400 - $cert->date;
            if ($cert->date >= 1610992800 && $cert->date <= 1642442400 && $cert->closed == 0) {
                $dt_check = strtotime($cert->date + $new_date);
            } else {
                $dt_check = (strtotime(' + 1 year', $cert->date) + 36000);
            }

            if ($cert && $cert->blocked === 0 && $cert->title_4 == '' && $cert->idnum_4 == '' && $dt_check > time()) {
                $cert->blocked = 1;
                if($cert->save()){
                    $exchange = $this->store($cert_id);

                    if($exchange) {
                        $success = true;
                        $message = 'Передача была успешно инициирована!';
                        $data = $exchange;

                        Log::create([
                            'event' => 'create',
                            'object_type' => 'exchange',
                            'object_id' => $exchange->id,
                            'when' => time()
                        ]);
                    }
                }

            } else {
                $message = 'Cертификат невозможно переоформить!';
            }
        }

        return [
            'data' => $data,
            'success' => $success,
            'message' => $message
        ];
    }

    public function update($request, $id)
    {
        $auth = app(AuthService::class)->auth();
        $exchange = Exchange::find($id);
        $cert = Certificate::find($exchange->certificate_id);
        $userType = 'owner';
        $message = 'Нет доступа';
        $messages = [];

        $success = false;
        $can = true;

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'idnum' => 'required',
            'phone' => 'required',
            'sign' => 'required',
            'cert_owner_address' => 'required',
        ]);
        if ($validator->fails()) {
            $message = 'Не заполнены объязательные поля!';
            $messages =  $validator->messages();
            $can = false;
        }

        if($can) {
            $log = Log::create([
                'event' => 'sign',
                'object_type' => 'exchange',
                'object_id' => $exchange->id,
                'when' => time(),
                'object_before' => json_encode($exchange),
            ]);

            if ($exchange) {
                $canSign = false;
                if ($exchange->approve === 0) {
                    if ($cert->idnum_1 === $auth->idnum) {
                        $canSign = true;
                    } else {
                        if ($exchange->idnum === $auth->idnum) {
                            $userType = 'receiver';
                            $canSign = true;
                        }
                    }
                }
                if ($userType === 'owner' && $canSign) {
                    if ($request->idnum === $auth->idnum) {
                        return [
                            'success' => $success,
                            'message' => 'ИИН не должен совпадать с вашим',
                        ];
                    }
                    $signedData = $this->signOwner($request, $id);
                    $message = $signedData['message'];
                    if ($signedData['success'] === true) {
                        $success = true;
                        $exchangeAfter = Exchange::find($id);
                        $log->object_after = json_encode($exchangeAfter);
                        $log->save();
                    }
                } else if ($userType === 'receiver' && $canSign) {
                    $signedData = $this->signReceiver($request, $id);
                    $message = $signedData['message'];

                    if ($signedData['success'] === true) {
                        $this->sendToApprove($id);
                        $success = true;
                        $exchangeAfter = Exchange::find($id);
                        $log->object_after = json_encode($exchangeAfter);
                        $log->save();
                    }
                } else {
                    $message = 'Ошибка подписи!';
                }
            }
        }

        return [
            'success' => $success,
            'message' => $message,
            'messages' => $messages
        ];
    }

    private function signOwner($request, $id)
    {
        $auth = app(AuthService::class)->auth();

        $exchange = Exchange::find($id);
        $hash = app(SignService::class)->__signExchangeData($exchange->id);
        $signData = app(EdsService::class)->check(new Request(['hash' => $hash, 'sign' => $request->sign]));

//        if($signData && ($signData->iin === $auth->idnum || $signData->bin === $auth->idnum)) {
            $this->setData(new Request([
                'title' => $request->title,
                'idnum' => $request->idnum,
                'phone' => $request->phone,
                'page' => $request->page,
                'hash' => $hash,
                'owner_sign' => $request->sign,
                'owner_sign_time' => time(),
                'cert_owner_address' => $request->cert_owner_address,
            ]), $exchange);
            return ['message' => 'Успешно подписано!',  'success' => true];
//        }
//        return ['message' => 'Ошибка',  'success' => false];
    }

    private function signReceiver($request, $id)
    {
        $auth = app(AuthService::class)->auth();

        $validator = Validator::make($request->all(), [
            'sign' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['messages' => $validator->messages()]);
        }
        $exchange = Exchange::find($id);
        $hash = app(SignService::class)->__signExchangeData($exchange->id);
        $signData = app(EdsService::class)->check(new Request(['hash' => $hash, 'sign' => $request->sign]));

//        if($signData && $signData->iin === $auth->idnum) {
            $this->setData(new Request([
                'receiver_sign' => $request->sign,
                'receiver_sign_time' => time(),
                'sended_to_approve' => time(),
            ]), $exchange);
            return ['message' => 'Успешно подписано!',  'success' => true];
//        }

//        return ['message' => 'Ошибка',  'success' => false];
    }

    private function sendToApprove($id)
    {
        $exchange = Exchange::find($id);
        if($exchange->owner_sign != '' && $exchange->receiver_sign != '') {
            $this->setData(new Request([
                'approve' => 1,
            ]), $exchange);
        }
    }

    public function store($id)
    {
        $data = new Request([
            'certificate_id' => $id,
            'created' => time(),
            'approve' => 0
        ]);

        $exchange = new Exchange();
        $this->setData($data, $exchange);

        return $exchange;
    }

    public function delete($id)
    {
        $exchange = Exchange::find($id);
        if($exchange && ($exchange->approve === 0 || $exchange->approve === 1)) {
            $cert = Certificate::find($exchange->certificate_id);
            $cert->blocked = 0;
            if($cert->save()) {
                $exchange->delete();
                return ['message' => 'Успешно удалено!', 'success' => true];
            }
        }else{
            return ['message' => 'Нет доступа!',  'success' => false];
        }
    }
}
