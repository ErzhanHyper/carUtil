<?php


namespace App\Services\Sell;


use App\Services\AuthService;

class SellApproveService
{
    public function approve(){
        $auth = app(AuthService::class)->auth();

    }
}
