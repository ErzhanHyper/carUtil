<?php


namespace App\Services\Document;


use App\Models\Car;
use App\Models\Client;
use App\Models\TransferDeal;
use App\Models\TransferOrder;
use App\Services\PriceConvertService;
use App\Services\AuthService;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentTransferService
{

    public function setData($id, $request): array
    {
        $data = [];
        $transfer = TransferOrder::find($id);
        $itemData = json_decode($transfer->data);
        $deal = TransferDeal::find($transfer->transfer_deal_id);
        $client = Client::find($transfer->client_id);
        $client2 = Client::find($deal->client_id);
        $car = Car::where('order_id', $transfer->order_id)->first();

        $data['model'] = $car->m_model ?? '______';
        $data['grnz'] = $car->grnz ?? '______';
        $data['year'] = $car->year ?? '______';
        $data['color'] = $car->color ?? '______';
        $data['engine_no'] = $car->engine_no ?? '______';
        $data['body_no'] = $car->body_no ?? '______';
        $data['chassis_no'] = $car->chassis_no ?? '______';
        $data['amount'] = $deal->amount ?? '______';
        $data['amount_text'] = app(PriceConvertService::class)->convertToText($deal->amount);

        $data['owner_address'] = $client->address ?? '______';
        $data['receiver_address'] = $client2->address ?? '______';
        $data['city'] = $client->region ? $client->region->title : '______';
        $data['date'] = date('d.m.Y');
        $data['owner_year'] = $client->year ?? '______';
        $data['owner_idnum'] = $client->idnum ?? '______';
        $data['owner_title'] = $client->title ?? '______';
        $data['owner_ud_num'] = $client->ud_num ?? '______';
        $data['owner_ud_expired'] = $client->ud_expired ?? '______';
        $data['owner_ud_issued'] = $client->ud_issued->title ?? '______';
        $data['receiver_year'] = $client2->year ?? '______';
        $data['receiver_title'] = $client2->title ?? '______';
        $data['receiver_idnum'] = $client2->idnum ?? '______';
        $data['receiver_ud_num'] = $client2->ud_num ?? '______';
        $data['receiver_ud_expired'] = $client2->ud_expired ?? '______';
        $data['receiver_ud_issued'] = $client2->ud_issued->title ?? '______';
        $data['recycle_type_title'] = in_array($car->car_type_id, [1,2]) ? 'вышедшего из эксплуатации транспортного средства' : 'вышедшего из эксплуатации самоходной сельскохозяйственной техники';
        $data['recycle_type_short_title'] = in_array($car->car_type_id, [1,2]) ? 'ВЭТС' : 'ВЭССХТ';

        $data['date'] = date('d.m.Y');

        $data['data1'] = $itemData->type ?? '';
        $data['data2'] = $itemData->data1 ?? '';
        $data['data3'] = $itemData->data2 ?? '';
        $data['data4'] = $itemData->data3 ?? '';
        $data['data5'] = $itemData->data4 ?? '';
        $data['data6'] = $itemData->data5 ?? '';
        $data['data7'] = $itemData->data6 ?? '';
        $data['data8'] = $itemData->data7 ?? '';
        $data['data9'] = $itemData->data8 ?? '';

        if(isset($request->type)) {
            $data['data1'] = $request->type;
        }
        if(isset($request->data1)) {
            $data['data2'] = $request->data1;
        }
        if(isset($request->data2)) {
            $data['data3'] = $request->data2;
        }
        if(isset($request->data3)) {
            $data['data4'] = $request->data3;
        }
        if(isset($request->data4)) {
            $data['data5'] = $request->data4;
        }
        if(isset($request->data5)) {
            $data['data6'] = $request->data5;
        }
        if(isset($request->data6)) {
            $data['data7'] = $request->data6;
        }
        if(isset($request->data7)) {
            $data['data8'] = $request->data7;
        }
        if(isset($request->data8)) {
            $data['data9'] = $request->data8;
        }

        return $data;
    }


    public function generateTransferContract($request, $id)
    {
        $data = $this->setData($id, $request);
        $transfer = TransferOrder::find($id);

        $data['owner_sign_qr'] = '<span> (____________________) <sub style="position: relative;top:8px;margin-left: -90px">Подпись</sub><br><br>_____________________________________ <sub style="position: relative;top:8px;margin-left: -170px">ФИО</sub></span>';
        $data['receiver_sign_qr'] = '<span> (____________________) <sub style="position: relative;top:8px;margin-left: -90px">Подпись</sub><br><br>____________________________________ <sub style="position: relative;top:8px;margin-left: -170px">ФИО</sub></span>';

        $data['eds'] = false;

        $_qr_edited1 = round((strlen($transfer->owner_sign) / 618) + 0.5);
        $qr_client1 = '';
        if($transfer->owner_sign) {
            for ($i = 0; $i < $_qr_edited1; $i++) {
                $ls = substr($transfer->owner_sign, $i * 618, 618);
                $png = QrCode::format('png')->color(60, 60, 60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($ls);
                $png = base64_encode($png);
                $qr_client1 .= "<img src='data:image/png;base64," . $png . "' width='123' height='123' style='margin: 0 1px;'>";
            }
            $data['owner_sign_qr'] = $qr_client1;
            $data['eds'] = true;
        }

        $_qr_edited2 = round((strlen($transfer->receiver_sign) / 618) + 0.5);
        $qr_client2 = '';
        if($transfer->receiver_sign) {
            for ($i = 0; $i < $_qr_edited2; $i++) {
                $ls = substr($transfer->receiver_sign, $i * 618, 618);
                $png = QrCode::format('png')->color(60, 60, 60)->backgroundColor(255, 255, 255)->size(165)->margin(1)->generate($ls);
                $png = base64_encode($png);
                $qr_client2 .= "<img src='data:image/png;base64," . $png . "' width='123' height='123' style='margin: 0 1px;'>";
            }
            $data['receiver_sign_qr'] = $qr_client2;
        }


        $pdf = PDF::loadView('templates.transfer_contract', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);
        return $pdf->stream('transfer_contract.pdf');
    }
}
