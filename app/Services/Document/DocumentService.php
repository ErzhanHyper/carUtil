<?php


namespace App\Services\Document;


use App\Models\Car;
use App\Models\Certificate;
use App\Models\Client;
use App\Models\Exchange;
use App\Models\Factory;
use App\Models\KapRequest;
use App\Models\Order;
use App\Services\AuthService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SimpleXMLElement;

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

        $data['owner_sign_time'] = $exchange->owner_sign_time ? date('d.m.Y H:i:s', $exchange->owner_sign_time) : '';
        $data['receiver_sign_time'] = $exchange->receiver_sign_time ? date('d.m.Y H:i:s', $exchange->receiver_sign_time) : '';

        $data['receiver_sign'] = $exchange->receiver_sign ? 'ПРОВЕРКА ПОДПИСИ ДОКУМЕНТА: ' . "\n" . mb_strtoupper(md5($exchange->receiver_sign)) : '';
        $data['owner_sign'] = $exchange->owner_sign ? 'ПРОВЕРКА ПОДПИСИ ДОКУМЕНТА: ' . "\n" . mb_strtoupper(md5($exchange->owner_sign)) : '';
        $data['sign_info_qr'] = $exchange->hash ? 'ДАННЫЕ ДЛЯ ПРОВЕРКИ ДОКУМЕНТА: ' . mb_strtoupper(md5($exchange->hash)) : '';

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
        $data['car_vin'] = $car->vin;
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
        $data['operator_company'] = '';
        $data['operator_req'] = '';

        $factory = Factory::find($auth->factory_id);
        $data['factory_name'] = $factory->title;

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


    public function generateComplect($request, $id)
    {
        $auth = app(AuthService::class)->auth();
        $data = [];
        $order = Order::find($id);
        $car = Car::where('order_id', $order->id)->first();
        $client = Client::find($order->client_id);

        $data['order_num'] = $order->id;
        $data['order_date'] = date("d.m.Y", $order->created);
        $data['car_num'] = $car->id;
        $data['car_vin'] = $car->vin;
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
        $data['operator_company'] = '';
        $data['operator_req'] = '';

        $factory = Factory::find($auth->factory_id);
        $data['factory_name'] = $factory->title ?? '';

        $data['doors_count'] = $car->doors_count;
        $data['weight'] = $car->weight;
        $data['wheels_count'] = $car->wheels_count;
        $data['wheels_protector_count'] = $car->wheels_protector_count;
        $data['percent_of_original_weight'] = '';
        $data['vssht_1_cert_sum'] = '';
        $data['vssht_2_cert_sum'] = '';

        if ($request->complect){
//            foreach ($request->complect as $i){
//                $data['VL_'.$i] = '✔';
//            }
//            $k = 1;
//            for ($k; $k < 13; $k++){
//                if($request->complect['id'.$k] == 'true'){
//                    $data['VL_'.$k] = '✔';
//                }else{
//                    $data['VL_' . $k] = '';
//                }
//            }
            if(count($request->complect) > 0) {
                foreach ($request->complect as $key => $complect) {
                    $k = $key +1 ;
                    if($complect['value'] === true || $complect['value'] ==='true') {
                        $data['VL_' . $k] = '✔';
                    }else{
                        $data['VL_' . $k] = '';
                    }
                }
            }

        }

        if($car->category->title == "M1") {
            $pdf = PDF::loadView('templates.annex_act_cert_m1', compact('data'));
        } else if($car->category->title == "M2" || $car->category->title == "M3") {
            $pdf = PDF::loadView('templates.annex_act_cert_m2m3', compact('data'));
        } else if($car->category->title == "tractor" ) {
            $pdf = PDF::loadView('templates.annex_act_cert_tractor', compact('data'));
        } else if($car->category->title == "combain" ) {
            $pdf = PDF::loadView('templates.annex_act_cert_combain', compact('data'));
        } else {
            $pdf = PDF::loadView('templates.annex_act_cert_n', compact('data'));
        }

        $pdf->setPaper('a4', 'portrait')->setWarnings(false);
        return $pdf->download('complect.pdf');
    }

    public function generateKapReference($request, $id)
    {
        $data = [];
        $auth = app(AuthService::class)->auth();

        $kap_request = KapRequest::find($id);
        $date = 'Дата: '. date('d.m.Y', $kap_request->created_at);
        $time = 'Время: '. date('H:i', $kap_request->created_at);

        $data['header'] = 'Запрос в КАП #'.$id;
        $data['author'] = 'Автор запроса (ИИН): '. $auth->login;
        $data['date'] = $date.'<br>'.$time;
        $data['description'] = $kap_request->base_on;

        $data_type = [
            "GRNZ" => "ГРНЗ",
            "STATUS_DATE" => "дата операции",
            "MODEL" => "марка, модель, модификация ТС",
            "ISSUE_YEAR" => "год выпуска ТС",
            "ENGINE_NO" => "номер двигателя ТС",
            "CHASSIS_NO" => "номер шасси ТС",
            "BODY_NO" => "номер кузова ТС (Vin-код)",
            "COLOR_NAME" => "цвет",
            "CATEGORY" => "категория ТС",
            "ENGINE_VOLUME" => "объем двигателя (куб. см)",
            "MAX_WEIGHT" => "разрешенная максимальная масса",
            "UNLOADED_WEIGHT" => "масса без нагрузки",
            "STATUS" => "статус карточки",
            "VIN" => "VIN ТС",
            "UNREG_REASON" => "причина снятия с учета ТС",
            "FIRSTNAME" => "Имя",
            "LASTNAME" => "Фамилия",
            "MIDNAME" => "Отчество",
            "IINBIN" => "ИИН/БИН"
        ];

        $xml_data = new SimpleXMLElement( $kap_request->xml_response );
        $xml = $xml_data->script->dataset->records;
        $record = $xml->record;
        $status = '';
        $k = 0;
        foreach( $record[0]->field as $field ){
            $field_name = $field->attributes();
            $field_name = $field_name[0];
            $k++;
            if( isset($data_type[strval($field_name)]) ){
                $status .=  '<span>'. $k .'. '. $data_type[strval($field_name)].': ';
                $status .= ' <b>'.$field.'</b></span><br>';
            }
        }
        $data['response'] = $status;

        $content_qr = 'id_'.$kap_request->id. '_status_'. $status .'_date_'.date('d.m.Y', $kap_request->created_at).'_user_'.$auth->idnum;
        $content_sign = md5($content_qr.md5(Config::get('APP_SALT')));
        $png = QrCode::format('png')->color(60, 60, 60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($content_sign);
        $png = base64_encode($png);
        $qr = "<img src='data:image/png;base64," . $png . "' width='115' height='115' style='margin: 0 1px;'>";

        $data['qr'] = $qr;

        $pdf = PDF::loadView('templates.kap_request', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);
        return $pdf->download('kap_request.pdf');
    }
}
