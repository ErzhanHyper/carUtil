<?php


namespace App\Services;


use App\Http\Resources\CertificateResource;
use App\Http\Resources\SellResource;
use App\Models\Certificate;
use App\Models\Exchange;
use App\Models\Sell;

class CheckupService
{
        public function checkCertById($id)
        {

            $show_result = false;
            $cert = null;
            $ex2 = null;
            $ex3 = null;
            $ex4 = null;
            $sell = null;

            $cert = Certificate::find($id);

            if($cert) {
                $show_result = true;
                // а где же инфа про переоформления?
                if ($cert->title_2) {
                    $ex2 = Exchange::where('certificate_id', $cert->id)->find_one();
                }

                // если не пуст третий слот
                if ($cert->title_3) {
                    $ex3 = Exchange::where('certificate_id', $cert->id)->offset(1)->find_one();
                }

                // если не пуст четвертый слот
                if ($cert->title_4) {
                    $ex4 = Exchange::where('certificate_id', $cert->id)->offset(2)->find_one();
                }
                $sell = Sell::where('cert_1', $cert->id)->orWhere('cert_2', $cert->id)->first();
            }

            if($cert){
                $cert = new CertificateResource($cert);
            }

            if($sell){
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
