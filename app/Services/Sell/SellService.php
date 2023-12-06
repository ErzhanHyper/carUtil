<?php


namespace App\Services\Sell;


use App\Http\Resources\SellResource;
use App\Models\Certificate;
use App\Models\Sell;
use App\Models\SellFile;
use App\Services\AuthService;
use App\Services\Certificate\CalcCertificatePriceService;
use Illuminate\Support\Facades\Validator;

class SellService
{
    public function getCollection()
    {
        $user = app(AuthService::class)->auth();
        $data = Sell::orderByDesc('created');

        if($user->role === 'dealer-light'){
            $data->where('user_id', $user->id);
        }

        if (isset($data)) {
            $paginate = 20;
            $pages = round($data->count() / $paginate);
            if ($pages == 0) {
                $pages = 1;
            }
            return [
                'pages' => $pages,
                'page' => $request->page ?? 1,
                'items' => SellResource::collection($data->orderByDesc('created')->paginate($paginate))
            ];
        }
    }

    public function get($id)
    {
        $data = Sell::find($id);
        if ($data) {
            return new SellResource($data);
        }
    }

    public function files($id)
    {
        $data = SellFile::where('sell_id', $id)->get();
        if ($data) {
            return [
                'items' => $data
            ];
        }
    }

    public function update($request, $id)
    {
        $can = true;

        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required',
            'vin' => 'required',
            'year' => 'required',
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            $message = 'Не заполнены объязательные поля!';
            $messages =  $validator->messages();
            $can = false;
        }

        $user = app(AuthService::class)->auth();
        $message = '';
        $success = false;

        $sell = Sell::find($id);
        if($sell->user_id != $user->id) {
            $can = false;
            $message = 'Действие запрещено. О попытке несанкционированного доступа будет осведомлена служба безопасности.';
        }

        $sell_check = Sell::where('vin', $request->vin)->first();
        if ($sell_check && $sell_check->id != $id) {
            $can = false;
            $message = 'Этот VIN уже используется в заявке на погашение #';
        }

        if($can) {
            if ($user->role === 'dealer-light' || $user->role === 'dealer-chief') {
                if ($sell) {
                    $sell->approve = 1;
                    $sell->sended_to_approve = time();
                    $cert = Certificate::find($sell->cert_1);
                    if($cert) {
                        $cert->blocked = 1;
                        $cert->save();
                    }
                    if ($sell->cert_2 != null && $sell->cert_2 > 0){
                        $cert2 = Certificate::find($sell->cert_2);
                        if($cert2) {
                            $cert2->blocked = 1;
                            $cert2->save();
                        }
                    }
                    $sell->vin = $request->vin;
                    $sell->year = $request->year;
                    $sell->subject = $request->vehicle_id;
                    $sell->phone = $request->phone;
                    $sell->save();
                    $success = true;
                }
            }
        }

        return [
            'message' => $message,
            'success' => $success
        ];
    }

    public function updateToSell($request, $id)
    {

        $message = 'Нет доступа';
        $success = false;

        $sell = Sell::find($id);

        if($sell) {
            $sell->approve = 4;
            $sell->save();
            $success = true;
            $message = 'Запрос на погашение отправлен';
        }

        return [
            'message' => $message,
            'success' => $success
        ];
    }

    public function create($request)
    {
        $message = '';
        $success = false;
        $data = [];

        $cert1 = "";
        $cert2 = "";
        $cert3 = "";
        $cert4 = "";

        $sum = 0;
        if($request->certs) {
            $certs = json_decode($request->certs);
            if (count($certs) > 0) {
                foreach ($certs as $key => $item) {
                    $date = date('d.m.Y', strtotime($item->cert_date));
                    $checkCert = $this->checkCert($key, $item->cert_num, $date, $item->cert_idnum);
                    if($checkCert['cert'] && $checkCert['success'] === true){
                        if ($key === 0) {
                            $cert1 = $checkCert['cert'];
                        }
                        if ($key === 1) {
                            $cert2 = $checkCert['cert'];
                        }
                        if ($key === 2) {
                            $cert3 = $checkCert['cert'];
                        }
                        if ($key === 3) {
                            $cert4 = $checkCert['cert'];
                        }
                        if ($checkCert['sum']) {
                            $sum = str_replace(" ", "", $checkCert['sum']['sum']);
                        }
                    }
                }
            }
        }

        if($cert1) {
            $user = app(AuthService::class)->auth();
            $sell = new Sell;
            $sell->created = time();
            $sell->user_id = $user->id;
            $sell->cert_1 = $cert1->id;
            $sell->sum = $sum;
            if($cert2) {
                $sell->cert_2 = $cert2->id;
                $cert2->blocked = 1;
                $cert2->save();
            }
            if($cert3) {
                $sell->cert_3 = $cert3->id;
                $cert3->blocked = 1;
                $cert3->save();
            }
            if($cert4) {
                $sell->cert_4 = $cert4->id;
                $cert4->blocked = 1;
                $cert4->save();
            }
            $sell->approve = 0;
            $sell->save();
            $data = $sell;
            $cert1->blocked = 1;
            $cert1->save();
            $success = true;
        }

        return [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];
    }

    private function checkCert($num, $cert_id, $cert_date, $cert_idnum){
        $success = false;
        $message = '';
        $sum = [];
        $cert = Certificate::find($cert_id);

        if($cert) {
            $new_date = strtotime($cert->date + (1705514400 - $cert->date));
            if (((strtotime('+1 year', $cert->date) + (36000)) > time()) || ($new_date > time())) {
                $dt_local = date('d.m.Y', $cert->date);
                $idnum_local = $cert->idnum_1;
                if ($dt_local == $cert_date && $idnum_local == $cert_idnum && $cert->blocked == 0) {
                    $sum = app(CalcCertificatePriceService::class)->run($cert->id);
                    $success = true;
                } else {
                    $message = 'Сертификат невозможно использовать.';
                }
            } else {
                $message = 'Сертификат - истек срок действия.';
            }
        }else{
            $message = 'Сертификат - не найден.';
        }

        return [
            'cert' => $cert,
            'sum' => $sum,
            'message' => $message,
            'success' => $success
        ];
    }

}
