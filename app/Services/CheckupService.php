<?php


namespace App\Services;


use App\Http\Resources\CertificateResource;
use App\Http\Resources\ExchangeResource;
use App\Http\Resources\SellResource;
use App\Models\Car;
use App\Models\Certificate;
use App\Models\Exchange;
use App\Models\Order;
use App\Models\Sell;
use App\Services\Document\DocumentCertService;

class CheckupService
{

    public function downloadCertByOrderId($request, $id)
    {
        $order = Order::find($id);

        if ($order) {
            $car = Car::where('order_id', $order->id)->first();
            $cert = Certificate::where('car_id', $car->id)->first();
            if ($cert && $car) {
                return app(DocumentCertService::class)->generateCert($request, $cert->id);
            }
        }
    }

    public function checkCertById($id)
    {
        $ex2 = null;
        $ex3 = null;
        $ex4 = null;
        $sell = null;

        $cert = Certificate::find($id);

        if ($cert) {
            // а где же инфа про переоформления?
            if ($cert->title_2) {
                $ex2 = new ExchangeResource(Exchange::where('certificate_id', $cert->id)->first());
            }

            // если не пуст третий слот
            if ($cert->title_3) {
                $ex3 = new ExchangeResource(Exchange::where('certificate_id', $cert->id)->offset(1)->first());
            }

            // если не пуст четвертый слот
            if ($cert->title_4) {
                $ex4 = new ExchangeResource(Exchange::where('certificate_id', $cert->id)->offset(2)->first());
            }
            $sell = Sell::where('cert_1', $cert->id)->orWhere('cert_2', $cert->id)->first();
        }

        if ($cert) {
            $cert = new CertificateResource($cert);
        }

        if ($sell) {
            $sell = new SellResource($sell);
        }
        return [
            'sell' => $sell,
            'cert' => $cert,
            'ex2' => $ex2,
            'ex3' => $ex3,
            'ex4' => $ex4,
        ];
    }
}
