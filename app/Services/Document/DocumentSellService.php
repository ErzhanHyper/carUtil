<?php


namespace App\Services\Document;


use App\Models\Certificate;
use App\Models\Region;
use App\Models\Sell;
use App\Services\AuthService;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentSellService
{
    public function generateSellApplication($id){
        $data = [];

        $user = app(AuthService::class)->auth();
        $sell = Sell::find($id);
        $cert = Certificate::find($sell->cert_1);

        $data['cert_title'] = $cert->title_1;
        $data['cert_idnum'] = $cert->idnum_1;

        if($sell->approved > 0) {
            $current_date = date('d.m.Y', $sell->approved);
        } else {
            $current_date = date('d.m.Y');
        }

        if($sell->approve == 2) {
            $current_date = date('d.m.Y');
        }

        $cert_num = str_pad($cert->id, 9, 0, STR_PAD_LEFT);
        $cert_date = date('d.m.Y', $cert->date);

        if($sell->cert_2) {
            $cert2 = Certificate::find($sell->cert_2);
            $cert_num = $cert_num.' и №'.str_pad($cert2->id, 9, 0, STR_PAD_LEFT);
            $cert_date = $cert_date.' и '.date('d.m.Y', $cert2->date);
        }

        if($sell->cert_3) {
            $cert3 = Certificate::find($sell->cert_3);
            $cert_num = $cert_num.' и № '.str_pad($cert3->id, 9, 0, STR_PAD_LEFT);
            $cert_date = $cert_date.' и '.date('d.m.Y', $cert3->date);
        }

        if($sell->cert_4) {
            $cert4 = Certificate::find($sell->cert_4);
            $cert_num = $cert_num.' и № '.str_pad($cert4->id, 9, 0, STR_PAD_LEFT);
            $cert_date = $cert_date.' и '.date('d.m.Y', $cert4->date);
        }


        $region = Region::find($user->custom_3);
        $data['user_for_docs'] = $user->for_docs;
        $data['user_custom_3'] = $region ? $region->title : '';
        $data['current_date'] = $current_date;
        $data['user_title'] = $user->title;

        $data['cert_num'] = $cert_num;
        $data['cert_date'] = $cert_date;

        $pdf = PDF::loadView('templates.sell_app', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);
        return $pdf->stream('sell_app.pdf');
    }
}
