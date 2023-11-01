<?php


namespace App\Services;


use App\Models\Car;
use App\Models\Exchange;
use App\Models\TransferDeal;
use App\Models\TransferOrder;

class SignService
{
    public function __signData($id){
        $car = Car::find($id);
        $hashed_string = 'id:'. $car->id .' vin:'. $car->vin .' grnz:'. $car->grnz.' chassis_no:'. $car->chassis_no;
        $hashed_string .= ' body_no:'. $car->body_no .' year:'. $car->year .' category_id:'. $car->category_id;
        $hashed_string .= ' car_type_id:'. $car->car_type_id .' order_id:'. $car->order_id .' proxy:'. $car->proxy;
        $hashed_string .= ' order_type:'. $car->order_type .' cert_title:'. $car->cert_title;
        $hashed_string .= ' cert_idnum:'. $car->cert_idnum .' owner_type:'. $car->owner_type;
        $zipped_string = gzencode($hashed_string);
        $zipped_string = base64_encode($zipped_string);

        $car->hash = $zipped_string;
        $car->save();

        return $zipped_string;
    }


    public function __signTransferData($id): string
    {
        $transfer = TransferOrder::find($id);
        $deal = TransferDeal::find($transfer->transfer_deal_id);
        $car = Car::where('order_id', $transfer->order_id)->first();

        $zipped_string = '';
        if($deal && $car) {
            $hashed_string = 'id:' . $transfer->id . ' vin:'. $car->vin .' grnz:'. $car->grnz. ' date:' . $deal->date . ' deal_id:' . $transfer->deal_id . ' amount:' . $deal->amount;
            $hashed_string .= ' owner_liner_id:' . $transfer->owner_liner_id . ' recipient_liner_id:' . $transfer->recipient_liner_id;
            $zipped_string = gzencode($hashed_string);
            $zipped_string = base64_encode($zipped_string);

            $transfer->hash = $zipped_string;
            $transfer->save();

        }
        return $zipped_string;
    }

    public function __signExchangeData($id){
        $exchange = Exchange::find($id);
        $hashed_string = 'id:'. $exchange->id .' title:'. $exchange->title .' idnum:'. $exchange->idnum.' certificate_id:'. $exchange->certificate_id;
        $hashed_string .= ' phone:'. $exchange->phone .' address:'. $exchange->cert_owner_address .' created:'. $exchange->created;
        $zipped_string = gzencode($hashed_string);
        $zipped_string = base64_encode($zipped_string);

        return $zipped_string;
    }

}
