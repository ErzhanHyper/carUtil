<?php


namespace App\Services\Document;


use App\Models\Car;
use App\Models\Certificate;
use App\Models\Client;
use App\Models\Exchange;
use App\Models\Order;
use App\Services\AuthService;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentService
{

    public function generateStatement($id){
        $data = [];
        $order = Order::find($id);
        $car = Car::where('order_id', $order->id)->first();
        $client = Client::find($order->client_id);
        $data['client_name'] = $client->title;
        $data['client_address'] = $client->address;
        $data['client_phone'] = $client->phone;
        $data['client_email'] = $client->email;
        $data['client_cat'] = $car->category->title;

        $data['region'] = $client->region->title;
        $data['order_date'] = date("d.m.Y", $order->created);
        $data['order_id'] = $order->id;

        $data['car_cat'] = $car->car_type->title;
        $data['client_vin'] = $car->vin;

        if($car->proxy > 0){
            $data['owner_title'] = $car->owner_title;
            $data['owner_title_2'] = $car->owner_title;

            $data['proxy_num'] = $car->proxy_num;
            $data['proxy_date'] = $car->proxy_date;;
        }else{
            $data['owner_title'] = $car->cert_title;
            $data['owner_title_2'] = '';
            $data['proxy_num'] = '';
            $data['proxy_date'] = '';
        }

        $data['owner_id_1'] = '';
        $data['owner_id_2'] = '';
        $data['owner_id_3'] = '';

        if ($car->owner_type != 0){
            $owner_type = $car->owner_type;
            switch ($owner_type){
                case 6:
                    $data['owner_id_1'] = '✔';
                    break;
                case 7:
                    $data['owner_id_2'] = '✔';
                    break;
                case 8:
                    $data['owner_id_3'] = '✔';
                    break;
            }
        }

        $data['qr_client_sign'] = '________________';
        $data['qr_client_sign_info'] = '';

        $pdf = PDF::loadView('templates.statement', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);

        return $pdf->download('statement.pdf');
    }


    public function generateExchangeApplication($id)
    {
        $data = [];
        $exchange = Exchange::find($id);
        $cert = Certificate::find($exchange->certificate_id);

        $client = Client::where('idnum', $cert->idnum_1)->first();
        $email = '';
        if($client){
            $email = $client->email;
        }
        $data['cert_title'] = $cert->title_1;
        $data['cert_idnum'] = $cert->idnum_1;
        $data['cert_address'] = $exchange->cert_owner_address;
        $data['cert_phone'] = $exchange->phone;
        $data['cert_email'] = $email;
        $data['cert_page'] = $exchange->page;

        $data['ex_idnum'] = $exchange->idnum;
        $data['ex_title'] = $exchange->title;

        $data['owner_sign_time'] = date('d.m.Y H:i:s', $exchange->owner_sign_time);
        $data['receiver_sign_time'] = date('d.m.Y H:i:s', $exchange->receiver_sign_time);

        $data['receiver_sign'] = 'ПРОВЕРКА ПОДПИСИ ДОКУМЕНТА: ' . "\n" . mb_strtoupper(md5($exchange->receiver_sign));;
        $data['owner_sign'] = 'ПРОВЕРКА ПОДПИСИ ДОКУМЕНТА: ' . "\n" . mb_strtoupper(md5($exchange->owner_sign));
        $data['sign_info_qr'] = 'ДАННЫЕ ДЛЯ ПРОВЕРКИ ДОКУМЕНТА: ' . mb_strtoupper(md5($exchange->hash));

        $data['cert_date'] = date('d.m.Y', $cert->date);
        $data['cert_num'] = '№' . str_pad($cert->id, 9, 0, STR_PAD_LEFT);
        $data['current_date'] = date('d.m.Y');


        $_qr_edited1 = round((strlen($exchange->owner_sign) / 515) + 0.5);
        $qr_client1 = '';
        if($exchange->owner_sign) {
            for ($i = 0; $i < $_qr_edited1; $i++) {
                $ls = substr($exchange->owner_sign, $i * 515, 515);
                $png = QrCode::format('png')->color(60, 60, 60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($ls);
                $png = base64_encode($png);
                $qr_client1 .= "<img src='data:image/png;base64," . $png . "' width='115' height='115' style='margin: 0 1px;'>";
            }
        }
        $qr_client2 = '';
        $_qr_edited2 = round((strlen($exchange->receiver_sign) / 515) + 0.5);
        if($exchange->receiver_sign) {
            for ($i = 0; $i < $_qr_edited2; $i++) {
                $ls = substr($exchange->receiver_sign, $i * 515, 515);
                $png = QrCode::format('png')->color(60, 60, 60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($ls);
                $png = base64_encode($png);
                $qr_client2 .= "<img src='data:image/png;base64," . $png . "' width='115' height='115' style='margin: 0 1px;'>";
            }
        }

        $data['owner_sign_qr'] = $qr_client1;
        $data['receiver_sign_qr'] = $qr_client2;


        $pdf = PDF::loadView('templates.exchange_app', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);

        return $pdf->download('exchange_app.pdf');
    }


    public function generateContract($id)
    {
        $auth = app(AuthService::class)->auth();
        $data = [];
        $order = Order::find($id);
        $car = Car::where('order_id', $order->id)->first();
        $client = Client::find($order->client_id);

        $data['order_num'] = $order->id;
        $data['order_date'] = date("d.m.Y", $order->created);
        $data['car_num'] = $car->id;
        $data['car_vin'] = $car->id;
        $data['car_category'] = $car->category->title;
        $data['region'] = $client->region->title;
        $data['region_num'] = $client->region->id;
        $data['operator_name'] = $auth->title;
        $data['operator_name_for_docs'] = $auth->for_docs;
        $data['operator_base'] = $auth->base;
        $data['proxy'] = $client->proxy;
        $data['client_address'] = $client->address;
        $data['client_phone'] = $client->phone;
        $data['client_name'] = $client->title;
        $data['client_idnum'] = $client->idnum;
        $data['ud_num'] = $client->ud_num;
        $data['ud_expired'] = $client->ud_expired;
        $data['ud_issued'] = $client->ud_issued->title;
        $data['operator_company'] = 'АО «Жасыл Даму»';
        $data['operator_req'] = 'АО «Жасыл Даму»';

        if($client->client_type_id === 2 || $client->client_type_id === 3){
            $pdf = PDF::loadView('templates.contract_company', compact('data'));
            $pdf->setPaper('a4', 'portrait')->setWarnings(false);
            return $pdf->download('contract.pdf');
        }else{
            $pdf = PDF::loadView('templates.contract_person', compact('data'));
            $pdf->setPaper('a4', 'portrait')->setWarnings(false);
            return $pdf->download('contract.pdf');
        }
    }
}
