<?php


namespace App\Services\Sell;


use App\Http\Resources\SellResource;
use App\Models\Car;
use App\Models\Certificate;
use App\Models\Log;
use App\Models\Sell;
use App\Services\AuthService;
use Illuminate\Http\Request;

class SellApproveService
{
    public function approve(Request $request, $id){
        $user = app(AuthService::class)->auth();
        $can = true;
        $message = 'Нет доступа';
        $success = false;

        $sell = Sell::find($id);

        if($sell->approve != 2) {
            $can = true;
        }

        $cert = Certificate::find($sell->cert_1);
        if($cert) {
            if($cert->closed == 1){
                $can = false;
                $message = "Перестаньте пытаться что-то сделать с сертификатом, который уже погашен! # CERT 1";
            }
        }

        if($sell->cert_2 != NULL || $sell->cert_2 != ''){
            $cert2 = Certificate::find($sell->cert_2);
            if($cert2) {
                if($cert2->closed == 1){
                    $can = false;
                    $message = "Перестаньте пытаться что-то сделать с сертификатом, который уже погашен! # CERT 2";
                }
            }
        }

        if($sell->cert_3 != NULL || $sell->cert_3 != ''){
            $cert3 = Certificate::find($sell->cert_3);
            if($cert3) {
                if($cert3->closed == 1){
                    $can = false;
                    $message = "Перестаньте пытаться что-то сделать с сертификатом, который уже погашен! # CERT 3";
                }
            }
        }

        if($sell->cert_4 != NULL || $sell->cert_4 != ''){
            $cert4 = Certificate::find($sell->cert_4);
            if($cert4) {
                if($cert4->closed == 1){
                    $can = false;
                    $message = "Перестаньте пытаться что-то сделать с сертификатом, который уже погашен! # CERT 4";
                }
            }
        }

        if($can) {
            $sell->approve = 2;
            $sell->approved = time();
            $sell->save();

            $success = true;
            $message = 'Сертификат погашен';
        }

        return [
            'message' => $message,
            'success' => $success
        ];
    }

    public function decline(Request $request, $id)
    {
        $message = 'Нет доступа';
        $success = false;

        $sell = Sell::find($id);

        $cert1 = $sell->cert_1;
        $cert2 = $sell->cert_2;
        $cert3 = $sell->cert_3;
        $cert4 = $sell->cert_4;
        $sell->cert_1 = null;
        $sell->cert_2 = null;
        $sell->cert_3 = null;
        $sell->cert_4 = null;
        $sell->vin = null;
        $sell->approve = 3;

        if ($sell->save()){
            $message = 'Запрос отклонен';
            $success = true;

            $cert = Certificate::find($cert1);
            if($cert) {
                $cert->blocked = 0;
                $cert->closed = 0;
                $cert->save();
            }

            $cert2 = Certificate::find($cert2);
            if($cert2) {
                $cert2->blocked = 0;
                $cert2->closed = 0;
                $cert2->save();
            }

            $cert3 = Certificate::find($cert3);
            if($cert3) {
                $cert3->blocked = 0;
                $cert3->closed = 0;
                $cert3->save();
            }

            $cert4 = Certificate::find($cert4);
            if($cert4) {
                $cert4->blocked = 0;
                $cert4->closed = 0;
                $cert4->save();
            }
        }

        return [
            'message' => $message,
            'success' => $success
        ];
    }

    public function message(Request $request, $id)
    {
        $user = app(AuthService::class)->auth();

        $message = 'Нет доступа';
        $success = false;

        $sell = Sell::find($id);

        if($sell) {
            $log = new Log();
            $log->event = 'msg';
            $log->object_type = 'sell';
            $log->object_id = $sell->id;
            $log->user_id = $user->id;
            $log->when = time();
            $log->object_before = $request->comment;
            $log->object_after = $request->comment;
            $log->__meta = $user->idnum . ' ' . $sell->approve . 'ID:' . $sell->id;
            $log->save();
            $success = true;
            $message = 'Комментарий добавлены';
        }

        return [
            'message' => $message,
            'success' => $success
        ];
    }

    public function close($request, $id)
    {
        $user = app(AuthService::class)->auth();

        $message = 'Нет доступа';
        $success = false;

        $sell = Sell::find($id);
        $can = true;

        $cert = Certificate::find($sell->cert_1);

        if($cert) {
            if($cert->closed == 1){
                $can = false;
                $message = "Перестаньте пытаться что-то сделать с сертификатом, который уже погашен! # CERT 1 - $cert->id";
            }
        }

        if($sell->cert_2 != NULL || $sell->cert_2 != ''){
            $cert2 = Certificate::find($sell->cert_2);
            if($cert2) {
                if($cert2->closed == 1){
                    $can = false;
                    $message = "Перестаньте пытаться что-то сделать с сертификатом, который уже погашен! # CERT 2 - $cert2->id";
                }
            }
        }

        if($sell->cert_3 != NULL || $sell->cert_3 != ''){
            $cert3 = Certificate::find($sell->cert_3);
            if($cert3) {
                $car3 = Car::where('id', $cert3->car_id)->first();
                if($car3->car_type_id == 3 || $car3->car_type_id == 4){
                    $can = true;
                }else{
                    if($cert3->closed == 1){
                        $can = false;
                        $message = "Перестаньте пытаться что-то делать с сертификатом, который уже погашен! # CERT 3 - $cert3->id";
                    }
                }
            }
        }

        if($sell->cert_4 != NULL || $sell->cert_4 != ''){
            $cert4 = Certificate::find($sell->cert_4);
            if($cert4) {
                $car4 = Car::where('id', $cert4->car_id)->first();
                if($car4->car_type_id == 3 || $car4->car_type_id == 4){
                    $can = true;
                }else{
                    if($cert4->closed == 1){
                        $can = false;
                        $message = "Перестаньте пытаться что-то делать с сертификатом, который уже погашен! # CERT 4 - $cert4->id";
                    }
                }
            }
        }


        if($can) {
            $cert->closed = 1;
            $cert->save();
            if ($sell->cert_2 != NULL || $sell->cert_2 != '') {
                $cert2 = Certificate::find($sell->cert_2);
                $cert2->closed = 1;
                $cert2->save();
            }

            if ($sell->cert_3 != NULL || $sell->cert_3 != '') {
                $cert3 = Certificate::find($sell->cert_3);
                $cert3->closed = 1;
                $cert3->save();
            }

            if ($sell->cert_4 != NULL || $sell->cert_4 != '') {
                $cert4 = Certificate::find($sell->cert_4);
                $cert4->closed = 1;
                $cert4->save();
            }

            $sell->approve = 5;
            $sell->closed = time();
            $sell->save();

            $success = true;
            $message = 'Сертификат погашен';
        }

        return [
            'message' => $message,
            'success' => $success
        ];
    }
}
