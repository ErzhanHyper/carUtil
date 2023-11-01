<?php


namespace App\Services\Exchange;


use App\Models\Certificate;
use App\Models\Exchange;
use App\Models\Log;
use App\Services\AuthService;

class ExchangeApproveService
{
    public function approve($id)
    {
        $auth = app(AuthService::class)->auth();

        $exchange = Exchange::select(['id', 'created', 'user_id', 'certificate_id', 'title', 'idnum', 'approve', 'approved', 'sended_to_approve', '__meta', 'hash'])->find($id);
        $cert = Certificate::find($exchange->certificate_id);

        $message = 'Нет доступа';
        $success = false;

        $can = false;

        if($cert->idnum_2 == '' && $cert->title_2 == '') {
            $can = true;
        }

        if($cert->idnum_3 == '' && $cert->title_3 == '') {
            $can = true;
        }

        if($cert->idnum_4 == '' && $cert->title_4 == '') {
            $can = true;
        }

        if($exchange->approve == 2) {
            $can = false;
            $message ='Это заявление уже было одобрено ранее.';
        }

        // проверка сертификата(погашен или переоформлен)
        if($cert->closed == 1){
            $can = false;
            $message = 'Перестаньте пытаться что-то сделать с сертификатом, который уже погашен!';
        }

        if($can) {
            $log = Log::create([
                'event' => 'approve',
                'object_type' => 'exchange',
                'object_id' => $exchange->id,
                'when' => time(),
                'object_before' => json_encode($exchange),
            ]);

            $cert->title_4 = $cert->title_3;
            $cert->idnum_4 = $cert->idnum_3;
            $cert->date_4 = $cert->date_3;
            $cert->title_3 = $cert->title_2;
            $cert->idnum_3 = $cert->idnum_2;
            $cert->date_3 = $cert->date_2;
            $cert->title_2 = $cert->title_1;
            $cert->idnum_2 = $cert->idnum_1;
            $cert->date_2 = $cert->date_1;
            $cert->title_1 = $exchange->title;
            $cert->idnum_1 = $exchange->idnum;
            $cert->date_1 = time();
            $cert->blocked = 0;

            $exchange->approve = 2;
            $exchange->user_id = $auth->id;
            $exchange->approved = time();
            $exchange->save();
            $cert->save();

            $success = true;
            $message ='Запрос одобрен!';

            $exchangeAfter = Exchange::select(['id', 'created', 'user_id', 'certificate_id', 'title', 'idnum', 'approve', 'approved', 'sended_to_approve', '__meta', 'hash'])->find($id);
            $log->object_after = json_encode($exchangeAfter);
            $log->save();
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function decline($id)
    {
        $auth = app(AuthService::class)->auth();

        $exchange = Exchange::select(['id', 'created', 'user_id', 'certificate_id', 'title', 'idnum', 'approve', 'approved', 'sended_to_approve', '__meta', 'hash'])->find($id);
        $cert = Certificate::find($exchange->certificate_id);

        $message = 'Нет доступа';
        $success = true;

        if($exchange->approve == 2) {
            $message = 'Это заявление уже было одобрено ранее и не может быть отклонено.';
            $success = false;
        }

        if($success) {
            $cert->blocked = 0;
            $cert->save();
            $exchange->approve = 3;
            $exchange->receiver_sign = '';
            $exchange->owner_sign = '';
            $exchange->save();

            $message = 'Запрос отклонен!';
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }
}
