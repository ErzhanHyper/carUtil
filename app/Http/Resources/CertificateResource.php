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

        $cert_title = '';
        $cert_idnum = '';
        $cert_date = '';

        if($this->idnum_4){
            $cert_title = $this->title_4;
            $cert_idnum = $this->idnum_4;
            $cert_date = $this->date_4;
        } else if($this->idnum_3){
            $cert_title = $this->title_3;
            $cert_idnum = $this->idnum_3;
            $cert_date = $this->date_3;
        } else if($this->idnum_2){
            $cert_title = $this->title_2;
            $cert_idnum = $this->idnum_2;
            $cert_date = $this->date_2;
        } else if($this->idnum_1){
            $cert_title = $this->title_1;
            $cert_idnum = $this->idnum_1;
            $cert_date = $this->date_1;
        }

        return [
            'id' => $this->id,
            'car_id' => $this->car_id,

            'cert_idnum' => $cert_idnum,
            'cert_title' => $cert_title,
            'cert_date' => $cert_date,

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
            'status' => $this->blocked ? 'Погашен' : 'Не погашен',
            'closed' => $this->closed,

            'date' => Carbon::parse($this->date)->format('Y-m-d')
        ];
    }
}
