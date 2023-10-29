<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        $new_date = 1705514400 - $this->date;
        if ($this->date >= 1610992800 && $this->date <= 1642442400) {
            $dateTill = date('d.m.Y', $this->date + $new_date);
        } else {
            $dateTill = date('d.m.Y', strtotime('+1 year', $this->date)); // date + 1 year
        }

        $car_cat = 0;
        $car_type = 0;
        $sum = 0;
        $dateNotOvered = 0;
        $car = $this->car;
        if($car){
            $car_cat = $car->category->title;
            $car_type = $car->car_type_id;
            $sum = in_array($car->category_id, ['tractor','combain']) ? $this->__cat_agro_sum($car_cat, $car_type) : $this->__cat_sum($car_cat);
            $dateNotOvered = strtotime($dateTill) > time();
        }

        $status = '-';
        if($this->blocked){
            if($this->closed){
                $status = 'Заблокирован и погашен';
            }else{
                $status = 'Заблокирован';
            }
        }else{
            if($dateNotOvered){
                $status = 'Активный';
            }else{
                $status = 'Срок действия истек';
            }
        }

        return [
            'dateTill' => $dateTill,
            'id' => $this->id,
            'car_id' => $this->car_id,

            'title_1' => $this->title_1,
            'idnum_1' => $this->idnum_1,
            'date_1' => $this->date_1,

            'title_2' => $this->title_2,
            'idnum_2' => $this->idnum_2,
            'date_2' => $this->date_2,

            'title_3' => $this->title_3,
            'idnum_3' => $this->idnum_3,
            'date_3' => $this->date_3,

            'title_4' => $this->title_4,
            'idnum_4' => $this->idnum_4,
            'date_4' => $this->date_4,

            'blocked' => $this->blocked,
            'status' => $status,
            'closed' => $this->closed,

            'sum' => $sum,
            'date' => date('d.m.Y', $this->date)
        ];
    }

    public function __cat_agro_sum($cat, $car_type_id) {
        $sum = 0;

        if($cat == "tractor") {
            if ($car_type_id == 1){
                $sum = 1000000;
            } elseif ($car_type_id == 2) {
                $sum = 560000;
            }
        } elseif($cat == "combain"){
            if ($car_type_id == 1){
                $sum = 2000000;
            } elseif ($car_type_id == 2) {
                $sum = 1500000;
            }
        }

        return $sum;
    }


    public function __cat_sum($cat, $day = 0) {
        if($day != 0 && $day < 1549854000) {
            // старые цены
            if($cat == "M1") {
                return 315000;
            }
            if($cat == "M2" || $cat == "N1" || $cat == "N2") {
                return 450000;
            }
            if($cat == "N3" || $cat == "M3") {
                return 650000;
            }
        } else {
            // цены после 11.02.2019
            if($cat == "M1") {
                return 315000;
            }
            if($cat == "M2" || $cat == "N1" || $cat == "N2") {
                return 550000;
            }
            if($cat == "N3" || $cat == "M3") {
                return 750000;
            }
        }
    }


}
