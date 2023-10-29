<?php


namespace App\Services\Certificate;


use App\Models\Car;
use App\Models\Certificate;

class CalcCertificatePriceService
{

    public function run($cert_id)
    {
        $cert = Certificate::find($cert_id);
        $car = Car::find($cert->car_id);
        $cat = $car->category->title;
        $car_type = $car->car_type;

        $price_increase_day = 1549854000;

        if($cert->date < $price_increase_day) {
            // старые цены
            if($cat == "M1") {
                $sum = '315 000';
                $sum_pro = 'триста пятнадцать тысяч';
            }
            if($cat == "M2" || $cat == "N1" || $cat == "N2") {
                $sum = '450 000';
                $sum_pro = 'четыреста пятьдесят тысяч';
            }
            if($cat == "N3" || $cat == "M3") {
                $sum = '650 000';
                $sum_pro = 'шестьсот пятьдесят тысяч';
            }
        } else {
            // цены после 11.02.2019
            if($cat == "M1") {
                $sum = '315 000';
                $sum_pro = 'триста пятнадцать тысяч';
            }
            if($cat == "M2" || $cat == "N1" || $cat == "N2") {
                $sum = '550 000';
                $sum_pro = 'пятьсот пятьдесят тысяч';
            }
            if($cat == "N3" || $cat == "M3") {
                $sum = '750 000';
                $sum_pro = 'семьсот пятьдесят тысяч';
            }
            if($cat == "tractor") {
                if ($car_type->id == 3){
                    $sum = '1 000 000';
                    $sum_pro = 'один миллион';
                } elseif ($car_type->id == 4) {
                    $sum = '560 000';
                    $sum_pro = 'пятьсот шестьдесят тысяч';
                }
            } elseif($cat == "combain"){
                if ($car_type->id == 3){
                    $sum = '2 000 000';
                    $sum_pro = 'два миллиона';
                } elseif ($car_type->id == 4) {
                    $sum = '1 500 000';
                    $sum_pro = 'один миллион пятьсот тысяч';
                }
            }
        }

        return [
            'sum' => $sum,
            'sum_pro' => $sum_pro
        ];
    }
}
