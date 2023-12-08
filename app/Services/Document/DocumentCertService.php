<?php


namespace App\Services\Document;


use App\Models\Car;
use App\Models\Certificate;
use App\Services\AuthService;
use App\Services\Certificate\CalcCertificatePriceService;
use App\Services\EdsService;
use Barryvdh\DomPDF\Facade\Pdf;
use InvalidArgumentException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentCertService
{
    public function generateCert($request, $id){
        $cert = Certificate::find($id);
        $car = Car::find($cert->car_id);
        $user = app(AuthService::class)->auth();

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

        $text_subtitle = '';
        if (in_array($cat, $agro_categories)) {
            $pre = '';
            $detail1 = '';
            $detail2 = '';
            $detail3 = '';
        } else {
            $pre = '';
            $detail1 = '';
            $detail2 = '';
            $detail3 = '';
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

        if($qr2) {
            $png = QrCode::format('png')->color(60,60,60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($qr2);
            $png = base64_encode($png);
            $z_qr2 = "<img src='data:image/png;base64," . $png . "' width='130' height='130'>";
        }

        if($qr3) {
            $png = QrCode::format('png')->color(60,60,60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($qr3);
            $png = base64_encode($png);
            $z_qr3 = "<img src='data:image/png;base64," . $png . "' width='130' height='130'>";
        }

        if($qr4) {
            $png = QrCode::format('png')->color(60,60,60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($qr4);
            $png = base64_encode($png);
            $z_qr4 = "<img src='data:image/png;base64," . $png . "' width='130' height='130'>";
        }

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

        $can = false;
        if(($user->role === 'moderator' || $user->role === 'operator') && $cert){
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
            $pdf = PDF::loadView('templates.certificate', compact('data'));
            $pdf->setPaper('a4', 'portrait')->setWarnings(false);
            return $pdf->download('certificate.pdf');
        }
    }
}
