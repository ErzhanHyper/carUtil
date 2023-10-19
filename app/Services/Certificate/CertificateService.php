<?php


namespace App\Services\Certificate;


use App\Http\Resources\CertificateResource;
use App\Models\Car;
use App\Models\Certificate;
use App\Models\Order;
use App\Services\AuthenticationService;
use Illuminate\Support\Facades\Request;

class CertificateService
{
    public function getCollection($request)
    {
        $user = app(AuthenticationService::class)->auth();

        if ($user && $user->role === 'liner') {
            $orders = Certificate::where('idnum_1', $user->idnum)
                ->orWhere('idnum_2', $user->idnum)
                ->orWhere('idnum_3', $user->idnum)
                ->orWhere('idnum_4', $user->idnum);
        } else {
            $orders = Certificate::class;
        }

        return CertificateResource::collection($orders->paginate(10));
    }

    public function storeCert($request)
    {
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        if ($order) {
            $car = $order->car;
            $cert = Certificate::where('car_id', $car->id)->first();
            if (!$cert) {
                $cert = new Certificate;
            }
            if ($cert->blocked == 0 || $cert->idnum_2 == '') {
                $cert->date = time();
                if ($car->cert_title) {
                    $cert->title_1 = $car->cert_title;
                } else {
                    $cert->title_1 = $order->client->title;
                }
                if ($car->cert_idnum) {
                    $cert->idnum_1 = $car->cert_idnum;
                } else {
                    $cert->idnum_1 = $order->client->idnum;
                }
                $cert->idnum_1 = $car->cert_idnum;
                $cert->date_1 = time();
                $cert->car_id = $car->id;
                if($cert->save()){
                    $order->status = 3;
                    $order->save();
                }
            }
        }
    }

    public function generateCert(Request $request){
        $car_id = $request->car_id;
        $car = Car::find($car_id);
        $cert = Certificate::where('car_id', $car_id)->find_one();
        $cat = $car->category->title;
        $car_type = $car->car_type;
    }
}
