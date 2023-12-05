<?php


namespace App\Services\Certificate;


use App\Http\Resources\CertificateResource;
use App\Models\Car;
use App\Models\Certificate;
use App\Models\Order;
use App\Services\AuthService;
use App\Services\Document\DocumentCertService;
use App\Services\EdsService;
use Barryvdh\DomPDF\Facade\Pdf;
use InvalidArgumentException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateService
{
    public function getCollection($request)
    {
        $user = app(AuthService::class)->auth();

        if ($user && $user->role === 'liner') {
            $orders = Certificate::where('idnum_1', $user->idnum);
        } else {
            $orders = Certificate::class;
        }

        return CertificateResource::collection($orders->paginate(1000));
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
                $cert->date_1 = time();
                $cert->car_id = $car->id;
                if($cert->save()){
                    $order->status = 3;
                    $order->save();
                }
            }
        }
    }

    public function downloadCertByOrderId($request, $id)
    {
        $order = Order::find($id);

        if($order) {
            $car = Car::where('order_id', $order->id)->first();
            $cert = Certificate::where('car_id', $car->id)->first();
            if ($cert && $car) {
                return app(DocumentCertService::class)->generateCert($request, $cert->id);
            }
        }
    }
}
