<?php


namespace App\Services;


class EdsService
{
    public function secure($pem)
    {
        $b64 = base64_encode($pem);
        $result = [];
        exec('cd /opt/ksign && LD_LIBRARY_PATH="/opt/kalkancrypt:/opt/kalkancrypt/lib/engines" php secure.php ' . $b64, $result);
        $j = $result[0] === '' ? $result[1] : $result[0];
        $p = json_decode(base64_decode($j));

//        if (!strpos($p->validate_ocsp, 'OK') || !strpos($p->validate_ocsp, 'good')) {
//            throw new InvalidArgumentException(json_encode(['title' => ['Ваш сертификат отозван!']]));
//        }

        return $p;
    }

    public function check($request)
    {
        $hash = $request->hash;
        $sign = $request->sign;

        exec('cd /opt/ksign && LD_LIBRARY_PATH="/opt/kalkancrypt:/opt/kalkancrypt/lib/engines" php check.php ' . $hash . ' ' . $sign, $check);
        $j = json_decode(base64_decode($check[0]));

//        if($j->status == 'SUCCESS' && $j->iin) {
        return $j;
//        }
    }


    public function sign($request)
    {
        $hash = $request->hash;
        $check = [];
        exec('cd /opt/ksign && LD_LIBRARY_PATH="/opt/kalkancrypt:/opt/kalkancrypt/lib/engines" php sign.php ' . $hash . ' ', $check);

        if ($check) {
            $sign = '';
            $j = json_decode(base64_decode($check[0]));
            if ($j->sign != 'FAILED') {
                $sign = $j;
            }

            return $sign;
        }

    }

}
