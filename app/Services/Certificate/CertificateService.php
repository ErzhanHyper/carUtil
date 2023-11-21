<?php


namespace App\Services\Certificate;


use App\Http\Resources\CertificateResource;
use App\Models\Car;
use App\Models\Certificate;
use App\Models\Order;
use App\Services\AuthService;
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
                $cert->date_1 = time();
                $cert->car_id = $car->id;
                if($cert->save()){
                    $order->status = 3;
                    $order->save();
                }
            }
        }
    }

    public function generateCert($request, $id){

        $user = app(AuthService::class)->auth();

        $cert = Certificate::find($id);
        $car = Car::find($cert->car_id);

        $can = false;
        if($user->role === 'moderator' || $user->role === 'operator'){
            $can = true;
        }

        if($user->role === 'liner' && $user->idnum === $cert->idnum_1){
            $secure = app(EdsService::class)->secure($request->pem);
            if($secure->iin === $cert->idnum_1 || $secure->bin === $cert->idnum_1){
                $can = true;
            }else{
                throw new InvalidArgumentException(json_encode(['Нет доступа']));
            }
        }

        if($can) {
            $agro_categories = ['tractor','combain'];
            $cat = $car->category->title;
            $cat_title = $car->category->title_ru;

            $cert_title = $cert->title_1;
            $cert_idnum = $cert->idnum_1;
            $date = date('d.m.Y', $cert->date);
            $vin = $car->vin;
            $grnz = $car->grnz;
            $z_num = '№' . str_pad($cert->id, 9, 0, STR_PAD_LEFT);
            $z_date = 'Выдан: ' . date('d.m.Y', $cert->date);
            $new_date = 1705514400 - $cert->date;

            if ($cert->date >= 1610992800 && $cert->date <= 1642442400) {
                $z_to = 'Годен до: ' . date('d.m.Y', $cert->date + $new_date);
            } else {
                $z_to = 'Годен до: ' . date('d.m.Y', strtotime('+1 year', $cert->date));
            }

            $sum_data = app(CalcCertificatePriceService::class)->run($cert->id);
            $sum = $sum_data['sum'];
            $sum_pro = $sum_data['sum_pro'];

            $text_subtitle = 'Скидочные сертификаты выданные в период с 19 января 2021 года по 18 января 2022 года продлены до 18 января 2024 года на основании совместного приказа Министра экологии, геологии и природных ресурсов Республики Казахстан от 14 ноября 2022 г. № 697 и Министра индустрии и инфраструктурного развития Республики Казахстан от 15 ноября 2022 г. № 63.';

            if (in_array($cat, $agro_categories)) {
                $pre = <<<EOF
            Настоящий скидочный сертификат подтверждает, что $cert_title ИИН / БИН: $cert_idnum, сдал/а/о $date на утилизацию сельскохозяйственную технику $cat_title, идентификационный номер $vin ($grnz).
            EOF;
                $detail1 = <<<EOF
            В соответствии с Экологическим кодексом Республики Казахстан от 9 января 2007 года и Правилами стимулирования производства в Республике Казахстан экологически чистых автомобильных транспортных средств (соответствующих экологическому классу 4 и выше; с электродвигателями) и их компонентов, а также самоходной сельскохозяйственной техники, соответствующей экологическим требованиям, определенным техническими регламентами (далее - Правила), оператор расширенных обязательств производителей (импортеров) “ТОО «Оператор РОП»" (далее - Оператор) гарантирует возмещение скидки в размере $sum ($sum_pro) (прописью) тенге, предоставленной физическим и юридическим лицам на приобретение на территории Республики Казахстан, произведенных в Республике Казахстан экологически чистых автомобильных транспортных средств, соответствующих экологическому классу 4 и выше; с электродвигателями
            и (или) самоходной сельскохозяйственной техники, соответствующей экологическим требованиям, определенным техническими регламентами.Представление данного скидочного сертификата при покупке произведенного и реализуемого на территории Республики Казахстан экологически чистого автомобильного транспортного средства и (или) самоходной сельскохозяйственной техники дает право лицу, его предъявившему на скидку в размере, указанном в нем (далее - Скидка).
            EOF;
                $detail2 = <<<EOF
            <strong>Настоящий сертификат действует в течение 1 (одного) года с момента выдачи и может быть передан иным лицам не более трех раз с момента его выдачи. Передача сертификата иным лицам не изменяет срок его действия.</strong>
            EOF;
                $detail3 = <<<EOF
            Информация о выдаче, использовании и передаче иным лицам скидочного сертификата подлежит регистрации/перерегистрации у Оператора.<br /><br />
            Скидка предоставляется только на приобретение экологически чистых автомобильных транспортных средств, производители которых имеют заключенный с Оператором договор на финансирование Скидки. С перечнем производителей, на автомобили которых распространяется Скидка, можно ознакомиться на сайте Оператора auto.recycle.kz.
            EOF;
            } else {
                $pre = <<<EOF
            Настоящий скидочный сертификат подтверждает, что $cert_title ИИН / БИН: $cert_idnum, сдал/а/о $date на утилизацию автомобиль категории $cat_title, VIN-код $vin.
            EOF;
                $detail1 = <<<EOF
            В соответствии с Экологическим кодексом Республики Казахстан и Совместным приказом
            и.о. Министра энергетики Республики Казахстан от 4 декабря 2015 года № 697 и Министра по инвестициям и развитию Республики Казахстан от 23 декабря 2015 года № 1219 «Об утверждении Правил стимулирования производства в Республике Казахстан экологически чистых автомобильных транспортных средств (соответствующих экологическому классу 4 и выше; с электродвигателями) и их компонентов» (далее - Правила), оператор расширенных обязательств производителей (импортеров) ТОО «Оператор РОП» (далее - Оператор) гарантирует возмещение скидки в размере $sum ($sum_pro) тенге, предоставленной физическим и юридическим лицам на приобретение экологически чистых автомобильных транспортных средств на территории Республики Казахстан, произведенных в Республике Казахстан: соответствующих экологическому классу 4 и выше; с электродвигателями.
            Представление данного скидочного сертификата при покупке произведенного и реализуемого на территории Республики Казахстан экологически чистого автомобильного транспортного средства дает право лицу, его предъявившему на скидку в размере, указанном в нем (далее - Скидка).
            Скидка может быть суммирована не более чем по двум скидочным сертификатам на одно приобретаемое транспортное средство.
            EOF;
                $detail2 = <<<EOF
            <strong>Настоящий сертификат действует в течение 1 (одного) года с момента выдачи и может быть передан иным лицам не более трех раз с момента его выдачи. Передача сертификата иным лицам не изменяет срок его действия.</strong>
            EOF;
                $detail3 = <<<EOF
            Информация о выдаче, использовании и передаче иным лицам скидочного сертификата подлежит регистрации/перерегистрации у Оператора.<br /><br />
            Скидка предоставляется только на приобретение экологически чистых автомобильных транспортных средств и (или) самоходной сельскохозяйственной техники, производители которых имеют заключенный с Оператором договор на финансирование Скидки.
            EOF;
            }


            $qr1 = str_pad($cert->id, 9, 0, STR_PAD_LEFT) . '::' . $car->vin . '::' . $cat . '::' . date('d.m.Y', $cert->date) . '::' . $cert->title_1 . '::' . $cert->idnum_1;
            $qr1 = $qr1 . '::' . md5($qr1 . md5(config('APP_SALT')));
            $qr2 = '';
            $qr3 = '';
            $qr4 = '';

            if ($cert->title_2 && $cert->idnum_2) {
                $qr2 = $qr1;
                $qr1 = str_pad($cert->id, 9, 0, STR_PAD_LEFT) . '::' . $car->vin . '::' . $cat . '::' . date('d.m.Y', $cert->date) . '::' . $cert->title_2 . '::' . $cert->idnum_2;
                $qr1 = $qr1 . '::' . md5($qr1 . md5(config('APP_SALT')));
            }

            if ($cert->title_3 && $cert->idnum_3) {
                $qr3 = $qr2;
                $qr2 = $qr1;
                $qr1 = str_pad($cert->id, 9, 0, STR_PAD_LEFT) . '::' . $car->vin . '::' . $cat . '::' . date('d.m.Y', $cert->date) . '::' . $cert->title_3 . '::' . $cert->idnum_3;
                $qr1 = $qr1 . '::' . md5($qr1 . md5(config('APP_SALT')));
            }

            if ($cert->title_4 && $cert->idnum_4) {
                $qr4 = $qr3;
                $qr3 = $qr2;
                $qr2 = $qr1;
                $qr1 = str_pad($cert->id, 9, 0, STR_PAD_LEFT) . '::' . $car->vin . '::' . $cat . '::' . date('d.m.Y', $cert->date) . '::' . $cert->title_4 . '::' . $cert->idnum_4;
                $qr1 = $qr1 . '::' . md5($qr1 . md5(config('APP_SALT')));
            }

            $img = public_path('img') . '/10x10.png';
            $z_qr2 = '<img src=' . $img . ' width="130" height="130">';
            $z_qr3 = '<img src=' . $img . ' width="130" height="130">';
            $z_qr4 = '<img src=' . $img . ' width="130" height="130">';
            $z_qr1 = '<img src=' . $img . ' width="130" height="130">';
            $png = QrCode::format('png')->color(60, 60, 60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($qr1);
            $png = base64_encode($png);
            $z_qr1 = "<img src='data:image/png;base64," . $png . "' width='130' height='130'>";

//        if($qr2) {
//            $png = QrCode::format('png')->color(60,60,60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($qr2);
//            $png = base64_encode($png);
//            $z_qr2 = "<img src='data:image/png;base64," . $png . "' width='130' height='130'>";
//        }
//
//        if($qr3) {
//            $png = QrCode::format('png')->color(60,60,60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($qr3);
//            $png = base64_encode($png);
//            $z_qr3 = "<img src='data:image/png;base64," . $png . "' width='130' height='130'>";
//        }
//
//        if($qr4) {
//            $png = QrCode::format('png')->color(60,60,60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($qr4);
//            $png = base64_encode($png);
//            $z_qr4 = "<img src='data:image/png;base64," . $png . "' width='130' height='130'>";
//        }

            $data = [
                'z_num' => $z_num,
                'z_date' => $z_date,
                'z_to' => $z_to,

                'z_qr1' => $z_qr1,
                'z_qr2' => $z_qr2,
                'z_qr3' => $z_qr3,
                'z_qr4' => $z_qr4,

                'pre' => $pre,
                'detail1' => $detail1,
                'detail2' => $detail2,
                'detail3' => $detail3,
                'text_subtitle' => $text_subtitle
            ];

            $pdf = PDF::loadView('templates.certificate', compact('data'));
            $pdf->setPaper('a4', 'portrait')->setWarnings(false);

            return $pdf->download('certificate.pdf');
        }
    }
}
