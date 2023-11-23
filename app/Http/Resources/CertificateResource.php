<?php

namespace App\Http\Resources;

use App\Models\Exchange;
use App\Services\Certificate\CalcCertificatePriceService;
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
            $certPrice = app(CalcCertificatePriceService::class)->run($this->id);
            $sum = $certPrice['sum'];
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

        $exchange = Exchange::where('certificate_id', $this->id)->orderByDesc('created')->first();

        $showExchange = true;

        if($this->blocked == 1){
            $showExchange = false;
        }

        if($this->closed == 1){
            $showExchange = false;
        }

        $exchange_status = '';
        if($exchange) {
            if ($this->blocked == 1 && $this->closed == 0) {
                if ($exchange->approve == 1) {
                    $exchange_status = 'Отправлена на одобрение';
                }else{
                    $showExchange = false;
                    $exchange_status = 'На переоформлении';
                }
            }else{
                if($this->blocked == 0 && ($exchange->approve == 3 || $exchange->approve == 2)){
                    $showExchange = true;
                }else{
                    $showExchange = false;
                    $exchange_status = 'На переоформлении';
                }
            }
        }

        return [
            'dateTill' => $dateTill,
            'id' => $this->id,
            'num' => str_pad($this->id, 9, 0, STR_PAD_LEFT),

            'car_id' => $this->car_id,

            'title_1' => $this->title_1,
            'idnum_1' => $this->idnum_1,
            'date_1' => date('Y-m-d', $this->date_1),

            'title_2' => $this->title_2,
            'idnum_2' => $this->idnum_2,
            'date_2' => $this->date_2 ? date('Y-m-d', $this->date_2) : '',

            'title_3' => $this->title_3,
            'idnum_3' => $this->idnum_3,
            'date_3' => $this->date_3 ? date('Y-m-d', $this->date_3) : '',

            'title_4' => $this->title_4,
            'idnum_4' => $this->idnum_4,
            'date_4' => $this->date_4 ? date('Y-m-d', $this->date_4) : '',

            'blocked' => $this->blocked,
            'status' => $status,
            'closed' => $this->closed,

            'sum' => $sum,
            'date' => date('d.m.Y', $this->date),

            'exchange' => $exchange,
            'showExchange' => $showExchange,
            'exchangeStatus' => $exchange_status
        ];
    }


}
